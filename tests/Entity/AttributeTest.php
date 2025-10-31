<?php

namespace CampaignBundle\Tests\Entity;

use CampaignBundle\Entity\Attribute;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(Attribute::class)]
final class AttributeTest extends AbstractEntityTestCase
{
    public function testConstruct(): void
    {
        $attribute = new Attribute();
        $this->assertInstanceOf(Attribute::class, $attribute);
    }

    protected function createEntity(): object
    {
        return new Attribute();
    }

    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'name' => ['name', 'test-name'];
        yield 'value' => ['value', 'test-value'];
    }
}
