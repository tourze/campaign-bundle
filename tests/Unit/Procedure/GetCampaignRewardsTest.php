<?php

namespace CampaignBundle\Tests\Unit\Procedure;

use CampaignBundle\Procedure\GetCampaignRewards;
use CampaignBundle\Tests\BaseTestCase;

class GetCampaignRewardsTest extends BaseTestCase
{
    public function testProcedureClass(): void
    {
        $this->assertTrue(class_exists(GetCampaignRewards::class));
    }
}