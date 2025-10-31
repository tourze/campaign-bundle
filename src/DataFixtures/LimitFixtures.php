<?php

declare(strict_types=1);

namespace CampaignBundle\DataFixtures;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Limit;
use CampaignBundle\Enum\LimitType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 测试和开发环境用的限制实体数据固件
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class LimitFixtures extends Fixture implements DependentFixtureInterface
{
    public const LIMIT_USER_TAG_REFERENCE = 'limit-user-tag';
    public const LIMIT_CHANCE_REFERENCE = 'limit-chance';

    /**
     * 将限制数据固件加载到数据库中
     *
     * @param ObjectManager $manager 对象管理器
     */
    public function load(ObjectManager $manager): void
    {
        $creditAward = $this->getReference(AwardFixtures::AWARD_CREDIT_REFERENCE, Award::class);
        $couponAward = $this->getReference(AwardFixtures::AWARD_COUPON_REFERENCE, Award::class);

        $userTagLimit = new Limit();
        $userTagLimit->setAward($creditAward);
        $userTagLimit->setType(LimitType::USER_TAG);
        $userTagLimit->setValue('vip,premium');
        $userTagLimit->setRemark('仅限VIP和高级用户');

        $manager->persist($userTagLimit);
        $this->addReference(self::LIMIT_USER_TAG_REFERENCE, $userTagLimit);

        $chanceLimit = new Limit();
        $chanceLimit->setAward($couponAward);
        $chanceLimit->setType(LimitType::CHANCE);
        $chanceLimit->setValue('3');
        $chanceLimit->setRemark('每日最多使用3次机会');

        $manager->persist($chanceLimit);
        $this->addReference(self::LIMIT_CHANCE_REFERENCE, $chanceLimit);

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
            AwardFixtures::class,
        ];
    }
}
