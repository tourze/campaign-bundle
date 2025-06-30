<?php

namespace CampaignBundle\Tests\Unit;

use CampaignBundle\CampaignBundle;
use CampaignBundle\Tests\BaseTestCase;

class CampaignBundleTest extends BaseTestCase
{
    public function testConstruct(): void
    {
        $bundle = new CampaignBundle();
        $this->assertInstanceOf(CampaignBundle::class, $bundle);
    }
}