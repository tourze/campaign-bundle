<?php

declare(strict_types=1);

namespace CampaignBundle\DataFixtures;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Enum\AwardType;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 测试和开发环境用的活动和奖励实体基础数据固件
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class BasicCampaignFixtures extends Fixture
{
    public const CAMPAIGN_TYJG_REFERENCE = 'campaign-tyjg';
    public const AWARD_TYJG_PHONE_REFERENCE = 'award-tyjg-phone';

    /**
     * 将基础活动和奖励数据固件加载到数据库中
     *
     * @param ObjectManager $manager 对象管理器
     */
    public function load(ObjectManager $manager): void
    {
        $campaign = new Campaign();
        $campaign->setCode('TYJG');
        $campaign->setName('托运进港');
        $campaign->setValid(true);
        $campaign->setStartTime(CarbonImmutable::now());
        $campaign->setEndTime(CarbonImmutable::now()->subYears(10));

        $manager->persist($campaign);
        $this->addReference(self::CAMPAIGN_TYJG_REFERENCE, $campaign);

        $award = new Award();
        $award->setCampaign($campaign);
        $award->setEvent('get-phone');
        $award->setType(AwardType::SPU_QUALIFICATION);
        $award->setValue('1');

        $manager->persist($award);
        $this->addReference(self::AWARD_TYJG_PHONE_REFERENCE, $award);

        $manager->flush();
    }
}
