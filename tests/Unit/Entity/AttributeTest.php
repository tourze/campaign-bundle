<?php

namespace CampaignBundle\Tests\Unit\Entity;

use CampaignBundle\Entity\Attribute;
use CampaignBundle\Tests\BaseTestCase;

class AttributeTest extends BaseTestCase
{
    public function testConstruct(): void
    {
        $attribute = new Attribute();
        $this->assertInstanceOf(Attribute::class, $attribute);
    }

    public function testSettersAndGetters(): void
    {
        $attribute = new Attribute();
        
        $attribute->setName('test-name');
        $this->assertEquals('test-name', $attribute->getName());
        
        $attribute->setValue('test-value');
        $this->assertEquals('test-value', $attribute->getValue());
    }
}