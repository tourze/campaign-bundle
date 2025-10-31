<?php

namespace CampaignBundle\Tests\Repository;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\EventLog;
use CampaignBundle\Repository\EventLogRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(EventLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class EventLogRepositoryTest extends AbstractRepositoryTestCase
{
    private EventLogRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(EventLogRepository::class);
    }

    public function testRepository(): void
    {
        $this->assertInstanceOf(EventLogRepository::class, $this->repository);
    }

    public function testSave(): void
    {
        $entityManager = self::getEntityManager();

        $campaign = new Campaign();
        $campaign->setName('Test Campaign');
        $campaign->setStartTime(new \DateTimeImmutable('-1 day'));
        $campaign->setEndTime(new \DateTimeImmutable('+1 day'));
        $entityManager->persist($campaign);

        $user = $this->createNormalUser('eventlog@example.com', 'password123');
        $entityManager->flush();

        $eventLog = new EventLog();
        $eventLog->setCampaign($campaign);
        $eventLog->setUser($user);
        $eventLog->setEvent('test_event');
        $eventLog->setParams(['key' => 'value']);

        $this->repository->save($eventLog);

        $this->assertNotNull($eventLog->getId());
        $this->assertTrue($entityManager->contains($eventLog));
    }

    public function testRemove(): void
    {
        $entityManager = self::getEntityManager();

        $campaign = new Campaign();
        $campaign->setName('Test Campaign');
        $campaign->setStartTime(new \DateTimeImmutable('-1 day'));
        $campaign->setEndTime(new \DateTimeImmutable('+1 day'));
        $entityManager->persist($campaign);

        $user = $this->createNormalUser('eventlogremove@example.com', 'password123');

        $eventLog = new EventLog();
        $eventLog->setCampaign($campaign);
        $eventLog->setUser($user);
        $eventLog->setEvent('test_event');
        $eventLog->setParams(['key' => 'value']);
        $entityManager->persist($eventLog);
        $entityManager->flush();

        $id = $eventLog->getId();
        $this->repository->remove($eventLog);

        $removedEventLog = $this->repository->find($id);
        $this->assertNull($removedEventLog);
    }

    /** @return EventLogRepository */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $campaign = new Campaign();
        $campaign->setName('Test Campaign');
        $campaign->setCode('TEST_CAMPAIGN_' . uniqid());
        $campaign->setValid(true);
        $campaign->setStartTime(new \DateTimeImmutable());
        $campaign->setEndTime(new \DateTimeImmutable('+1 month'));

        $user = $this->createNormalUser('testuser@example.com', 'password123');

        $eventLog = new EventLog();
        $eventLog->setCampaign($campaign);
        $eventLog->setUser($user);
        $eventLog->setEvent('test_event');
        $eventLog->setParams(['key' => 'value']);

        return $eventLog;
    }
}
