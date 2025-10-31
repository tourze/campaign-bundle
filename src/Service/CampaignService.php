<?php

declare(strict_types=1);

namespace CampaignBundle\Service;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Limit;
use CampaignBundle\Entity\Reward;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;

/**
 * 活动相关操作服务。
 *
 * 主服务协调器，负责协调各个专门的服务类，提供统一的活动操作接口。
 * 遵循组合优于继承的原则，通过依赖注入整合各个服务。
 */
readonly class CampaignService
{
    public function __construct(
        private CampaignLimitService $limitService,
        private CampaignRewardService $rewardService,
        private CampaignRewardProcessorService $rewardProcessorService,
    ) {
    }

    /**
     * 检查用户是否满足给定限制的要求。
     *
     * @param UserInterface $user  the user to check
     * @param Limit         $limit the limit to check against
     *
     * @return bool true if the user meets the limit requirements
     */
    public function checkLimit(UserInterface $user, Limit $limit): bool
    {
        return $this->limitService->checkLimit($user, $limit);
    }

    /**
     * 消耗限制条件
     *
     * 不考虑并发：此方法操作的数据具有唯一性约束，数据库层面已有保护
     *
     * @param UserInterface $user  the user consuming the limit
     * @param Limit         $limit the limit to consume
     *
     * @return bool true if the limit was successfully consumed
     */
    public function consumeLimit(UserInterface $user, Limit $limit): bool
    {
        return $this->limitService->consumeLimit($user, $limit);
    }

    /**
     * 为用户颁发指定奖品。
     *
     * @param UserInterface $user  the user to reward
     * @param Award         $award the award to give
     *
     * @return Reward the created reward
     */
    #[Transactional]
    public function rewardUser(UserInterface $user, Award $award): Reward
    {
        $this->rewardService->validateAwardAvailability($award, $user);

        $reward = $this->rewardService->createBaseReward($user, $award);
        $this->rewardProcessorService->processRewardByType($user, $award, $reward);
        $this->rewardService->saveRewardAndUpdateAward($reward, $award);

        return $reward;
    }
}
