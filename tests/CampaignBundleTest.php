<?php

declare(strict_types=1);

namespace CampaignBundle\Tests;

use CampaignBundle\CampaignBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CampaignBundle::class)]
#[RunTestsInSeparateProcesses]
final class CampaignBundleTest extends AbstractBundleTestCase
{
}
