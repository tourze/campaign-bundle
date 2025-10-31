<?php

declare(strict_types=1);

namespace CampaignBundle\Service;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardLimitType;
use CampaignBundle\Exception\AwardUnavailableException;
use CampaignBundle\Exception\RewardLimitExceededException;
use CampaignBundle\Repository\RewardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 活动奖励操作服务。
 *
 * 专门处理活动奖励相关的业务逻辑，包括奖励验证、创建和限制检查。
 * 遵循单一职责原则，只负责奖励相关的核心操作。
 */
readonly class CampaignRewardService
{
    public function __construct(
        private RewardRepository $rewardRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 验证奖品可用性
     *
     * 不考虑并发：数量检查是先查后判断，实际消费在事务中处理
     *
     * @param Award         $award the award to validate
     * @param UserInterface $user  the user requesting the award
     */
    public function validateAwardAvailability(Award $award, UserInterface $user): void
    {
        if ($award->getPrizeQuantity() <= 0) {
            throw new AwardUnavailableException('奖品已领取完毕');
        }

        if ($award->getTimes() > 0) {
            $this->checkUserRewardLimit($award, $user);
        }
    }

    /**
     * 检查用户是否超出该奖品的奖励限制。
     *
     * @param Award         $award the award to check
     * @param UserInterface $user  the user to check
     */
    private function checkUserRewardLimit(Award $award, UserInterface $user): void
    {
        $rewardCount = $this->getUserRewardCount($award, $user);

        if ($rewardCount >= $award->getTimes()) {
            $message = $this->getRewardLimitMessage($award);
            throw new RewardLimitExceededException($message);
        }
    }

    /**
     * 获取用户对特定奖品的奖励次数。
     *
     * @param Award         $award the award to check
     * @param UserInterface $user  the user to check
     *
     * @return int the number of rewards
     */
    private function getUserRewardCount(Award $award, UserInterface $user): int
    {
        $qb = $this->rewardRepository->createQueryBuilder('r')
            ->select('count(r)')
            ->where('r.award = :award and r.user = :user')
            ->setParameter('award', $award)
            ->setParameter('user', $user)
        ;

        $limitType = $award->getAwardLimitType();
        if (null !== $limitType) {
            $this->addTimeRangeToQuery($qb, $limitType);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * 根据限制类型向查询构建器添加时间范围约束。
     *
     * @param QueryBuilder   $qb        the query builder
     * @param AwardLimitType $limitType the award limit type
     */
    private function addTimeRangeToQuery(QueryBuilder $qb, AwardLimitType $limitType): void
    {
        if (AwardLimitType::BUY_TOTAL === $limitType) {
            return;
        }

        $now = new \DateTimeImmutable();

        match ($limitType) {
            AwardLimitType::BUY_DAILY => $this->addTimeRange(
                $qb,
                $now->setTime(0, 0, 0),
                $now->setTime(23, 59, 59)
            ),
            AwardLimitType::BUY_WEEK => $this->addTimeRange(
                $qb,
                $now->modify('monday this week')->setTime(0, 0, 0),
                $now->modify('sunday this week')->setTime(23, 59, 59)
            ),
            AwardLimitType::BUY_MONTH => $this->addTimeRange(
                $qb,
                $now->modify('first day of this month')->setTime(0, 0, 0),
                $now->modify('last day of this month')->setTime(23, 59, 59)
            ),
            AwardLimitType::BUY_QUARTER => $this->addTimeRange(
                $qb,
                $this->getQuarterStart($now),
                $this->getQuarterEnd($now)
            ),
            AwardLimitType::BUY_YEAR => $this->addTimeRange(
                $qb,
                $now->setDate((int) $now->format('Y'), 1, 1)->setTime(0, 0, 0),
                $now->setDate((int) $now->format('Y'), 12, 31)->setTime(23, 59, 59)
            ),
        };
    }

    /**
     * 向查询构建器添加时间范围约束。
     *
     * @param QueryBuilder       $qb    the query builder
     * @param \DateTimeInterface $start the start time
     * @param \DateTimeInterface $end   the end time
     */
    private function addTimeRange(QueryBuilder $qb, \DateTimeInterface $start, \DateTimeInterface $end): void
    {
        $qb->andWhere('r.createTime between :start and :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
        ;
    }

    /**
     * 获取给定日期所在季度的开始时间。
     */
    private function getQuarterStart(\DateTimeImmutable $date): \DateTimeImmutable
    {
        $month = (int) $date->format('n');
        $quarterStartMonth = (intval(($month - 1) / 3) * 3) + 1;

        return $date->setDate((int) $date->format('Y'), $quarterStartMonth, 1)->setTime(0, 0, 0);
    }

    /**
     * 获取给定日期所在季度的结束时间。
     */
    private function getQuarterEnd(\DateTimeImmutable $date): \DateTimeImmutable
    {
        $month = (int) $date->format('n');
        $quarterStartMonth = (intval(($month - 1) / 3) * 3) + 1;
        $quarterEndMonth = $quarterStartMonth + 2;
        $lastDay = $date->setDate((int) $date->format('Y'), $quarterEndMonth, 1)->format('t');

        return $date->setDate((int) $date->format('Y'), $quarterEndMonth, (int) $lastDay)->setTime(23, 59, 59);
    }

    /**
     * 获取奖品的奖励限制消息。
     *
     * @param Award $award the award
     *
     * @return string the limit message
     */
    private function getRewardLimitMessage(Award $award): string
    {
        return match ($award->getAwardLimitType()) {
            AwardLimitType::BUY_DAILY => "每人每日只能领取{$award->getTimes()}次",
            AwardLimitType::BUY_WEEK => "每人每周只能领取{$award->getTimes()}次",
            AwardLimitType::BUY_MONTH => "每人每月只能领取{$award->getTimes()}次",
            AwardLimitType::BUY_QUARTER => "每人每季度只能领取{$award->getTimes()}次",
            AwardLimitType::BUY_YEAR => "每人每年只能领取{$award->getTimes()}次",
            default => '已达到领取限制',
        };
    }

    /**
     * 为用户和奖品创建基础奖励实体。
     *
     * @param UserInterface $user  the user receiving the reward
     * @param Award         $award the award being given
     *
     * @return Reward the created reward
     */
    public function createBaseReward(UserInterface $user, Award $award): Reward
    {
        $reward = new Reward();
        $reward->setCampaign($award->getCampaign());
        $reward->setAward($award);
        $reward->setType($award->getType());
        $reward->setValue($award->getValue());
        $reward->setUser($user);
        $reward->setValid(true);
        $reward->setSn(uniqid());

        return $reward;
    }

    /**
     * 保存奖励并更新奖品数量
     *
     * 不考虑并发：在事务内，由数据库保证原子性
     *
     * @param Reward $reward the reward to save
     * @param Award  $award  the award to update
     */
    public function saveRewardAndUpdateAward(Reward $reward, Award $award): void
    {
        $this->entityManager->persist($reward);
        $currentQuantity = $award->getPrizeQuantity();
        if (null !== $currentQuantity && $currentQuantity > 0) {
            $award->setPrizeQuantity($currentQuantity - 1);
        }
        $this->entityManager->persist($award);
        $this->entityManager->flush();
    }
}
