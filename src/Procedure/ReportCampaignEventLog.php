<?php

declare(strict_types=1);

namespace CampaignBundle\Procedure;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\EventLog;
use CampaignBundle\Entity\Limit;
use CampaignBundle\Event\UserEventReportEvent;
use CampaignBundle\Exception\AwardUnavailableException;
use CampaignBundle\Exception\InsufficientStockException;
use CampaignBundle\Exception\RewardLimitExceededException;
use CampaignBundle\Repository\AwardRepository;
use CampaignBundle\Repository\CampaignRepository;
use CampaignBundle\Service\CampaignService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '活动模块')]
#[MethodDoc(summary: '上报活动事件')]
#[MethodExpose(method: 'ReportCampaignEventLog')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class ReportCampaignEventLog extends LockableProcedure
{
    #[MethodParam(description: '活动ID')]
    public string $campaignCode;

    #[MethodParam(description: '事件')]
    public string $event;

    /**
     * @var array<string, mixed>
     */
    #[MethodParam(description: '参数')]
    public array $params = [];

    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly CampaignService $campaignService,
        private readonly AwardRepository $awardRepository,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /** @var array<string, mixed> */
    private array $result = [];

    /** @return array<string, mixed> */
    public function execute(): array
    {
        $campaign = $this->findValidCampaign();
        $log = $this->createEventLog($campaign);

        $this->result = $this->initializeResult($log);
        $event = $this->dispatchUserEvent($log, $this->result);

        if (!$event->isHook()) {
            $this->processAwards($campaign);
            $event->setResult($this->result);
        }

        return $event->getResult();
    }

    private function findValidCampaign(): Campaign
    {
        $campaign = $this->campaignRepository->findOneBy([
            'code' => $this->campaignCode,
            'valid' => true,
        ]);

        if (null === $campaign || !($campaign instanceof Campaign)) {
            throw new ApiException('找不到活动信息');
        }

        return $campaign;
    }

    private function createEventLog(Campaign $campaign): EventLog
    {
        $log = new EventLog();
        $log->setCampaign($campaign);
        $log->setUser($this->security->getUser());
        $log->setEvent($this->event);
        $log->setParams($this->params);
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $log;
    }

    /** @return array<string, mixed> */
    private function initializeResult(EventLog $log): array
    {
        return [
            'id' => $log->getId(),
            'rewards' => [],
        ];
    }

    /** @param array<string, mixed> $result */
    private function dispatchUserEvent(EventLog $log, array $result): UserEventReportEvent
    {
        $event = new UserEventReportEvent();
        $event->setEvent($this->event);
        $event->setParams($this->params);
        $event->setLog($log);
        $event->setResult($result);
        $this->eventDispatcher->dispatch($event);

        return $event;
    }

    private function processAwards(Campaign $campaign): void
    {
        $awards = $this->getValidAwards($campaign);
        $this->validateAwardLimits($awards);
        $this->consumeAwardLimits($awards);
        $this->distributeRewards($awards);
    }

    /** @return array<Award> */
    private function getValidAwards(Campaign $campaign): array
    {
        $awards = $this->awardRepository->findBy([
            'campaign' => $campaign,
            'event' => $this->event,
        ]);

        // 类型安全过滤和转换
        $filteredAwards = [];
        foreach ($awards as $award) {
            if ($award instanceof Award) {
                $filteredAwards[] = $award;
            }
        }

        return $filteredAwards;
    }

    /** @param array<Award> $awards */
    private function validateAwardLimits(array $awards): void
    {
        $user = $this->security->getUser();
        if (null === $user) {
            throw new ApiException('用户未登录', -401);
        }

        foreach ($awards as $award) {
            if (!($award instanceof Award)) {
                continue; // 跳过非Award实例
            }

            foreach ($award->getLimits() as $limit) {
                if (!($limit instanceof Limit)) {
                    continue; // 跳过非Limit实例
                }

                if (!$this->campaignService->checkLimit($user, $limit)) {
                    throw new ApiException('不满足活动资格', -775);
                }
            }
        }
    }

    /** @param array<Award> $awards */
    private function consumeAwardLimits(array $awards): void
    {
        $user = $this->security->getUser();
        if (null === $user) {
            throw new ApiException('用户未登录', -401);
        }

        foreach ($awards as $award) {
            if (!($award instanceof Award)) {
                continue; // 跳过非Award实例
            }

            foreach ($award->getLimits() as $limit) {
                if (!($limit instanceof Limit)) {
                    continue; // 跳过非Limit实例
                }

                if (!$this->campaignService->consumeLimit($user, $limit)) {
                    throw new ApiException('活动资格处理失败', -776);
                }
            }
        }
    }

    /** @param array<Award> $awards */
    private function distributeRewards(array $awards): void
    {
        $user = $this->security->getUser();
        if (null === $user) {
            throw new ApiException('用户未登录', -401);
        }
        // PHPStan已经推断出$user是UserInterface类型

        foreach ($awards as $award) {
            $this->processIndividualAward($user, $award);
        }
    }

    private function processIndividualAward(UserInterface $user, Award $award): void
    {
        try {
            $reward = $this->campaignService->rewardUser($user, $award);
            $apiArray = $reward->retrieveApiArray();
            if (!isset($this->result['rewards'])) {
                $this->result['rewards'] = [];
            }
            // 确保rewards是数组类型
            if (!is_array($this->result['rewards'])) {
                $this->result['rewards'] = [];
            }
            $this->result['rewards'][] = $apiArray;
        } catch (AwardUnavailableException $e) {
            throw new ApiException($e->getMessage(), 1007);
        } catch (RewardLimitExceededException $e) {
            throw new ApiException($e->getMessage(), 1006);
        } catch (InsufficientStockException $e) {
            throw new ApiException($e->getMessage(), 1003);
        }
    }
}
