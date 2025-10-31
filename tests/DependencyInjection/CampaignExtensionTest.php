<?php

namespace CampaignBundle\Tests\DependencyInjection;

use CampaignBundle\DependencyInjection\CampaignExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CampaignExtension::class)]
final class CampaignExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testConstruct(): void
    {
        $extension = new CampaignExtension();
        $this->assertInstanceOf(CampaignExtension::class, $extension);
    }

    public function testAlias(): void
    {
        $extension = new CampaignExtension();
        $this->assertSame('campaign', $extension->getAlias());
    }
}
