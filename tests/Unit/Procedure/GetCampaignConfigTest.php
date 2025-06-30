<?php

namespace CampaignBundle\Tests\Unit\Procedure;

use CampaignBundle\Procedure\GetCampaignConfig;
use CampaignBundle\Tests\BaseTestCase;

class GetCampaignConfigTest extends BaseTestCase
{
    public function testProcedureClass(): void
    {
        $this->assertTrue(class_exists(GetCampaignConfig::class));
    }
}