<?php

namespace CampaignBundle\Tests\Integration\Command;

use CampaignBundle\Command\CheckExpiredCampaignCommand;
use CampaignBundle\Tests\BaseTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CheckExpiredCampaignCommandTest extends BaseTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        $command = $container->get(CheckExpiredCampaignCommand::class);
        $this->assertInstanceOf(CheckExpiredCampaignCommand::class, $command);
        
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}