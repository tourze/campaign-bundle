<?php

namespace CampaignBundle\Tests\Event;

use CampaignBundle\Entity\EventLog;
use CampaignBundle\Event\UserEventReportEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(UserEventReportEvent::class)]
final class UserEventReportEventTest extends AbstractEventTestCase
{
    public function testEventClass(): void
    {
        $event = new UserEventReportEvent();
        $this->assertInstanceOf(UserEventReportEvent::class, $event);
    }

    public function testGettersAndSetters(): void
    {
        $event = new UserEventReportEvent();

        // Test result
        $result = ['key' => 'value'];
        $event->setResult($result);
        $this->assertSame($result, $event->getResult());

        // Test event name
        $eventName = 'test_event';
        $event->setEvent($eventName);
        $this->assertSame($eventName, $event->getEvent());

        // Test params
        $params = ['param1' => 'value1'];
        $event->setParams($params);
        $this->assertSame($params, $event->getParams());

        // Test hook flag
        $event->setHook(true);
        $this->assertTrue($event->isHook());

        $event->setHook(false);
        $this->assertFalse($event->isHook());

        // Test event log
        $eventLog = new EventLog();
        $event->setLog($eventLog);
        $this->assertSame($eventLog, $event->getLog());
    }
}
