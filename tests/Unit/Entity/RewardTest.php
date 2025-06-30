<?php

namespace CampaignBundle\Tests\Unit\Entity;

use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Tests\BaseTestCase;

class RewardTest extends BaseTestCase
{
    public function testConstruct(): void
    {
        $reward = new Reward();
        $this->assertInstanceOf(Reward::class, $reward);
    }

    public function testSettersAndGetters(): void
    {
        $reward = new Reward();
        
        $reward->setValid(true);
        $this->assertTrue($reward->isValid());
        
        $reward->setType(AwardType::COUPON);
        $this->assertEquals(AwardType::COUPON, $reward->getType());
        
        $reward->setValue('test-value');
        $this->assertEquals('test-value', $reward->getValue());
    }
}