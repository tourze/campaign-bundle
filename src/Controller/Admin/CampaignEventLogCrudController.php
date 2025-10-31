<?php

declare(strict_types=1);

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\EventLog;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/** @extends AbstractCrudController<EventLog> */
#[AdminCrud(routePath: '/campaign/event-log', routeName: 'campaign_event_log')]
final class CampaignEventLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EventLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('参与日志')
            ->setEntityLabelInPlural('参与日志管理')
            ->setPageTitle('index', '参与日志列表')
            ->setPageTitle('detail', '参与日志详情')
            ->setHelp('index', '查看用户参与活动的详细日志记录')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'event'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield AssociationField::new('campaign', '所属活动');
        yield AssociationField::new('user', '用户');
        yield TextField::new('event', '事件类型');
        yield ArrayField::new('params', '参数')
            ->setHelp('JSON格式的事件参数')
            ->hideOnIndex()
            ->onlyOnDetail()
        ;
        yield TextField::new('paramsPreview', '参数')
            ->onlyOnIndex()
        ;
        yield DateTimeField::new('createTime', '创建时间');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
            ->setPermissions([
                Action::INDEX => 'ROLE_ADMIN',
                Action::DETAIL => 'ROLE_ADMIN',
            ])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('campaign', '所属活动'))
            ->add(EntityFilter::new('user', '用户'))
            ->add(TextFilter::new('event', '事件类型'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
