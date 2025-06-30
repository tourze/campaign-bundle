<?php

namespace CampaignBundle\Tests\Unit\Entity;

use CampaignBundle\Entity\Category;
use CampaignBundle\Tests\BaseTestCase;

class CategoryTest extends BaseTestCase
{
    public function testConstruct(): void
    {
        $category = new Category();
        $this->assertInstanceOf(Category::class, $category);
    }

    public function testSettersAndGetters(): void
    {
        $category = new Category();
        
        $category->setTitle('test-category');
        $this->assertEquals('test-category', $category->getTitle());
        
        $category->setValid(true);
        $this->assertTrue($category->isValid());
    }

    public function testToString(): void
    {
        $category = new Category();
        $category->setTitle('test-category');
        $this->assertEquals('test-category', $category->getTitle());
    }
}