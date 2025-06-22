<?php

namespace CampaignBundle\DataFixtures;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Repository\AwardRepository;
use CampaignBundle\Repository\CampaignRepository;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BasicCampaignFixture extends Fixture
{
    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly AwardRepository $awardRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $campaign = $this->campaignRepository->findOneBy(['code' => 'TYJG']);
        if ($campaign === null) {
            $campaign = new Campaign();
            $campaign->setCode('TYJG');
            $campaign->setName('托运进港');
            $campaign->setValid(true);
            $campaign->setStartTime(CarbonImmutable::now());
            $campaign->setEndTime(CarbonImmutable::now()->subYears(10));
        }

        $manager->persist($campaign);

        $awards = $this->awardRepository->findBy(['campaign' => $campaign]);
        if (empty($awards)) {
            $awards = new Award();
            $awards->setCampaign($campaign);
            $awards->setEvent('get-phone');
            $awards->setType(AwardType::SPU_QUALIFICATION);
            $awards->setValue('1');
            $manager->persist($awards);
        }

        $manager->flush();
    }
}
