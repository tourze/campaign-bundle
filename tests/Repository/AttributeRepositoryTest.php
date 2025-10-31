<?php

namespace CampaignBundle\Tests\Repository;

use CampaignBundle\Entity\Attribute;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Repository\AttributeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(AttributeRepository::class)]
#[RunTestsInSeparateProcesses]
final class AttributeRepositoryTest extends AbstractRepositoryTestCase
{
    private AttributeRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(AttributeRepository::class);
    }

    public function testSave(): void
    {
        $entityManager = self::getEntityManager();
        $attribute = new Attribute();
        $attribute->setName('测试属性');
        $attribute->setValue('测试值');

        $campaign = new Campaign();
        $campaign->setName('测试活动');
        $campaign->setCode('TEST_CAMPAIGN_' . time() . '_' . random_int(1000, 9999));
        $campaign->setValid(true);
        $campaign->setStartTime(new \DateTimeImmutable());
        $campaign->setEndTime(new \DateTimeImmutable('+1 month'));
        $entityManager->persist($campaign);

        $attribute->setCampaign($campaign);

        $this->repository->save($attribute);

        $this->assertGreaterThan(0, $attribute->getId());
        $this->assertTrue($entityManager->contains($attribute));
    }

    public function testRemove(): void
    {
        $entityManager = self::getEntityManager();
        $attribute = new Attribute();
        $attribute->setName('测试属性');
        $attribute->setValue('测试值');

        $campaign = new Campaign();
        $campaign->setName('测试活动');
        $campaign->setCode('TEST_CAMPAIGN_' . time() . '_' . random_int(1000, 9999));
        $campaign->setValid(true);
        $campaign->setStartTime(new \DateTimeImmutable());
        $campaign->setEndTime(new \DateTimeImmutable('+1 month'));
        $entityManager->persist($campaign);

        $attribute->setCampaign($campaign);
        $this->repository->save($attribute);

        $id = $attribute->getId();
        $this->repository->remove($attribute);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    /** @return AttributeRepository */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $entityManager = self::getEntityManager();

        $attribute = new Attribute();
        $attribute->setName('测试属性_' . uniqid());
        $attribute->setValue('测试值_' . uniqid());
        $attribute->setRemark('测试备注_' . uniqid());

        $campaign = new Campaign();
        $campaign->setName('测试活动_' . uniqid());
        $campaign->setCode('TEST_CAMPAIGN_' . uniqid());
        $campaign->setValid(true);
        $campaign->setStartTime(new \DateTimeImmutable());
        $campaign->setEndTime(new \DateTimeImmutable('+1 month'));
        $entityManager->persist($campaign);

        $attribute->setCampaign($campaign);

        return $attribute;
    }
}
