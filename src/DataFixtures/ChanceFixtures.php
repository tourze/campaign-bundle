<?php

declare(strict_types=1);

namespace CampaignBundle\DataFixtures;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Chance;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserServiceContracts\UserManagerInterface;

#[When(env: 'test')]
#[When(env: 'dev')]
class ChanceFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserManagerInterface $userManager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $campaign = $this->getReference(CampaignFixtures::CAMPAIGN_SPRING_FESTIVAL_REFERENCE, Campaign::class);

        // 获取或创建测试用户
        $user = $this->getOrCreateTestUser();

        $chance1 = new Chance();
        $chance1->setCampaign($campaign);
        $chance1->setUser($user);
        $chance1->setStartTime(CarbonImmutable::now());
        $chance1->setExpireTime(CarbonImmutable::now()->addDays(7));
        $chance1->setValid(true);
        $chance1->setContext(['source' => 'test']);
        $manager->persist($chance1);

        $chance2 = new Chance();
        $chance2->setCampaign($campaign);
        $chance2->setUser($user);
        $chance2->setStartTime(CarbonImmutable::now()->subDay());
        $chance2->setExpireTime(CarbonImmutable::now()->addDays(3));
        $chance2->setValid(false);
        $chance2->setContext(['source' => 'test', 'type' => 'expired']);
        $manager->persist($chance2);

        $manager->flush();
    }

    private function getOrCreateTestUser(): UserInterface
    {
        // 尝试加载已存在的用户
        $user = $this->userManager->loadUserByIdentifier('chance-test-user');

        // 如果用户不存在，创建一个新的测试用户
        if (null === $user) {
            $user = $this->userManager->createUser('chance-test-user', '机会测试用户');
            $this->userManager->saveUser($user);
        }

        return $user;
    }

    public function getDependencies(): array
    {
        return [
            CampaignFixtures::class,
        ];
    }
}
