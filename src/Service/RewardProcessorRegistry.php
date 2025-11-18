<?php

declare(strict_types=1);

namespace CampaignBundle\Service;

use CampaignBundle\Contract\RewardProcessorInterface;
use CampaignBundle\Enum\AwardType;

/**
 * 奖励处理器注册表
 *
 * 管理所有已注册的奖励处理器，并提供查找功能。
 * 通过 Symfony 的 Tagged Iterator 自动注入所有实现了 RewardProcessorInterface 的服务。
 *
 * 使用示例：
 * ```php
 * $processor = $registry->getProcessor(AwardType::COUPON);
 * if (null !== $processor) {
 *     $processor->process($user, $award, $reward);
 * }
 * ```
 *
 * @see \CampaignBundle\Contract\RewardProcessorInterface
 */
readonly class RewardProcessorRegistry
{
    /**
     * @var list<RewardProcessorInterface> 已注册的处理器列表
     */
    private array $processors;

    /**
     * @param iterable<RewardProcessorInterface> $processors 处理器迭代器（由 Symfony DI 注入）
     */
    public function __construct(iterable $processors)
    {
        // 将迭代器转换为数组以便多次遍历
        $this->processors = $processors instanceof \Traversable
            ? iterator_to_array($processors)
            : (array) $processors;
    }

    /**
     * 获取支持指定奖励类型的处理器
     *
     * 当多个处理器都支持同一类型时，返回优先级最高的处理器。
     *
     * @param AwardType $type 奖励类型
     *
     * @return RewardProcessorInterface|null 找到的处理器，如果没有找到返回 null
     */
    public function getProcessor(AwardType $type): ?RewardProcessorInterface
    {
        $matched = [];

        foreach ($this->processors as $processor) {
            if ($processor->supports($type)) {
                $matched[] = $processor;
            }
        }

        if (empty($matched)) {
            return null;
        }

        // 如果只有一个匹配，直接返回
        if (1 === count($matched)) {
            return $matched[0];
        }

        // 按优先级降序排序（优先级高的在前）
        usort($matched, fn (RewardProcessorInterface $a, RewardProcessorInterface $b) => $b->getPriority() <=> $a->getPriority());

        return $matched[0];
    }

    /**
     * 检查是否有处理器支持指定类型
     *
     * @param AwardType $type 奖励类型
     *
     * @return bool 是否有支持的处理器
     */
    public function hasProcessor(AwardType $type): bool
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取所有已注册的处理器
     *
     * @return list<RewardProcessorInterface> 处理器列表
     */
    public function getAllProcessors(): array
    {
        return $this->processors;
    }

    /**
     * 获取支持指定类型的所有处理器（按优先级排序）
     *
     * @param AwardType $type 奖励类型
     *
     * @return list<RewardProcessorInterface> 处理器列表
     */
    public function getProcessors(AwardType $type): array
    {
        $matched = [];

        foreach ($this->processors as $processor) {
            if ($processor->supports($type)) {
                $matched[] = $processor;
            }
        }

        // 按优先级降序排序
        usort($matched, fn (RewardProcessorInterface $a, RewardProcessorInterface $b) => $b->getPriority() <=> $a->getPriority());

        return $matched;
    }
}
