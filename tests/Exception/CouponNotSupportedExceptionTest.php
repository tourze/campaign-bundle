<?php

namespace CampaignBundle\Tests\Exception;

use CampaignBundle\Exception\CouponNotSupportedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CouponNotSupportedException::class)]
final class CouponNotSupportedExceptionTest extends AbstractExceptionTestCase
{
    public function testException(): void
    {
        $exception = new CouponNotSupportedException();
        $this->assertInstanceOf(CouponNotSupportedException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}
