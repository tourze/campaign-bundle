<?php

namespace CampaignBundle\Tests\Unit\Event;

use CampaignBundle\Event\UserEventReportEvent;
use CampaignBundle\Tests\BaseTestCase;

class UserEventReportEventTest extends BaseTestCase
{
    public function testEventClass(): void
    {
        $this->assertTrue(class_exists(UserEventReportEvent::class));
    }
}