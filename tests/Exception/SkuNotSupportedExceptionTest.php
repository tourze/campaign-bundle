<?php

namespace CampaignBundle\Tests\Exception;

use CampaignBundle\Exception\SkuNotSupportedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(SkuNotSupportedException::class)]
final class SkuNotSupportedExceptionTest extends AbstractExceptionTestCase
{
    public function testException(): void
    {
        $exception = new SkuNotSupportedException();
        $this->assertInstanceOf(SkuNotSupportedException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}
