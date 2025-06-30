<?php

namespace CampaignBundle\Tests\Unit\Exception;

use CampaignBundle\Exception\OrderNotSupportedException;
use CampaignBundle\Tests\BaseTestCase;

class OrderNotSupportedExceptionTest extends BaseTestCase
{
    public function testException(): void
    {
        $exception = new OrderNotSupportedException();
        $this->assertInstanceOf(OrderNotSupportedException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}