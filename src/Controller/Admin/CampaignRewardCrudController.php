<?php

declare(strict_types=1);

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Reward;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/** @extends AbstractCrudController<Reward> */
#[AdminCrud(routePath: '/campaign/reward', routeName: 'campaign_reward')]
final class CampaignRewardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reward::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('奖励记录')
            ->setEntityLabelInPlural('奖励记录管理')
            ->setPageTitle('index', '奖励记录列表')
            ->setPageTitle('new', '新增奖励记录')
            ->setPageTitle('edit', '编辑奖励记录')
            ->setPageTitle('detail', '奖励记录详情')
            ->setHelp('index', '管理用户获得的奖励记录')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'sn'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield TextField::new('sn', '奖品序列号');
        yield AssociationField::new('campaign', '所属活动');
        yield AssociationField::new('award', '奖励配置');
        yield AssociationField::new('user', '获奖用户');
        yield BooleanField::new('valid', '有效状态');
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
            ->add(EntityFilter::new('award', '奖励配置'))
            ->add(EntityFilter::new('user', '获奖用户'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(TextFilter::new('sn', '奖品序列号'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
