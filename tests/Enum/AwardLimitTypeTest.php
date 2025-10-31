<?php

namespace CampaignBundle\Tests\Enum;

use CampaignBundle\Enum\AwardLimitType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(AwardLimitType::class)]
final class AwardLimitTypeTest extends AbstractEnumTestCase
{
    public function testEnum(): void
    {
        $this->assertTrue(enum_exists(AwardLimitType::class));
        $this->assertCount(6, AwardLimitType::cases());
        $this->assertSame('buy-total', AwardLimitType::BUY_TOTAL->value);
        $this->assertSame('总次数限购', AwardLimitType::BUY_TOTAL->getLabel());
    }

    public function testToArray(): void
    {
        $result = AwardLimitType::BUY_TOTAL->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('buy-total', $result['value']);
        $this->assertSame('总次数限购', $result['label']);
    }
}
