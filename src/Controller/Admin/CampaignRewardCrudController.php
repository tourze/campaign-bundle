<?php

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Reward;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CampaignRewardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reward::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
