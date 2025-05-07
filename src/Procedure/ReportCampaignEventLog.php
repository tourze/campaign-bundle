<?php

namespace CampaignBundle\Procedure;

use CampaignBundle\Entity\EventLog;
use CampaignBundle\Event\UserEventReportEvent;
use CampaignBundle\Repository\AwardRepository;
use CampaignBundle\Repository\CampaignRepository;
use CampaignBundle\Repository\EventLogRepository;
use CampaignBundle\Service\CampaignService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Doctrine\ORM\EntityManagerInterface;

#[MethodTag('活动模块')]
#[MethodDoc('上报活动事件')]
#[MethodExpose('ReportCampaignEventLog')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Log]
class ReportCampaignEventLog extends LockableProcedure
{
    #[MethodParam('活动ID')]
    public string $campaignCode;

    #[MethodParam('事件')]
    public string $event;

    #[MethodParam('参数')]
    public array $params = [];

    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly EventLogRepository $logRepository,
        private readonly CampaignService $campaignService,
        private readonly AwardRepository $awardRepository,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(): array
    {
        $campaign = $this->campaignRepository->findOneBy([
            'code' => $this->campaignCode,
            'valid' => true,
        ]);
        if (!$campaign) {
            throw new ApiException('找不到活动信息');
        }

        $log = new EventLog();
        $log->setCampaign($campaign);
        $log->setUser($this->security->getUser());
        $log->setEvent($this->event);
        $log->setParams($this->params);
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        // 分发事件，方便我们去做其他逻辑
        $result = [
            'id' => $log->getId(),
            'rewards' => [],
        ];
        $event = new UserEventReportEvent();
        $event->setEvent($this->event);
        $event->setParams($this->params);
        $event->setLog($log);
        $event->setResult($result);
        $this->eventDispatcher->dispatch($event);
        $result = $event->getResult();

        if (!$event->isHook()) {
            // 查出所有的权益
            $awards = $this->awardRepository->findBy([
                'campaign' => $campaign,
                'event' => $this->event,
            ]);
            // 每个权益，有不同的限制条件，进行不同的检查
            foreach ($awards as $award) {
                foreach ($award->getLimits() as $limit) {
                    if (!$this->campaignService->checkLimit($this->security->getUser(), $limit)) {
                        throw new ApiException('不满足活动资格', -775);
                    }
                }
            }
            // 不同的限制条件，有不同的消耗规则，例如积分/机会次数
            foreach ($awards as $award) {
                foreach ($award->getLimits() as $limit) {
                    if (!$this->campaignService->consumeLimit($this->security->getUser(), $limit)) {
                        throw new ApiException('活动资格处理失败', -776);
                    }
                }
            }
            // 开始发放奖励了
            foreach ($awards as $award) {
                $reward = $this->campaignService->rewardUser($this->security->getUser(), $award);
                $result['rewards'][] = $reward->retrieveApiArray();
            }
        }

        return $result;
    }
}
