<?php

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\EventLog;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CampaignEventLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EventLog::class;
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
