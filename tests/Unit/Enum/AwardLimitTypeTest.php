<?php

namespace CampaignBundle\Tests\Unit\Enum;

use CampaignBundle\Enum\AwardLimitType;
use CampaignBundle\Tests\BaseTestCase;

class AwardLimitTypeTest extends BaseTestCase
{
    public function testEnum(): void
    {
        $this->assertTrue(enum_exists(AwardLimitType::class));
        $this->assertCount(6, AwardLimitType::cases());
        $this->assertSame('buy-total', AwardLimitType::BUY_TOTAL->value);
        $this->assertSame('总次数限购', AwardLimitType::BUY_TOTAL->getLabel());
    }
}