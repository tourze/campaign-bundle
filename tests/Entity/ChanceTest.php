<?php

namespace CampaignBundle\Tests\Entity;

use CampaignBundle\Entity\Chance;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(Chance::class)]
final class ChanceTest extends AbstractEntityTestCase
{
    public function testConstruct(): void
    {
        $chance = new Chance();
        $this->assertInstanceOf(Chance::class, $chance);
    }

    protected function createEntity(): object
    {
        return new Chance();
    }

    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'valid' => ['valid', true];
        yield 'context' => ['context', ['key' => 'value']];
    }
}
