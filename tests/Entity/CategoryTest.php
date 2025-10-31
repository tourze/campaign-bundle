<?php

namespace CampaignBundle\Tests\Entity;

use CampaignBundle\Entity\Category;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(Category::class)]
final class CategoryTest extends AbstractEntityTestCase
{
    public function testConstruct(): void
    {
        $category = new Category();
        $this->assertInstanceOf(Category::class, $category);
    }

    protected function createEntity(): object
    {
        return new Category();
    }

    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'title' => ['title', 'test-category'];
        yield 'valid' => ['valid', true];
    }

    public function testToString(): void
    {
        $category = new Category();
        $category->setTitle('test-category');
        $this->assertEquals('test-category', $category->getTitle());
    }
}
