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
use CampaignBundle\Param\ReportCampaignEventLogParam;
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
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
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

    /**
     * @phpstan-param ReportCampaignEventLogParam $param
     */
    public function execute(ReportCampaignEventLogParam|RpcParamInterface $param): ArrayResult
    {
        $campaign = $this->findValidCampaign($param);
        $log = $this->createEventLog($campaign, $param);

        $this->result = $this->initializeResult($log);
        $event = $this->dispatchUserEvent($log, $this->result, $param);

        if (!$event->isHook()) {
            $this->processAwards($campaign, $param);
            $event->setResult($this->result);
        }

        return $event->getResult();
    }

    private function findValidCampaign(ReportCampaignEventLogParam $param): Campaign
    {
        $campaign = $this->campaignRepository->findOneBy([
            'code' => $param->campaignCode,
            'valid' => true,
        ]);

        if (null === $campaign || !($campaign instanceof Campaign)) {
            throw new ApiException('找不到活动信息');
        }

        return new ArrayResult($campaign);
    }

    private function createEventLog(Campaign $campaign, ReportCampaignEventLogParam $param): EventLog
    {
        $log = new EventLog();
        $log->setCampaign($campaign);
        $log->setUser($this->security->getUser());
        $log->setEvent($param->event);
        $log->setParams($param->params);
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return new ArrayResult($log);
    }

    /** @return array<string, mixed> */
    private function initializeResult(EventLog $log): array
    {
        return new ArrayResult([
            'id' => $log->getId(),
            'rewards' => [],
        ]);
    }

    /** @param array<string, mixed> $result */
    private function dispatchUserEvent(EventLog $log, array $result, ReportCampaignEventLogParam $param): UserEventReportEvent
    {
        $event = new UserEventReportEvent();
        $event->setEvent($param->event);
        $event->setParams($param->params);
        $event->setLog($log);
        $event->setResult($result);
        $this->eventDispatcher->dispatch($event);

        return new ArrayResult($event);
    }

    private function processAwards(Campaign $campaign, ReportCampaignEventLogParam $param): void
    {
        $awards = $this->getValidAwards($campaign, $param);
        $this->validateAwardLimits($awards);
        $this->consumeAwardLimits($awards);
        $this->distributeRewards($awards);
    }

    /** @return array<Award> */
    private function getValidAwards(Campaign $campaign, ReportCampaignEventLogParam $param): array
    {
        $awards = $this->awardRepository->findBy([
            'campaign' => $campaign,
            'event' => $param->event,
        ]);

        // 类型安全过滤和转换
        $filteredAwards = [];
        foreach ($awards as $award) {
            if ($award instanceof Award) {
                $filteredAwards[] = $award;
            }
        }

        return new ArrayResult($filteredAwards);
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
