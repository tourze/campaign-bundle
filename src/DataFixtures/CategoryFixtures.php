<?php

declare(strict_types=1);

namespace CampaignBundle\DataFixtures;

use CampaignBundle\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 测试和开发环境用的分类实体数据固件
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class CategoryFixtures extends Fixture
{
    public const CATEGORY_FESTIVAL_REFERENCE = 'category-festival';
    public const CATEGORY_DAILY_REFERENCE = 'category-daily';

    /**
     * 将分类数据固件加载到数据库中
     *
     * @param ObjectManager $manager 对象管理器
     */
    public function load(ObjectManager $manager): void
    {
        $festivalCategory = new Category();
        $festivalCategory->setTitle('节日活动');
        $festivalCategory->setValid(true);
        $festivalCategory->setSortNumber(1);

        $manager->persist($festivalCategory);
        $this->addReference(self::CATEGORY_FESTIVAL_REFERENCE, $festivalCategory);

        $dailyCategory = new Category();
        $dailyCategory->setTitle('日常活动');
        $dailyCategory->setValid(true);
        $dailyCategory->setSortNumber(2);

        $manager->persist($dailyCategory);
        $this->addReference(self::CATEGORY_DAILY_REFERENCE, $dailyCategory);

        $manager->flush();
    }
}
