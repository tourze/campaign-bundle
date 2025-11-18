<?php

declare(strict_types=1);

namespace CampaignBundle\Service;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Reward;
use CampaignBundle\Exception\UnsupportedRewardTypeException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 活动奖励类型处理服务
 *
 * 通过处理器注册表委托给具体的奖励处理器。
 * 采用策略模式，实现奖励类型的可扩展处理。
 *
 * 重构说明：
 * - v1.x：直接处理所有奖励类型，硬编码业务逻辑
 * - v2.x：使用 RewardProcessorRegistry 委托给独立的处理器
 *
 * @see \CampaignBundle\Service\RewardProcessorRegistry
 * @see \CampaignBundle\Contract\RewardProcessorInterface
 */
readonly class CampaignRewardProcessorService
{
    public function __construct(
        private RewardProcessorRegistry $registry,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * 根据奖励类型处理奖励
     *
     * 通过注册表查找对应的处理器并委托处理。
     * 如果没有找到处理器，抛出 UnsupportedRewardTypeException。
     *
     * @param UserInterface $user   接收奖励的用户
     * @param Award         $award  奖励配置
     * @param Reward        $reward 奖励记录
     *
     * @throws UnsupportedRewardTypeException 当没有处理器支持该奖励类型时
     */
    public function processRewardByType(UserInterface $user, Award $award, Reward $reward): void
    {
        $processor = $this->registry->getProcessor($award->getType());

        if (null === $processor) {
            $message = sprintf(
                'No processor registered for reward type: %s. '
                . 'Please install the corresponding extension package (e.g., campaign-coupon-bundle, campaign-credit-bundle, campaign-product-bundle).',
                $award->getType()->value
            );

            $this->logger->error($message, [
                'award_type' => $award->getType()->value,
                'award_id' => $award->getId(),
                'campaign_id' => $award->getCampaign()->getId(),
            ]);

            throw new UnsupportedRewardTypeException($message);
        }

        try {
            $processor->process($user, $award, $reward);

            $this->logger->info('Reward processed successfully', [
                'processor' => get_class($processor),
                'reward_type' => $award->getType()->value,
                'award_id' => $award->getId(),
                'campaign_id' => $award->getCampaign()->getId(),
                'user_id' => method_exists($user, 'getId') ? $user->getId() : 'unknown',
            ]);
        } catch (\Throwable $exception) {
            $this->logger->error('Reward processing failed', [
                'processor' => get_class($processor),
                'reward_type' => $award->getType()->value,
                'award_id' => $award->getId(),
                'campaign_id' => $award->getCampaign()->getId(),
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            // 重新抛出异常，让上层处理
            throw $exception;
        }
    }
}
