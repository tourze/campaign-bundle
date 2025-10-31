<?php

declare(strict_types=1);

namespace CampaignBundle\Service;

use CampaignBundle\Entity\Limit;
use CampaignBundle\Enum\LimitType;
use CampaignBundle\Repository\ChanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserTagContracts\TagLoaderInterface;

/**
 * 活动限制操作服务。
 *
 * 专门处理活动限制相关的业务逻辑，包括限制检查和限制消耗。
 * 遵循单一职责原则，只负责限制相关的操作。
 */
readonly class CampaignLimitService
{
    public function __construct(
        private ChanceRepository $chanceRepository,
        private EntityManagerInterface $entityManager,
        private TagLoaderInterface $userTagService,
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
        // 看这个人是不是有可以使用的机会
        if (LimitType::CHANCE === $limit->getType()) {
            $award = $limit->getAward();
            if (null === $award) {
                return false;
            }

            $chance = $this->chanceRepository->findOneBy([
                'campaign' => $award->getCampaign(),
                'user' => $user,
                'valid' => true,
            ]);
            if (null === $chance) {
                return false;
            }
        }

        // 检查标签情况
        if (LimitType::USER_TAG === $limit->getType()) {
            $userTagNames = [];
            foreach ($this->userTagService->loadTagsByUser($user) as $tag) {
                $userTagNames[] = $tag->getName();
            }
            if (false === in_array($limit->getValue(), $userTagNames, true)) {
                return false;
            }
        }

        return true;
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
        // 消耗一个机会
        if (LimitType::CHANCE === $limit->getType()) {
            $award = $limit->getAward();
            if (null === $award) {
                return false;
            }

            $chance = $this->chanceRepository->findOneBy([
                'campaign' => $award->getCampaign(),
                'user' => $user,
                'valid' => true,
            ]);
            if (null === $chance) {
                return false;
            }

            // 确保 $chance 是正确的类型，添加方法检查
            if (!method_exists($chance, 'setUseTime') || !method_exists($chance, 'setValid')) {
                return false;
            }

            $chance->setUseTime(new \DateTimeImmutable());
            $chance->setValid(false);
            $this->entityManager->persist($chance);
            $this->entityManager->flush();
        }

        // 标签不用处理

        return true;
    }
}
