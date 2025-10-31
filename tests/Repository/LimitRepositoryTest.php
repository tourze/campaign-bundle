<?php

namespace CampaignBundle\Tests\Repository;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Limit;
use CampaignBundle\Enum\AwardLimitType;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Enum\LimitType;
use CampaignBundle\Repository\LimitRepository;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(LimitRepository::class)]
#[RunTestsInSeparateProcesses]
final class LimitRepositoryTest extends AbstractRepositoryTestCase
{
    private LimitRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(LimitRepository::class);
    }

    public function testRepository(): void
    {
        $this->assertInstanceOf(LimitRepository::class, $this->repository);
    }

    public function testFindAll(): void
    {
        $limits = $this->repository->findAll();
        $this->assertGreaterThanOrEqual(0, count($limits));
    }

    public function testSave(): void
    {
        $campaign = $this->createCampaign();
        $award = $this->createAward($campaign);
        self::getEntityManager()->persist($campaign);
        self::getEntityManager()->persist($award);
        self::getEntityManager()->flush();

        $limit = $this->createLimit($award, LimitType::CHANCE, '5');
        $limit->setRemark('每日限制五次');

        $this->repository->save($limit, true);

        $this->assertTrue($limit->getId() > 0);

        $savedLimit = $this->repository->find($limit->getId());
        $this->assertInstanceOf(Limit::class, $savedLimit);
        $this->assertEquals(LimitType::CHANCE, $savedLimit->getType());
        $this->assertEquals('5', $savedLimit->getValue());
        $this->assertEquals('每日限制五次', $savedLimit->getRemark());
    }

    public function testRemove(): void
    {
        $campaign = $this->createCampaign();
        $award = $this->createAward($campaign);
        self::getEntityManager()->persist($campaign);
        self::getEntityManager()->persist($award);

        $limit = $this->createLimit($award, LimitType::USER_TAG, 'newbie');
        self::getEntityManager()->persist($limit);
        self::getEntityManager()->flush();

        $limitId = $limit->getId();
        $this->assertNotNull($this->repository->find($limitId));

        $this->repository->remove($limit, true);

        $this->assertNull($this->repository->find($limitId));
    }

    private function createCampaign(): Campaign
    {
        $campaign = new Campaign();
        $campaign->setCode('TEST_CAMPAIGN_' . uniqid());
        $campaign->setName('测试活动');
        $campaign->setStartTime(CarbonImmutable::now());
        $campaign->setEndTime(CarbonImmutable::now()->addDays(7));
        $campaign->setValid(true);

        return $campaign;
    }

    private function createAward(Campaign $campaign): Award
    {
        $award = new Award();
        $award->setCampaign($campaign);
        $award->setEvent('login');
        $award->setType(AwardType::CREDIT);
        $award->setValue('100');
        $award->setAwardLimitType(AwardLimitType::BUY_TOTAL);
        $award->setTimes(1);
        $award->setPrizeQuantity(50);

        return $award;
    }

    private function createLimit(?Award $award, LimitType $type, string $value): Limit
    {
        $limit = new Limit();
        $limit->setAward($award);
        $limit->setType($type);
        $limit->setValue($value);

        return $limit;
    }

    /** @return LimitRepository */
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

        $award = new Award();
        $award->setCampaign($campaign);
        $award->setEvent('test_event');
        $award->setType(AwardType::CREDIT);
        $award->setValue('100');
        $award->setAwardLimitType(AwardLimitType::BUY_TOTAL);
        $award->setTimes(1);
        $award->setPrizeQuantity(10);

        // Persist related entities first
        self::getEntityManager()->persist($campaign);
        self::getEntityManager()->persist($award);

        $limit = new Limit();
        $limit->setAward($award);
        $limit->setType(LimitType::USER_TAG);
        $limit->setValue('test_value');

        return $limit;
    }
}
