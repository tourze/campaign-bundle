<?php

namespace CampaignBundle\Tests\Exception;

use CampaignBundle\Exception\RewardLimitExceededException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(RewardLimitExceededException::class)]
final class RewardLimitExceededExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCreation(): void
    {
        $exception = new RewardLimitExceededException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('已达到领取限制', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionWithCustomMessage(): void
    {
        $customMessage = '您今日已达到奖励领取上限';
        $exception = new RewardLimitExceededException($customMessage);

        $this->assertEquals($customMessage, $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previous = new \Exception('原始异常');
        $customMessage = '用户领取次数超限';
        $code = 2001;

        $exception = new RewardLimitExceededException($customMessage, $code, $previous);

        $this->assertEquals($customMessage, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionDefaultMessage(): void
    {
        $exception = new RewardLimitExceededException();

        $this->assertEquals('已达到领取限制', $exception->getMessage());
    }
}
