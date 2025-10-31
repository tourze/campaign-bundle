<?php

declare(strict_types=1);

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Limit;
use CampaignBundle\Enum\LimitType;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/** @extends AbstractCrudController<Limit> */
#[AdminCrud(routePath: '/campaign/limit', routeName: 'campaign_limit')]
final class CampaignLimitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Limit::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('限制条件')
            ->setEntityLabelInPlural('限制条件管理')
            ->setPageTitle('index', '限制条件列表')
            ->setPageTitle('new', '新增限制条件')
            ->setPageTitle('edit', '编辑限制条件')
            ->setPageTitle('detail', '限制条件详情')
            ->setHelp('index', '管理奖励的获取限制条件')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'value', 'remark'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield AssociationField::new('award', '所属奖励');
        yield ChoiceField::new('type', '限制类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => LimitType::class])
            ->formatValue(function ($value) {
                return $value instanceof LimitType ? $value->getLabel() : '';
            })
        ;
        yield TextField::new('value', '条件值');
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
        $limitTypeChoices = [];
        foreach (LimitType::cases() as $case) {
            $limitTypeChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('award', '所属奖励'))
            ->add(ChoiceFilter::new('type', '限制类型')->setChoices($limitTypeChoices))
            ->add(TextFilter::new('value', '条件值'))
        ;
    }
}
