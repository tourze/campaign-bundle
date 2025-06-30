<?php

namespace CampaignBundle\Tests\Unit\Enum;

use CampaignBundle\Enum\AwardType;
use CampaignBundle\Tests\BaseTestCase;

class AwardTypeTest extends BaseTestCase
{
    public function testEnum(): void
    {
        $this->assertTrue(enum_exists(AwardType::class));
        $this->assertCount(5, AwardType::cases());
        $this->assertSame('coupon', AwardType::COUPON->value);
        $this->assertSame('优惠券', AwardType::COUPON->getLabel());
    }
}