<?php

namespace CampaignBundle\Tests\Service;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardLimitType;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Exception\AwardUnavailableException;
use CampaignBundle\Service\CampaignRewardService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 活动奖励服务测试类
 * @internal
 */
#[CoversClass(CampaignRewardService::class)]
#[RunTestsInSeparateProcesses]
final class CampaignRewardServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 初始化逻辑在父类中处理
    }

    public function testServiceInstantiation(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignRewardService $service */
        $service = self::getContainer()->get(CampaignRewardService::class);

        $this->assertInstanceOf(CampaignRewardService::class, $service);
    }

    public function testValidateAwardAvailabilityWithValidAward(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignRewardService $service */
        $service = self::getContainer()->get(CampaignRewardService::class);

        $user = $this->createUserStub('test_user_' . uniqid());
        $award = $this->createTestAward();
        $award->setPrizeQuantity(10);
        $award->setTimes(0); // 无限制

        // Should not throw exception
        $service->validateAwardAvailability($award, $user);

        $this->assertInstanceOf(CampaignRewardService::class, $service);
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertInstanceOf(Award::class, $award);
    }

    public function testValidateAwardAvailabilityWithNoStock(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignRewardService $service */
        $service = self::getContainer()->get(CampaignRewardService::class);

        $user = $this->createUserStub('test_user_' . uniqid());
        $award = $this->createTestAward();
        $award->setPrizeQuantity(0); // 无库存

        $this->expectException(AwardUnavailableException::class);
        $this->expectExceptionMessage('奖品已领取完毕');

        $service->validateAwardAvailability($award, $user);
    }

    public function testCreateBaseReward(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignRewardService $service */
        $service = self::getContainer()->get(CampaignRewardService::class);

        $user = $this->createUserStub('test_user_' . uniqid());
        $award = $this->createTestAward();

        $reward = $service->createBaseReward($user, $award);

        $this->assertInstanceOf(Reward::class, $reward);
        $this->assertSame($user, $reward->getUser());
        $this->assertSame($award, $reward->getAward());
        $this->assertSame($award->getCampaign(), $reward->getCampaign());
        $this->assertSame($award->getType(), $reward->getType());
        $this->assertSame($award->getValue(), $reward->getValue());
        $this->assertTrue($reward->isValid());
        $this->assertNotEmpty($reward->getSn());
    }

    public function testSaveRewardAndUpdateAward(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignRewardService $service */
        $service = self::getContainer()->get(CampaignRewardService::class);

        $user = $this->createNormalUser('test_user_' . uniqid(), 'password');
        $award = $this->createTestAward();
        $initialQuantity = 10;
        $award->setPrizeQuantity($initialQuantity);

        $reward = $service->createBaseReward($user, $award);

        // Save reward and update award
        $service->saveRewardAndUpdateAward($reward, $award);

        // Verify award quantity was decremented
        $this->assertSame($initialQuantity - 1, $award->getPrizeQuantity());
    }

    /**
     * 创建测试奖励实例
     */
    private function createTestAward(): Award
    {
        $campaign = $this->createTestCampaign();

        $award = new Award();
        $award->setCampaign($campaign);
        $award->setType(AwardType::COUPON);
        $award->setValue('test_coupon_123');
        $award->setTimes(1);
        $award->setPrizeQuantity(10);
        $award->setAwardLimitType(AwardLimitType::BUY_TOTAL);

        return $award;
    }

    /**
     * 创建测试活动实例
     */
    private function createTestCampaign(): Campaign
    {
        $campaign = new Campaign();
        $campaign->setName('Test Campaign ' . uniqid());
        $campaign->setValid(true);
        $campaign->setStartTime(new \DateTimeImmutable('-1 day'));
        $campaign->setEndTime(new \DateTimeImmutable('+1 day'));

        return $campaign;
    }

    // InterfaceStub方法 - 简化测试中的接口实现

    /**
     * 创建UserInterface的简单stub实现
     *
     * @param non-empty-string $userIdentifier 用户标识符，默认为'test-user'
     * @param array<string> $roles 用户角色数组，默认为空数组
     */
    private function createUserStub(string $userIdentifier = 'test-user', array $roles = []): UserInterface
    {
        return new class($userIdentifier, $roles) implements UserInterface {
            /**
             * @param non-empty-string $userIdentifier
             * @param array<string> $roles
             */
            public function __construct(
                private string $userIdentifier,
                private array $roles,
            ) {
            }

            /**
             * @return array<string>
             */
            public function getRoles(): array
            {
                return $this->roles;
            }

            public function eraseCredentials(): void
            {
                // 空实现 - stub不需要真正的凭据管理
            }

            /**
             * @return non-empty-string
             */
            public function getUserIdentifier(): string
            {
                return $this->userIdentifier;
            }
        };
    }
}
