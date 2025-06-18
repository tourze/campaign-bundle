<?php

namespace CampaignBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class CampaignBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle::class => ['all' => true],
            \Tourze\Symfony\CronJob\CronJobBundle::class => ['all' => true],
        ];
    }
}
