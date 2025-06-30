<?php

namespace CampaignBundle\Tests\Unit\Procedure;

use CampaignBundle\Procedure\RequestCampaignChance;
use CampaignBundle\Tests\BaseTestCase;

class RequestCampaignChanceTest extends BaseTestCase
{
    public function testProcedureClass(): void
    {
        $this->assertTrue(class_exists(RequestCampaignChance::class));
    }
}