<?php

namespace CampaignBundle\Tests\Unit\Enum;

use CampaignBundle\Enum\CampaignStatus;
use CampaignBundle\Tests\BaseTestCase;

class CampaignStatusTest extends BaseTestCase
{
    public function testEnum(): void
    {
        $this->assertTrue(enum_exists(CampaignStatus::class));
        $this->assertCount(3, CampaignStatus::cases());
        $this->assertSame('pending', CampaignStatus::PENDING->value);
        $this->assertSame('未开始', CampaignStatus::PENDING->getLabel());
    }
}