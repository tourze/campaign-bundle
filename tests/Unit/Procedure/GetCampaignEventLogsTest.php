<?php

namespace CampaignBundle\Tests\Unit\Procedure;

use CampaignBundle\Procedure\GetCampaignEventLogs;
use CampaignBundle\Tests\BaseTestCase;

class GetCampaignEventLogsTest extends BaseTestCase
{
    public function testProcedureClass(): void
    {
        $this->assertTrue(class_exists(GetCampaignEventLogs::class));
    }
}