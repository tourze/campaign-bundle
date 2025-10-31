<?php

namespace CampaignBundle\Tests\Repository;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Enum\AwardLimitType;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Repository\AwardRepository;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(AwardRepository::class)]
#[RunTestsInSeparateProcesses]
final class AwardRepositoryTest extends AbstractRepositoryTestCase
{
    private AwardRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(AwardRepository::class);
    }

    public function testSave(): void
    {
        $campaign = $this->createCampaign();
        self::getEntityManager()->persist($campaign);
        self::getEntityManager()->flush();

        $award = $this->createAward($campaign, 'purchase', AwardType::COUPON_LOCAL, 'LOCAL001');

        $this->repository->save($award, true);

        $this->assertTrue($award->getId() > 0);

        $savedAward = $this->repository->find($award->getId());
        $this->assertInstanceOf(Award::class, $savedAward);
        $this->assertEquals('purchase', $savedAward->getEvent());
        $this->assertEquals(AwardType::COUPON_LOCAL, $savedAward->getType());
        $this->assertEquals('LOCAL001', $savedAward->getValue());
    }

    public function testRemove(): void
    {
        $campaign = $this->createCampaign();
        self::getEntityManager()->persist($campaign);

        $award = $this->createAward($campaign, 'review', AwardType::SPU_QUALIFICATION, 'SPU001');
        self::getEntityManager()->persist($award);
        self::getEntityManager()->flush();

        $awardId = $award->getId();
        $this->assertNotNull($this->repository->find($awardId));

        $this->repository->remove($award, true);

        $this->assertNull($this->repository->find($awardId));
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

    private function createAward(Campaign $campaign, string $event, AwardType $type, string $value): Award
    {
        $award = new Award();
        $award->setCampaign($campaign);
        $award->setEvent($event);
        $award->setType($type);
        $award->setValue($value);
        $award->setAwardLimitType(AwardLimitType::BUY_TOTAL);
        $award->setTimes(1);
        $award->setPrizeQuantity(100);

        return $award;
    }

    /** @return AwardRepository */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        // 创建一个简单的 campaign 实体，设置所有必需的字段
        $campaign = new Campaign();
        $campaign->setName('Test Campaign');
        $campaign->setCode('TEST_CAMPAIGN_' . uniqid());
        $campaign->setStartTime(new \DateTimeImmutable());
        $campaign->setEndTime(new \DateTimeImmutable('+1 month'));
        $campaign->setValid(true);

        // 创建 award 实体并关联 campaign
        $award = new Award();
        $award->setCampaign($campaign);
        $award->setEvent('test_event');
        $award->setType(AwardType::CREDIT);
        $award->setValue('100');
        $award->setAwardLimitType(AwardLimitType::BUY_TOTAL);
        $award->setTimes(1);
        $award->setPrizeQuantity(10);

        return $award;
    }
}
