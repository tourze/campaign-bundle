<?php

namespace CampaignBundle\Tests\Unit\ExpressionLanguage\Function;

use CampaignBundle\ExpressionLanguage\Function\ChanceFunctionProvider;
use CampaignBundle\Tests\BaseTestCase;

class ChanceFunctionProviderTest extends BaseTestCase
{
    public function testFunctionProviderClass(): void
    {
        $this->assertTrue(class_exists(ChanceFunctionProvider::class));
    }
}