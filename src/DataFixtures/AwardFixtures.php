<?php

declare(strict_types=1);

namespace CampaignBundle\DataFixtures;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Enum\AwardType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AwardFixtures extends Fixture implements DependentFixtureInterface
{
    public const AWARD_CREDIT_REFERENCE = 'award-credit';
    public const AWARD_COUPON_REFERENCE = 'award-coupon';

    public function load(ObjectManager $manager): void
    {
        $campaign = $this->getReference(BasicCampaignFixtures::CAMPAIGN_TYJG_REFERENCE, Campaign::class);

        $creditAward = new Award();
        $creditAward->setCampaign($campaign);
        $creditAward->setEvent('daily-checkin');
        $creditAward->setType(AwardType::CREDIT);
        $creditAward->setValue('100');

        $manager->persist($creditAward);
        $this->addReference(self::AWARD_CREDIT_REFERENCE, $creditAward);

        $couponAward = new Award();
        $couponAward->setCampaign($campaign);
        $couponAward->setEvent('share-complete');
        $couponAward->setType(AwardType::COUPON);
        $couponAward->setValue('DISCOUNT10');

        $manager->persist($couponAward);
        $this->addReference(self::AWARD_COUPON_REFERENCE, $couponAward);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BasicCampaignFixtures::class,
        ];
    }
}
