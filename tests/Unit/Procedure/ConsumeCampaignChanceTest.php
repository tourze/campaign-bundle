<?php

namespace CampaignBundle\Tests\Unit\Procedure;

use CampaignBundle\Procedure\ConsumeCampaignChance;
use CampaignBundle\Tests\BaseTestCase;

class ConsumeCampaignChanceTest extends BaseTestCase
{
    public function testProcedureClass(): void
    {
        $this->assertTrue(class_exists(ConsumeCampaignChance::class));
    }
}