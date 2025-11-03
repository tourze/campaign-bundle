<?php

declare(strict_types=1);

namespace CampaignBundle;

use CreditBundle\CreditBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use OrderCoreBundle\OrderCoreBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\CouponCoreBundle\CouponCoreBundle;
use Tourze\DoctrineResolveTargetEntityBundle\DoctrineResolveTargetEntityBundle;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;
use Tourze\EcolBundle\EcolBundle;
use Tourze\JsonRPCCacheBundle\JsonRPCCacheBundle;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\JsonRPCLogBundle\JsonRPCLogBundle;
use Tourze\JsonRPCPaginatorBundle\JsonRPCPaginatorBundle;
use Tourze\JsonRPCSecurityBundle\JsonRPCSecurityBundle;
use Tourze\ProductCoreBundle\ProductCoreBundle;
use Tourze\SpecialOrderBundle\SpecialOrderBundle;
use Tourze\Symfony\CronJob\CronJobBundle;
use Tourze\TextManageBundle\TextManageBundle;
use UserTagBundle\UserTagBundle;

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
            JsonRPCSecurityBundle::class => ['all' => true],
            TextManageBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
            EcolBundle::class => ['all' => true],
            OrderCoreBundle::class => ['all' => true],
            UserTagBundle::class => ['all' => true],
            CouponCoreBundle::class => ['all' => true],
            ProductCoreBundle::class => ['all' => true],
            SpecialOrderBundle::class => ['all' => true],
            CreditBundle::class => ['all' => true],
        ];
    }
}
