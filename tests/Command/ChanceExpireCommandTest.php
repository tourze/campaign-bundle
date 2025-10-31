<?php

namespace CampaignBundle\Tests\Command;

use CampaignBundle\Command\ChanceExpireCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(ChanceExpireCommand::class)]
#[RunTestsInSeparateProcesses]
final class ChanceExpireCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(ChanceExpireCommand::class);

        return new CommandTester($command);
    }

    public function testCommandName(): void
    {
        $this->assertSame('campaign:chance-expire', ChanceExpireCommand::NAME);
    }

    public function testCommandExecution(): void
    {
        $commandTester = $this->getCommandTester();
        $result = $commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $result);
    }
}
