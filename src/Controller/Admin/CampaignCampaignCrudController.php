<?php

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Campaign;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class CampaignCampaignCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Campaign::class;
    }
}
