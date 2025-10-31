<?php

declare(strict_types=1);

namespace CampaignBundle\Controller\Admin;

use CampaignBundle\Entity\Award;
use CampaignBundle\Enum\AwardLimitType;
use CampaignBundle\Enum\AwardType;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/** @extends AbstractCrudController<Award> */
#[AdminCrud(routePath: '/campaign/award', routeName: 'campaign_award')]
final class CampaignAwardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Award::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('奖励')
            ->setEntityLabelInPlural('奖励管理')
            ->setPageTitle('index', '奖励列表')
            ->setPageTitle('new', '新增奖励')
            ->setPageTitle('edit', '编辑奖励')
            ->setPageTitle('detail', '奖励详情')
            ->setHelp('index', '管理活动奖励配置和限制设置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'event', 'value', 'remark'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield AssociationField::new('campaign', '所属活动');
        yield TextField::new('event', '触发事件');
        yield ChoiceField::new('type', '奖励类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => AwardType::class])
            ->formatValue(function ($value) {
                return $value instanceof AwardType ? $value->getLabel() : '';
            })
        ;
        yield TextField::new('value', '奖励值/ID');
        yield TextareaField::new('remark', '备注');
        yield IntegerField::new('prizeQuantity', '总数量');
        yield ChoiceField::new('awardLimitType', '限制类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => AwardLimitType::class])
            ->formatValue(function ($value) {
                return $value instanceof AwardLimitType ? $value->getLabel() : '';
            })
        ;
        yield IntegerField::new('times', '限制次数');
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
        $awardTypeChoices = [];
        foreach (AwardType::cases() as $case) {
            $awardTypeChoices[$case->getLabel()] = $case->value;
        }

        $limitTypeChoices = [];
        foreach (AwardLimitType::cases() as $case) {
            $limitTypeChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('campaign', '所属活动'))
            ->add(TextFilter::new('event', '触发事件'))
            ->add(ChoiceFilter::new('type', '奖励类型')->setChoices($awardTypeChoices))
            ->add(ChoiceFilter::new('awardLimitType', '限制类型')->setChoices($limitTypeChoices))
            ->add(TextFilter::new('value', '奖励值'))
        ;
    }
}
