<?php

namespace CampaignBundle\Tests\Entity;

use CampaignBundle\Entity\Award;
use CampaignBundle\Enum\AwardType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(Award::class)]
final class AwardTest extends AbstractEntityTestCase
{
    public function testConstruct(): void
    {
        $award = new Award();
        $this->assertInstanceOf(Award::class, $award);
    }

    protected function createEntity(): object
    {
        return new Award();
    }

    /**
     * @return iterable<array{0: string, 1: mixed}>
     */
    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'event' => ['event', 'test-event'];
        yield 'type' => ['type', AwardType::COUPON];
        yield 'value' => ['value', 'test-value'];
    }
}
