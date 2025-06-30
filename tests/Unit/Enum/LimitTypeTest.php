<?php

namespace CampaignBundle\Tests\Unit\Enum;

use CampaignBundle\Enum\LimitType;
use CampaignBundle\Tests\BaseTestCase;

class LimitTypeTest extends BaseTestCase
{
    public function testEnum(): void
    {
        $this->assertTrue(enum_exists(LimitType::class));
        $this->assertCount(2, LimitType::cases());
        $this->assertSame('user-tag', LimitType::USER_TAG->value);
        $this->assertSame('用户标签', LimitType::USER_TAG->getLabel());
    }
}