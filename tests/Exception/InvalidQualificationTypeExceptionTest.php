<?php

namespace CampaignBundle\Tests\Exception;

use CampaignBundle\Exception\InvalidQualificationTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(InvalidQualificationTypeException::class)]
final class InvalidQualificationTypeExceptionTest extends AbstractExceptionTestCase
{
    public function testException(): void
    {
        $exception = new InvalidQualificationTypeException();
        $this->assertInstanceOf(InvalidQualificationTypeException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $exception = new InvalidQualificationTypeException('Test message');
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testExceptionWithCode(): void
    {
        $exception = new InvalidQualificationTypeException('', 100);
        $this->assertEquals(100, $exception->getCode());
    }

    public function testExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new InvalidQualificationTypeException('', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }
}
