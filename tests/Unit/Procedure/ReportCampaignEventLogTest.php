<?php

namespace CampaignBundle\Tests\Unit\Procedure;

use CampaignBundle\Procedure\ReportCampaignEventLog;
use CampaignBundle\Tests\BaseTestCase;

class ReportCampaignEventLogTest extends BaseTestCase
{
    public function testProcedureClass(): void
    {
        $this->assertTrue(class_exists(ReportCampaignEventLog::class));
    }
}