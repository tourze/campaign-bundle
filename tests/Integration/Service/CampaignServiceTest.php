<?php

namespace CampaignBundle\Tests\Integration\Service;

use CampaignBundle\Service\CampaignService;
use CampaignBundle\Tests\BaseTestCase;

class CampaignServiceTest extends BaseTestCase
{
    public function testServiceClass(): void
    {
        $this->assertTrue(class_exists(CampaignService::class));
    }
}