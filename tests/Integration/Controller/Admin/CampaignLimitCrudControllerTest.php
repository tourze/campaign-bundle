<?php

namespace CampaignBundle\Tests\Integration\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignLimitCrudController;
use CampaignBundle\Tests\BaseTestCase;

class CampaignLimitCrudControllerTest extends BaseTestCase
{
    public function testController(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        $controller = $container->get(CampaignLimitCrudController::class);
        $this->assertInstanceOf(CampaignLimitCrudController::class, $controller);
    }
}