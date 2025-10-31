<?php

declare(strict_types=1);

namespace CampaignBundle\DataFixtures;

use CampaignBundle\Entity\Campaign;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 测试和开发环境用的活动实体数据固件
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class CampaignFixtures extends Fixture
{
    public const CAMPAIGN_SPRING_FESTIVAL_REFERENCE = 'campaign-spring-festival';
    public const CAMPAIGN_SUMMER_SALE_REFERENCE = 'campaign-summer-sale';

    /**
     * 将活动数据固件加载到数据库中
     *
     * @param ObjectManager $manager 对象管理器
     */
    public function load(ObjectManager $manager): void
    {
        $springFestival = new Campaign();
        $springFestival->setCode('SPRING2024');
        $springFestival->setName('春节大促销');
        $springFestival->setValid(true);
        $springFestival->setStartTime(CarbonImmutable::now());
        $springFestival->setEndTime(CarbonImmutable::now()->addDays(30));

        $manager->persist($springFestival);
        $this->addReference(self::CAMPAIGN_SPRING_FESTIVAL_REFERENCE, $springFestival);

        $summerSale = new Campaign();
        $summerSale->setCode('SUMMER2024');
        $summerSale->setName('夏日特惠');
        $summerSale->setValid(false);
        $summerSale->setStartTime(CarbonImmutable::now()->addDays(60));
        $summerSale->setEndTime(CarbonImmutable::now()->addDays(90));

        $manager->persist($summerSale);
        $this->addReference(self::CAMPAIGN_SUMMER_SALE_REFERENCE, $summerSale);

        $manager->flush();
    }
}
