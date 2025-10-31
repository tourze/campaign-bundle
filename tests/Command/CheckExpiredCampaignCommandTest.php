<?php

namespace CampaignBundle\Tests\Command;

use CampaignBundle\Command\CheckExpiredCampaignCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CheckExpiredCampaignCommand::class)]
#[RunTestsInSeparateProcesses]
final class CheckExpiredCampaignCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(CheckExpiredCampaignCommand::class);

        return new CommandTester($command);
    }

    public function testCommandName(): void
    {
        $this->assertSame('campaign:check-expired-campaign', CheckExpiredCampaignCommand::NAME);
    }

    public function testCommandExecution(): void
    {
        $commandTester = $this->getCommandTester();
        $result = $commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $result);
    }
}
