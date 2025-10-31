<?php

namespace CampaignBundle\Tests\Enum;

use CampaignBundle\Enum\LimitType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(LimitType::class)]
final class LimitTypeTest extends AbstractEnumTestCase
{
    public function testEnum(): void
    {
        $this->assertTrue(enum_exists(LimitType::class));
        $this->assertCount(2, LimitType::cases());
        $this->assertSame('user-tag', LimitType::USER_TAG->value);
        $this->assertSame('用户标签', LimitType::USER_TAG->getLabel());
    }

    public function testToArray(): void
    {
        $result = LimitType::USER_TAG->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('user-tag', $result['value']);
        $this->assertSame('用户标签', $result['label']);
    }
}
