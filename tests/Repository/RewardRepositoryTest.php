<?php

namespace CampaignBundle\Tests\Repository;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardLimitType;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Repository\RewardRepository;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(RewardRepository::class)]
#[RunTestsInSeparateProcesses]
final class RewardRepositoryTest extends AbstractRepositoryTestCase
{
    private RewardRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(RewardRepository::class);
    }

    public function testRepository(): void
    {
        $this->assertInstanceOf(RewardRepository::class, $this->repository);
    }

    public function testFindAll(): void
    {
        $rewards = $this->repository->findAll();
        $this->assertGreaterThanOrEqual(0, count($rewards));
    }

    public function testSave(): void
    {
        $campaign = $this->createCampaign();
        $award = $this->createAward($campaign);

        self::getEntityManager()->persist($campaign);
        self::getEntityManager()->persist($award);
        self::getEntityManager()->flush();

        $reward = $this->createReward($campaign, $award, 'SAVE001');
        $reward->setRemark('测试奖励');
        $reward->setBusinessChannel('web');

        $this->repository->save($reward, true);

        $this->assertNotNull($reward->getId());
        $this->assertNotEquals('0', $reward->getId());

        $savedReward = $this->repository->find($reward->getId());
        $this->assertInstanceOf(Reward::class, $savedReward);
        $this->assertEquals('SAVE001', $savedReward->getSn());
        $this->assertEquals('测试奖励', $savedReward->getRemark());
        $this->assertEquals('web', $savedReward->getBusinessChannel());
    }

    public function testRemove(): void
    {
        $campaign = $this->createCampaign();
        $award = $this->createAward($campaign);

        self::getEntityManager()->persist($campaign);
        self::getEntityManager()->persist($award);

        $reward = $this->createReward($campaign, $award, 'DEL001');
        self::getEntityManager()->persist($reward);
        self::getEntityManager()->flush();

        $rewardId = $reward->getId();
        $this->assertNotNull($this->repository->find($rewardId));

        $this->repository->remove($reward, true);

        $this->assertNull($this->repository->find($rewardId));
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
        $award->setEvent('signin');
        $award->setType(AwardType::CREDIT);
        $award->setValue('100');
        $award->setAwardLimitType(AwardLimitType::BUY_TOTAL);
        $award->setTimes(1);
        $award->setPrizeQuantity(10);

        return $award;
    }

    private function createReward(Campaign $campaign, Award $award, string $sn): Reward
    {
        $reward = new Reward();
        $reward->setCampaign($campaign);
        $reward->setAward($award);
        $reward->setSn($sn);
        $reward->setType(AwardType::CREDIT);
        $reward->setValue('100');
        $reward->setValid(true);

        return $reward;
    }

    /** @return RewardRepository */
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

        $reward = new Reward();
        $reward->setCampaign($campaign);
        $reward->setAward($award);
        $reward->setSn('TEST_' . uniqid());
        $reward->setType(AwardType::CREDIT);
        $reward->setValue('100');
        $reward->setValid(true);

        return $reward;
    }
}
