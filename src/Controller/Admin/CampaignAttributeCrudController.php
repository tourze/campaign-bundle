<?php

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Attribute;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CampaignAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Attribute::class;
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
