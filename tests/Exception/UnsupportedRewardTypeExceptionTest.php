<?php

declare(strict_types=1);

namespace CampaignBundle\Tests\Exception;

use CampaignBundle\Exception\UnsupportedRewardTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(UnsupportedRewardTypeException::class)]
final class UnsupportedRewardTypeExceptionTest extends AbstractExceptionTestCase
{
    protected function createException(?string $message = null): UnsupportedRewardTypeException
    {
        return new UnsupportedRewardTypeException($message ?? '');
    }

    protected function getExpectedException(): string
    {
        return \RuntimeException::class;
    }

    protected function getExceptionClass(): string
    {
        return UnsupportedRewardTypeException::class;
    }

    protected function getDefaultMessage(): string
    {
        return '';
    }

    public function testExceptionWithCode(): void
    {
        $message = '不支持的奖励类型';
        $code = 1001;
        $exception = new UnsupportedRewardTypeException($message, $code);
        $this->assertEquals($code, $exception->getCode());
    }

    public function testExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new UnsupportedRewardTypeException('Message', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }
}
