<?php

namespace CampaignBundle\Tests\Enum;

use CampaignBundle\Enum\AwardType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(AwardType::class)]
final class AwardTypeTest extends AbstractEnumTestCase
{
    public function testEnum(): void
    {
        $this->assertTrue(enum_exists(AwardType::class));
        $this->assertCount(5, AwardType::cases());
        $this->assertSame('coupon', AwardType::COUPON->value);
        $this->assertSame('优惠券', AwardType::COUPON->getLabel());
    }

    public function testToArray(): void
    {
        $result = AwardType::COUPON->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('coupon', $result['value']);
        $this->assertSame('优惠券', $result['label']);
    }
}
