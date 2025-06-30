<?php

namespace CampaignBundle\Tests\Integration\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignAttributeCrudController;
use CampaignBundle\Tests\BaseTestCase;

class CampaignAttributeCrudControllerTest extends BaseTestCase
{
    public function testController(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        $controller = $container->get(CampaignAttributeCrudController::class);
        $this->assertInstanceOf(CampaignAttributeCrudController::class, $controller);
    }
}