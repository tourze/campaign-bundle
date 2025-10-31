<?php

declare(strict_types=1);

namespace CampaignBundle\DataFixtures;

use CampaignBundle\Entity\Attribute;
use CampaignBundle\Entity\Campaign;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 测试和开发环境用的属性实体数据固件
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class AttributeFixtures extends Fixture implements DependentFixtureInterface
{
    public const ATTRIBUTE_DISPLAY_ORDER_REFERENCE = 'attribute-display-order';
    public const ATTRIBUTE_THEME_COLOR_REFERENCE = 'attribute-theme-color';

    /**
     * 将属性数据固件加载到数据库中
     *
     * @param ObjectManager $manager 对象管理器
     */
    public function load(ObjectManager $manager): void
    {
        $campaign = $this->getReference(BasicCampaignFixtures::CAMPAIGN_TYJG_REFERENCE, Campaign::class);

        $displayOrderAttribute = new Attribute();
        $displayOrderAttribute->setCampaign($campaign);
        $displayOrderAttribute->setName('display_order');
        $displayOrderAttribute->setValue('1');
        $displayOrderAttribute->setRemark('展示顺序');

        $manager->persist($displayOrderAttribute);
        $this->addReference(self::ATTRIBUTE_DISPLAY_ORDER_REFERENCE, $displayOrderAttribute);

        $themeColorAttribute = new Attribute();
        $themeColorAttribute->setCampaign($campaign);
        $themeColorAttribute->setName('theme_color');
        $themeColorAttribute->setValue('#FF6B6B');
        $themeColorAttribute->setRemark('主题颜色');

        $manager->persist($themeColorAttribute);
        $this->addReference(self::ATTRIBUTE_THEME_COLOR_REFERENCE, $themeColorAttribute);

        $manager->flush();
    }

    /**
     * 获取数据固件依赖项
     *
     * @return array<class-string<FixtureInterface>> 此固件依赖的固件类
     */
    public function getDependencies(): array
    {
        return [
            BasicCampaignFixtures::class,
        ];
    }
}
