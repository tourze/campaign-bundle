<?php

namespace CampaignBundle\Tests\Integration\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignCategoryCrudController;
use CampaignBundle\Tests\BaseTestCase;

class CampaignCategoryCrudControllerTest extends BaseTestCase
{
    public function testController(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        $controller = $container->get(CampaignCategoryCrudController::class);
        $this->assertInstanceOf(CampaignCategoryCrudController::class, $controller);
    }
}