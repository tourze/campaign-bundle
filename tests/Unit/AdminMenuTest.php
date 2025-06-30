<?php

namespace CampaignBundle\Tests\Unit;

use CampaignBundle\AdminMenu;
use CampaignBundle\Tests\BaseTestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

class AdminMenuTest extends BaseTestCase
{
    public function testConstruct(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $adminMenu = new AdminMenu($linkGenerator);
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }
}