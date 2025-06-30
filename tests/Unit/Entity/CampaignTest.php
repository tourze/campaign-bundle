<?php

namespace CampaignBundle\Tests\Unit\Entity;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Enum\CampaignStatus;
use CampaignBundle\Tests\BaseTestCase;

class CampaignTest extends BaseTestCase
{
    public function testConstruct(): void
    {
        $campaign = new Campaign();
        $this->assertInstanceOf(Campaign::class, $campaign);
    }

    public function testSettersAndGetters(): void
    {
        $campaign = new Campaign();
        
        $campaign->setName('test-campaign');
        $this->assertEquals('test-campaign', $campaign->getName());
        
        $campaign->setCode('test-code');
        $this->assertEquals('test-code', $campaign->getCode());
        
        $campaign->setValid(true);
        $this->assertTrue($campaign->isValid());
    }
}