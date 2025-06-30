<?php

namespace CampaignBundle\Tests\Integration\Command;

use CampaignBundle\Command\ChanceExpireCommand;
use CampaignBundle\Tests\BaseTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ChanceExpireCommandTest extends BaseTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        $command = $container->get(ChanceExpireCommand::class);
        $this->assertInstanceOf(ChanceExpireCommand::class, $command);
        
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}