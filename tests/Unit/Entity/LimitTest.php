<?php

namespace CampaignBundle\Tests\Unit\Entity;

use CampaignBundle\Entity\Limit;
use CampaignBundle\Enum\LimitType;
use CampaignBundle\Tests\BaseTestCase;

class LimitTest extends BaseTestCase
{
    public function testConstruct(): void
    {
        $limit = new Limit();
        $this->assertInstanceOf(Limit::class, $limit);
    }

    public function testSettersAndGetters(): void
    {
        $limit = new Limit();
        
        $limit->setType(LimitType::CHANCE);
        $this->assertEquals(LimitType::CHANCE, $limit->getType());
        
        $limit->setValue('test-value');
        $this->assertEquals('test-value', $limit->getValue());
    }
}