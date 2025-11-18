<?php

declare(strict_types=1);

namespace CampaignBundle\Exception;

/**
 * 不支持的奖励类型异常
 *
 * 当尝试处理的奖励类型没有对应的处理器时抛出此异常。
 *
 * 使用场景：
 * - 未安装对应的扩展 Bundle（如 campaign-coupon-bundle）
 * - 配置了不支持的奖励类型
 *
 * 解决方案：
 * - 安装对应的扩展 Bundle
 * - 或实现自定义的 RewardProcessorInterface
 */
class UnsupportedRewardTypeException extends \RuntimeException
{
}
