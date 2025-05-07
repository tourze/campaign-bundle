<?php

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Limit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CampaignLimitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Limit::class;
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
