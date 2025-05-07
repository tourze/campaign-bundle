<?php

namespace CampaignBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '活动模块')]
class CampaignBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle::class => ['all' => true],
            \Tourze\Symfony\CronJob\CronJobBundle::class => ['all' => true],
            \AntdCpBundle\AntdCpBundle::class => ['all' => true],
        ];
    }
}
