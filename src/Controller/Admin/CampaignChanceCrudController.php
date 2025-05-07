<?php

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Chance;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CampaignChanceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Chance::class;
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
