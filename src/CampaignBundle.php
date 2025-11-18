<?php

declare(strict_types=1);

namespace CampaignBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineResolveTargetEntityBundle\DoctrineResolveTargetEntityBundle;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;
use Tourze\EcolBundle\EcolBundle;
use Tourze\JsonRPCCacheBundle\JsonRPCCacheBundle;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\JsonRPCLogBundle\JsonRPCLogBundle;
use Tourze\JsonRPCPaginatorBundle\JsonRPCPaginatorBundle;
use Tourze\Symfony\CronJob\CronJobBundle;
use Tourze\TextManageBundle\TextManageBundle;

/**
 * Campaign Bundle - 核心活动管理 Bundle
 *
 * 提供活动管理的核心功能，包括：
 * - 活动生命周期管理
 * - 奖励配置和发放（通过扩展 Bundle）
 * - 参与机会管理
 * - 限制条件控制
 * - 事件日志记录
 *
 * 重构说明（v2.0）：
 * 移除了所有业务 Bundle 的硬依赖，采用插件化架构。
 * 奖励类型处理通过独立的扩展 Bundle 提供：
 * - tourze/campaign-coupon-bundle：优惠券奖励
 * - tourze/campaign-credit-bundle：积分奖励
 * - tourze/campaign-product-bundle：商品资格奖励
 *
 * @see https://github.com/tourze/php-monorepo/packages/campaign-bundle/REFACTORING.md
 */
class CampaignBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            DoctrineResolveTargetEntityBundle::class => ['all' => true],
            DoctrineSnowflakeBundle::class => ['all' => true],
            CronJobBundle::class => ['all' => true],
            SecurityBundle::class => ['all' => true],
            JsonRPCCacheBundle::class => ['all' => true],
            JsonRPCLockBundle::class => ['all' => true],
            JsonRPCLogBundle::class => ['all' => true],
            JsonRPCPaginatorBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
            TextManageBundle::class => ['all' => true],
            EcolBundle::class => ['all' => true],
        ];
    }
}
