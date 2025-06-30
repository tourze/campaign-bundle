<?php

namespace CampaignBundle\Tests;

use CampaignBundle\CampaignBundle;
use PHPUnit\Framework\TestCase;
use Tourze\BundleDependency\BundleDependencyInterface;

class BundleTest extends TestCase
{
    public function testBundleExists(): void
    {
        $this->assertTrue(class_exists(CampaignBundle::class));
    }

    public function testBundleImplementsInterface(): void
    {
        $bundle = new CampaignBundle();
        $this->assertInstanceOf(BundleDependencyInterface::class, $bundle);
    }

    public function testGetBundleDependencies(): void
    {
        $dependencies = CampaignBundle::getBundleDependencies();
        $this->assertArrayHasKey(\Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\Symfony\CronJob\CronJobBundle::class, $dependencies);
    }
}