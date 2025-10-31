<?php

namespace CampaignBundle\Tests\Entity;

use CampaignBundle\Entity\Campaign;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(Campaign::class)]
final class CampaignTest extends AbstractEntityTestCase
{
    public function testConstruct(): void
    {
        $campaign = new Campaign();
        $this->assertInstanceOf(Campaign::class, $campaign);
    }

    protected function createEntity(): object
    {
        return new Campaign();
    }

    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'name' => ['name', 'test-campaign'];
        yield 'code' => ['code', 'test-code'];
        yield 'valid' => ['valid', true];
    }
}
