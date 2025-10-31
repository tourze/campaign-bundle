<?php

namespace CampaignBundle\Tests\Entity;

use CampaignBundle\Entity\EventLog;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(EventLog::class)]
final class EventLogTest extends AbstractEntityTestCase
{
    public function testConstruct(): void
    {
        $eventLog = new EventLog();
        $this->assertInstanceOf(EventLog::class, $eventLog);
    }

    protected function createEntity(): object
    {
        return new EventLog();
    }

    /**
     * @return \Generator<string, array{string, mixed}>
     */
    public static function propertiesProvider(): \Generator
    {
        yield 'event' => ['event', 'test-event'];
        yield 'params' => ['params', ['param1' => 'value1']];
    }

    public function testToString(): void
    {
        $eventLog = new EventLog();
        $eventLog->setEvent('test-event');
        $this->assertEquals('test-event', (string) $eventLog);
    }
}
