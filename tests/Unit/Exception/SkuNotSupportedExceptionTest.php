<?php

namespace CampaignBundle\Tests\Unit\Exception;

use CampaignBundle\Exception\SkuNotSupportedException;
use CampaignBundle\Tests\BaseTestCase;

class SkuNotSupportedExceptionTest extends BaseTestCase
{
    public function testException(): void
    {
        $exception = new SkuNotSupportedException();
        $this->assertInstanceOf(SkuNotSupportedException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}