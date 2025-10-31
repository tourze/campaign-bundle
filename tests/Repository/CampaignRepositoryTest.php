<?php

namespace CampaignBundle\Tests\Repository;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Repository\CampaignRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CampaignRepository::class)]
#[RunTestsInSeparateProcesses]
final class CampaignRepositoryTest extends AbstractRepositoryTestCase
{
    private CampaignRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(CampaignRepository::class);
    }

    public function testSave(): void
    {
        $campaign = new Campaign();
        $campaign->setName('Test Campaign for Save');
        $campaign->setStartTime(new \DateTimeImmutable('-1 day'));
        $campaign->setEndTime(new \DateTimeImmutable('+1 day'));

        $this->repository->save($campaign);

        $this->assertGreaterThan(0, $campaign->getId());
        $entityManager = self::getEntityManager();
        $this->assertTrue($entityManager->contains($campaign));
    }

    public function testRemove(): void
    {
        $entityManager = self::getEntityManager();

        $campaign = new Campaign();
        $campaign->setName('Test Campaign for Remove');
        $campaign->setStartTime(new \DateTimeImmutable('-1 day'));
        $campaign->setEndTime(new \DateTimeImmutable('+1 day'));
        $entityManager->persist($campaign);
        $entityManager->flush();

        $id = $campaign->getId();
        $this->repository->remove($campaign);

        $removedCampaign = $this->repository->find($id);
        $this->assertNull($removedCampaign);
    }

    /** @return CampaignRepository */
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

        return $campaign;
    }
}
