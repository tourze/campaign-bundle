<?php

namespace CampaignBundle\DataFixtures;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Enum\AwardType;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BasicCampaignFixture extends Fixture
{
    public const CAMPAIGN_TYJG_REFERENCE = 'campaign-tyjg';
    public const AWARD_TYJG_PHONE_REFERENCE = 'award-tyjg-phone';

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
