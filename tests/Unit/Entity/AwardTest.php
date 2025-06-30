<?php

namespace CampaignBundle\Tests\Unit\Entity;

use CampaignBundle\Entity\Award;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Tests\BaseTestCase;

class AwardTest extends BaseTestCase
{
    public function testConstruct(): void
    {
        $award = new Award();
        $this->assertInstanceOf(Award::class, $award);
    }

    public function testSettersAndGetters(): void
    {
        $award = new Award();
        
        $award->setEvent('test-event');
        $this->assertEquals('test-event', $award->getEvent());
        
        $award->setType(AwardType::COUPON);
        $this->assertEquals(AwardType::COUPON, $award->getType());
        
        $award->setValue('test-value');
        $this->assertEquals('test-value', $award->getValue());
    }
}