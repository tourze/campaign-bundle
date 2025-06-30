<?php

namespace CampaignBundle\Tests\Integration\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignCampaignCrudController;
use CampaignBundle\Tests\BaseTestCase;

class CampaignCampaignCrudControllerTest extends BaseTestCase
{
    public function testController(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        $controller = $container->get(CampaignCampaignCrudController::class);
        $this->assertInstanceOf(CampaignCampaignCrudController::class, $controller);
    }
}