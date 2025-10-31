<?php

declare(strict_types=1);

namespace CampaignBundle\DataFixtures;

use BizUserBundle\Entity\BizUser;
use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class RewardFixtures extends Fixture implements DependentFixtureInterface
{
    public const REWARD_CREDIT_REFERENCE = 'reward-credit';
    public const REWARD_COUPON_REFERENCE = 'reward-coupon';

    public function load(ObjectManager $manager): void
    {
        $campaign = $this->getReference(BasicCampaignFixtures::CAMPAIGN_TYJG_REFERENCE, Campaign::class);
        $creditAward = $this->getReference(AwardFixtures::AWARD_CREDIT_REFERENCE, Award::class);
        $couponAward = $this->getReference(AwardFixtures::AWARD_COUPON_REFERENCE, Award::class);

        // 创建测试用户
        $user = new BizUser();
        $user->setUsername('test_user');
        $user->setEmail('test@test.local');
        $user->setPasswordHash('hashed_password');
        $manager->persist($user);

        // 创建积分奖励记录
        $creditReward = new Reward();
        $creditReward->setValid(true);
        $creditReward->setSn('REWARD-CREDIT-001');
        $creditReward->setCampaign($campaign);
        $creditReward->setAward($creditAward);
        $creditReward->setUser($user);
        $creditReward->setType(AwardType::CREDIT);
        $creditReward->setValue('100');

        $manager->persist($creditReward);
        $this->addReference(self::REWARD_CREDIT_REFERENCE, $creditReward);

        // 创建优惠券奖励记录
        $couponReward = new Reward();
        $couponReward->setValid(true);
        $couponReward->setSn('REWARD-COUPON-001');
        $couponReward->setCampaign($campaign);
        $couponReward->setAward($couponAward);
        $couponReward->setUser($user);
        $couponReward->setType(AwardType::COUPON);
        $couponReward->setValue('DISCOUNT10');

        $manager->persist($couponReward);
        $this->addReference(self::REWARD_COUPON_REFERENCE, $couponReward);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BasicCampaignFixtures::class,
            AwardFixtures::class,
        ];
    }
}
