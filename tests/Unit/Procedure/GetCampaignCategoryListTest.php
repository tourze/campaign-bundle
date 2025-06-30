<?php

namespace CampaignBundle\Tests\Unit\Procedure;

use CampaignBundle\Procedure\GetCampaignCategoryList;
use CampaignBundle\Tests\BaseTestCase;

class GetCampaignCategoryListTest extends BaseTestCase
{
    public function testProcedureClass(): void
    {
        $this->assertTrue(class_exists(GetCampaignCategoryList::class));
    }
}