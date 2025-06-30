<?php

namespace CampaignBundle\Tests\Integration\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignAwardCrudController;
use CampaignBundle\Tests\BaseTestCase;

class CampaignAwardCrudControllerTest extends BaseTestCase
{
    public function testController(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        $controller = $container->get(CampaignAwardCrudController::class);
        $this->assertInstanceOf(CampaignAwardCrudController::class, $controller);
    }
}