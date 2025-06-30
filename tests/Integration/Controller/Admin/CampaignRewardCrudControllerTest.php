<?php

namespace CampaignBundle\Tests\Integration\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignRewardCrudController;
use CampaignBundle\Tests\BaseTestCase;

class CampaignRewardCrudControllerTest extends BaseTestCase
{
    public function testController(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        $controller = $container->get(CampaignRewardCrudController::class);
        $this->assertInstanceOf(CampaignRewardCrudController::class, $controller);
    }
}