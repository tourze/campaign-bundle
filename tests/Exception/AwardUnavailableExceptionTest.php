<?php

namespace CampaignBundle\Tests\Exception;

use CampaignBundle\Exception\AwardUnavailableException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(AwardUnavailableException::class)]
final class AwardUnavailableExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCreation(): void
    {
        $exception = new AwardUnavailableException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('奖品不可用', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionWithCustomMessage(): void
    {
        $customMessage = '该奖品已下架';
        $exception = new AwardUnavailableException($customMessage);

        $this->assertEquals($customMessage, $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previous = new \Exception('原始异常');
        $customMessage = '奖品暂时不可用';
        $code = 1001;

        $exception = new AwardUnavailableException($customMessage, $code, $previous);

        $this->assertEquals($customMessage, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionDefaultMessage(): void
    {
        $exception = new AwardUnavailableException();

        $this->assertEquals('奖品不可用', $exception->getMessage());
    }
}
