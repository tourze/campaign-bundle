<?php

namespace CampaignBundle\Tests\Entity;

use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(Reward::class)]
final class RewardTest extends AbstractEntityTestCase
{
    public function testConstruct(): void
    {
        $reward = new Reward();
        $this->assertInstanceOf(Reward::class, $reward);
    }

    protected function createEntity(): object
    {
        return new Reward();
    }

    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'valid' => ['valid', true];
        yield 'type' => ['type', AwardType::COUPON];
        yield 'value' => ['value', 'test-value'];
    }
}
