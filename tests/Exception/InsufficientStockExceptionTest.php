<?php

namespace CampaignBundle\Tests\Exception;

use CampaignBundle\Exception\InsufficientStockException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(InsufficientStockException::class)]
final class InsufficientStockExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCreation(): void
    {
        $exception = new InsufficientStockException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('库存不足', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionWithCustomMessage(): void
    {
        $customMessage = '商品库存已售罄';
        $exception = new InsufficientStockException($customMessage);

        $this->assertEquals($customMessage, $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previous = new \Exception('原始异常');
        $customMessage = '奖品库存不足，无法兑换';
        $code = 3001;

        $exception = new InsufficientStockException($customMessage, $code, $previous);

        $this->assertEquals($customMessage, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionDefaultMessage(): void
    {
        $exception = new InsufficientStockException();

        $this->assertEquals('库存不足', $exception->getMessage());
    }
}
