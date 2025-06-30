<?php

namespace CampaignBundle\Tests\Integration\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignChanceCrudController;
use CampaignBundle\Tests\BaseTestCase;

class CampaignChanceCrudControllerTest extends BaseTestCase
{
    public function testController(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        $controller = $container->get(CampaignChanceCrudController::class);
        $this->assertInstanceOf(CampaignChanceCrudController::class, $controller);
    }
}