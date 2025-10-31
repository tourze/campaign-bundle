<?php

declare(strict_types=1);

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/** @extends AbstractCrudController<Category> */
#[AdminCrud(routePath: '/campaign/category', routeName: 'campaign_category')]
final class CampaignCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('活动分类')
            ->setEntityLabelInPlural('活动分类管理')
            ->setPageTitle('index', '活动分类列表')
            ->setPageTitle('new', '新增活动分类')
            ->setPageTitle('edit', '编辑活动分类')
            ->setPageTitle('detail', '活动分类详情')
            ->setHelp('index', '管理活动分类和排序设置')
            ->setDefaultSort(['sortNumber' => 'ASC'])
            ->setSearchFields(['id', 'title'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield TextField::new('title', '分类名称');
        yield BooleanField::new('valid', '有效状态');
        yield IntegerField::new('sortNumber', '排序');
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
            ->add(TextFilter::new('title', '分类名称'))
            ->add(BooleanFilter::new('valid', '有效状态'))
        ;
    }
}
