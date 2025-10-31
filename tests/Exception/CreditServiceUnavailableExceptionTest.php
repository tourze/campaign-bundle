<?php

namespace CampaignBundle\Tests\Exception;

use CampaignBundle\Exception\CreditServiceUnavailableException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CreditServiceUnavailableException::class)]
final class CreditServiceUnavailableExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCreation(): void
    {
        $exception = new CreditServiceUnavailableException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('积分服务不可用', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionWithCustomMessage(): void
    {
        $customMessage = '积分系统正在维护中';
        $exception = new CreditServiceUnavailableException($customMessage);

        $this->assertEquals($customMessage, $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previous = new \Exception('原始异常');
        $customMessage = '积分服务响应超时';
        $code = 4001;

        $exception = new CreditServiceUnavailableException($customMessage, $code, $previous);

        $this->assertEquals($customMessage, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionDefaultMessage(): void
    {
        $exception = new CreditServiceUnavailableException();

        $this->assertEquals('积分服务不可用', $exception->getMessage());
    }
}
