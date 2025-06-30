<?php

namespace CampaignBundle\Tests\Unit\Entity;

use CampaignBundle\Entity\Chance;
use CampaignBundle\Tests\BaseTestCase;

class ChanceTest extends BaseTestCase
{
    public function testConstruct(): void
    {
        $chance = new Chance();
        $this->assertInstanceOf(Chance::class, $chance);
    }

    public function testSettersAndGetters(): void
    {
        $chance = new Chance();
        
        $chance->setValid(true);
        $this->assertTrue($chance->isValid());
        
        $chance->setContext(['key' => 'value']);
        $this->assertEquals(['key' => 'value'], $chance->getContext());
    }
}