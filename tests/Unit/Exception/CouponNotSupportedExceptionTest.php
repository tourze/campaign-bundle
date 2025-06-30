<?php

namespace CampaignBundle\Tests\Unit\Exception;

use CampaignBundle\Exception\CouponNotSupportedException;
use CampaignBundle\Tests\BaseTestCase;

class CouponNotSupportedExceptionTest extends BaseTestCase
{
    public function testException(): void
    {
        $exception = new CouponNotSupportedException();
        $this->assertInstanceOf(CouponNotSupportedException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}