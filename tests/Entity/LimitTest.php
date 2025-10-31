<?php

namespace CampaignBundle\Tests\Entity;

use CampaignBundle\Entity\Limit;
use CampaignBundle\Enum\LimitType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(Limit::class)]
final class LimitTest extends AbstractEntityTestCase
{
    public function testConstruct(): void
    {
        $limit = new Limit();
        $this->assertInstanceOf(Limit::class, $limit);
    }

    protected function createEntity(): object
    {
        return new Limit();
    }

    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'type' => ['type', LimitType::CHANCE];
        yield 'value' => ['value', 'test-value'];
    }
}
