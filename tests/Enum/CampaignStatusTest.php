<?php

namespace CampaignBundle\Tests\Enum;

use CampaignBundle\Enum\CampaignStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CampaignStatus::class)]
final class CampaignStatusTest extends AbstractEnumTestCase
{
    public function testEnum(): void
    {
        $this->assertTrue(enum_exists(CampaignStatus::class));
        $this->assertCount(3, CampaignStatus::cases());
        $this->assertSame('pending', CampaignStatus::PENDING->value);
        $this->assertSame('未开始', CampaignStatus::PENDING->getLabel());
    }

    public function testToArray(): void
    {
        $result = CampaignStatus::PENDING->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('pending', $result['value']);
        $this->assertSame('未开始', $result['label']);
    }
}
