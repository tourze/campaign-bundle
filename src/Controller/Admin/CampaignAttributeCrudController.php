<?php

declare(strict_types=1);

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Attribute;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/** @extends AbstractCrudController<Attribute> */
#[AdminCrud(routePath: '/campaign/attribute', routeName: 'campaign_attribute')]
final class CampaignAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Attribute::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('活动属性')
            ->setEntityLabelInPlural('活动属性管理')
            ->setPageTitle('index', '活动属性列表')
            ->setPageTitle('new', '新增活动属性')
            ->setPageTitle('edit', '编辑活动属性')
            ->setPageTitle('detail', '活动属性详情')
            ->setHelp('index', '管理活动的自定义属性配置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'value'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield AssociationField::new('campaign', '所属活动');
        yield TextField::new('name', '属性名称');
        yield TextField::new('value', '属性值');
        yield TextareaField::new('remark', '备注');
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
            ->add(TextFilter::new('name', '属性名称'))
            ->add(TextFilter::new('value', '属性值'))
        ;
    }
}
