<?php

namespace CampaignBundle\Tests\Unit\Exception;

use CampaignBundle\Exception\SpuNotSupportedException;
use CampaignBundle\Tests\BaseTestCase;

class SpuNotSupportedExceptionTest extends BaseTestCase
{
    public function testException(): void
    {
        $exception = new SpuNotSupportedException();
        $this->assertInstanceOf(SpuNotSupportedException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}