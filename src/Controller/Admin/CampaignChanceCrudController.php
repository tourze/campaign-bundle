<?php

declare(strict_types=1);

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Chance;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

/** @extends AbstractCrudController<Chance> */
#[AdminCrud(routePath: '/campaign/chance', routeName: 'campaign_chance')]
final class CampaignChanceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Chance::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('参与机会')
            ->setEntityLabelInPlural('参与机会管理')
            ->setPageTitle('index', '参与机会列表')
            ->setPageTitle('new', '新增参与机会')
            ->setPageTitle('edit', '编辑参与机会')
            ->setPageTitle('detail', '参与机会详情')
            ->setHelp('index', '管理用户参与活动的机会记录')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield AssociationField::new('campaign', '所属活动');
        yield AssociationField::new('user', '用户');
        yield BooleanField::new('valid', '有效状态');
        yield DateTimeField::new('startTime', '开始时间');
        yield DateTimeField::new('expireTime', '过期时间');
        yield ArrayField::new('context', '上下文')
            ->setHelp('JSON格式的上下文数据')
            ->hideOnIndex()
            ->onlyOnDetail()
        ;
        yield DateTimeField::new('createTime', '创建时间')->hideOnForm();
        yield DateTimeField::new('updateTime', '更新时间')->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('campaign', '所属活动'))
            ->add(EntityFilter::new('user', '用户'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(DateTimeFilter::new('startTime', '开始时间'))
            ->add(DateTimeFilter::new('expireTime', '过期时间'))
        ;
    }
}
