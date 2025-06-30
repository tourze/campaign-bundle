<?php

namespace CampaignBundle\Tests\Unit\Entity;

use CampaignBundle\Entity\EventLog;
use CampaignBundle\Tests\BaseTestCase;

class EventLogTest extends BaseTestCase
{
    public function testConstruct(): void
    {
        $eventLog = new EventLog();
        $this->assertInstanceOf(EventLog::class, $eventLog);
    }

    public function testSettersAndGetters(): void
    {
        $eventLog = new EventLog();
        
        $eventLog->setEvent('test-event');
        $this->assertEquals('test-event', $eventLog->getEvent());
        
        $eventLog->setParams(['param1' => 'value1']);
        $this->assertEquals(['param1' => 'value1'], $eventLog->getParams());
    }

    public function testToString(): void
    {
        $eventLog = new EventLog();
        $eventLog->setEvent('test-event');
        $this->assertEquals('test-event', (string) $eventLog);
    }
}