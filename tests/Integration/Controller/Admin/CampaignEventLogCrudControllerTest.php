<?php

namespace CampaignBundle\Tests\Integration\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignEventLogCrudController;
use CampaignBundle\Tests\BaseTestCase;

class CampaignEventLogCrudControllerTest extends BaseTestCase
{
    public function testController(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        $controller = $container->get(CampaignEventLogCrudController::class);
        $this->assertInstanceOf(CampaignEventLogCrudController::class, $controller);
    }
}