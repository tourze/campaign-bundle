<?php

namespace CampaignBundle\Tests\Unit\DependencyInjection;

use CampaignBundle\DependencyInjection\CampaignExtension;
use CampaignBundle\Tests\BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CampaignExtensionTest extends BaseTestCase
{
    public function testConstruct(): void
    {
        $extension = new CampaignExtension();
        $this->assertInstanceOf(CampaignExtension::class, $extension);
    }

    public function testLoad(): void
    {
        $extension = new CampaignExtension();
        $container = new ContainerBuilder();
        
        $extension->load([], $container);
        
        $this->assertTrue($container->hasDefinition('CampaignBundle\Service\CampaignService'));
    }
}