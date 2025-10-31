<?php

declare(strict_types=1);

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Campaign;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/** @extends AbstractCrudController<Campaign> */
#[AdminCrud(routePath: '/campaign/campaign', routeName: 'campaign_campaign')]
final class CampaignCampaignCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Campaign::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('活动')
            ->setEntityLabelInPlural('活动管理')
            ->setPageTitle('index', '活动列表')
            ->setPageTitle('new', '新增活动')
            ->setPageTitle('edit', '编辑活动')
            ->setPageTitle('detail', '活动详情')
            ->setHelp('index', '管理活动信息、时间设置和状态配置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'code', 'name', 'subtitle'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield TextField::new('code', '活动代号');
        yield TextField::new('name', '活动名称');
        yield TextField::new('subtitle', '副标题');
        yield AssociationField::new('category', '活动分类');
        yield BooleanField::new('valid', '有效状态');
        yield BooleanField::new('recommend', '推荐');
        yield IntegerField::new('sortNumber', '排序');
        yield UrlField::new('thumbUrl', '缩略图');
        yield UrlField::new('shareImg', '分享图片');
        yield TextField::new('shareTitle', '分享标题');
        yield UrlField::new('entryUrl', '入口地址');
        yield DateTimeField::new('startTime', '开始时间');
        yield DateTimeField::new('endTime', '结束时间');
        yield TextareaField::new('tags', '标签')
            ->formatValue(function ($value) {
                return is_array($value) ? implode(', ', $value) : $value;
            })
        ;
        yield TextareaField::new('requestExpression', '请求表达式');
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
            ->add(TextFilter::new('code', '活动代号'))
            ->add(TextFilter::new('name', '活动名称'))
            ->add(EntityFilter::new('category', '活动分类'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(BooleanFilter::new('recommend', '推荐'))
            ->add(DateTimeFilter::new('startTime', '开始时间'))
            ->add(DateTimeFilter::new('endTime', '结束时间'))
        ;
    }
}
