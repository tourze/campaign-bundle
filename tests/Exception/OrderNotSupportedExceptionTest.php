<?php

namespace CampaignBundle\Tests\Exception;

use CampaignBundle\Exception\OrderNotSupportedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(OrderNotSupportedException::class)]
final class OrderNotSupportedExceptionTest extends AbstractExceptionTestCase
{
    public function testException(): void
    {
        $exception = new OrderNotSupportedException();
        $this->assertInstanceOf(OrderNotSupportedException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}
