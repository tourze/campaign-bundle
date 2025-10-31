<?php

namespace CampaignBundle\Tests\Repository;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Chance;
use CampaignBundle\Repository\ChanceRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(ChanceRepository::class)]
#[RunTestsInSeparateProcesses]
final class ChanceRepositoryTest extends AbstractRepositoryTestCase
{
    private ChanceRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ChanceRepository::class);
    }

    public function testCountTotalChanceByCampaignAndUser(): void
    {
        $entityManager = self::getEntityManager();

        $campaign = new Campaign();
        $campaign->setName('Test Campaign');
        $campaign->setStartTime(new \DateTimeImmutable('-1 day'));
        $campaign->setEndTime(new \DateTimeImmutable('+1 day'));
        $entityManager->persist($campaign);

        $user = $this->createNormalUser('test@example.com', 'password123');

        $chance = new Chance();
        $chance->setCampaign($campaign);
        $chance->setUser($user);
        $chance->setStartTime(new \DateTimeImmutable('-1 hour'));
        $chance->setExpireTime(new \DateTimeImmutable('+1 hour'));
        $chance->setValid(true);
        $entityManager->persist($chance);

        $entityManager->flush();

        $count = $this->repository->countTotalChanceByCampaignAndUser($campaign, $user);
        $this->assertSame(1, $count);
    }

    public function testSave(): void
    {
        $entityManager = self::getEntityManager();

        $campaign = new Campaign();
        $campaign->setName('Test Campaign');
        $campaign->setStartTime(new \DateTimeImmutable('-1 day'));
        $campaign->setEndTime(new \DateTimeImmutable('+1 day'));
        $entityManager->persist($campaign);

        $user = $this->createNormalUser('save@example.com', 'password123');
        $entityManager->flush();

        $chance = new Chance();
        $chance->setCampaign($campaign);
        $chance->setUser($user);
        $chance->setStartTime(new \DateTimeImmutable('-1 hour'));
        $chance->setExpireTime(new \DateTimeImmutable('+1 hour'));
        $chance->setValid(true);

        $this->repository->save($chance);

        $this->assertNotNull($chance->getId());
        $this->assertTrue($entityManager->contains($chance));
    }

    public function testRemove(): void
    {
        $entityManager = self::getEntityManager();

        $campaign = new Campaign();
        $campaign->setName('Test Campaign');
        $campaign->setStartTime(new \DateTimeImmutable('-1 day'));
        $campaign->setEndTime(new \DateTimeImmutable('+1 day'));
        $entityManager->persist($campaign);

        $user = $this->createNormalUser('remove@example.com', 'password123');

        $chance = new Chance();
        $chance->setCampaign($campaign);
        $chance->setUser($user);
        $chance->setStartTime(new \DateTimeImmutable('-1 hour'));
        $chance->setExpireTime(new \DateTimeImmutable('+1 hour'));
        $chance->setValid(true);
        $entityManager->persist($chance);
        $entityManager->flush();

        $id = $chance->getId();
        $this->repository->remove($chance);

        $removedChance = $this->repository->find($id);
        $this->assertNull($removedChance);
    }

    /** @return ChanceRepository */
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

        $chance = new Chance();
        $chance->setCampaign($campaign);
        $chance->setUser($user);
        $chance->setStartTime(new \DateTimeImmutable('-1 hour'));
        $chance->setExpireTime(new \DateTimeImmutable('+1 hour'));
        $chance->setValid(true);

        return $chance;
    }
}
