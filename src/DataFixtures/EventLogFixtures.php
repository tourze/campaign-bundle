<?php

declare(strict_types=1);

namespace CampaignBundle\DataFixtures;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\EventLog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserServiceContracts\UserManagerInterface;

#[When(env: 'test')]
#[When(env: 'dev')]
class EventLogFixtures extends Fixture implements DependentFixtureInterface
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

        $eventLog1 = new EventLog();
        $eventLog1->setCampaign($campaign);
        $eventLog1->setUser($user);
        $eventLog1->setEvent('campaign_join');
        $eventLog1->setParams(['source' => 'test', 'type' => 'join']);
        $manager->persist($eventLog1);

        $eventLog2 = new EventLog();
        $eventLog2->setCampaign($campaign);
        $eventLog2->setUser($user);
        $eventLog2->setEvent('campaign_complete');
        $eventLog2->setParams(['source' => 'test', 'type' => 'complete', 'result' => 'success']);
        $manager->persist($eventLog2);

        $manager->flush();
    }

    private function getOrCreateTestUser(): UserInterface
    {
        // 尝试加载已存在的用户
        $user = $this->userManager->loadUserByIdentifier('eventlog-test-user');

        // 如果用户不存在，创建一个新的测试用户
        if (null === $user) {
            $user = $this->userManager->createUser('eventlog-test-user', '事件日志测试用户');
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
