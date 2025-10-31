<?php

namespace CampaignBundle\Tests\Exception;

use CampaignBundle\Exception\SpuNotSupportedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(SpuNotSupportedException::class)]
final class SpuNotSupportedExceptionTest extends AbstractExceptionTestCase
{
    public function testException(): void
    {
        $exception = new SpuNotSupportedException();
        $this->assertInstanceOf(SpuNotSupportedException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}
