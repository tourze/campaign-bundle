<?php

namespace CampaignBundle\Tests\Service;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Limit;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Enum\LimitType;
use CampaignBundle\Service\CampaignLimitService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 活动限制操作服务测试类
 * @internal
 */
#[CoversClass(CampaignLimitService::class)]
#[RunTestsInSeparateProcesses]
final class CampaignLimitServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 初始化逻辑在父类中处理
    }

    public function testServiceInstantiation(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignLimitService $service */
        $service = self::getContainer()->get(CampaignLimitService::class);

        $this->assertInstanceOf(CampaignLimitService::class, $service);
    }

    public function testCheckLimitWithChanceType(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignLimitService $service */
        $service = self::getContainer()->get(CampaignLimitService::class);

        $user = $this->createNormalUser('test_user_' . uniqid(), 'password');
        $limit = $this->createTestLimit(LimitType::CHANCE);

        // Create a test award and campaign for the limit
        $campaign = $this->createTestCampaign();
        $award = $this->createTestAward();
        $award->setCampaign($campaign);
        $limit->setAward($award);

        $result = $service->checkLimit($user, $limit);

        // Should return false when no valid chance exists
        $this->assertFalse($result);
    }

    public function testCheckLimitWithUserTagType(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignLimitService $service */
        $service = self::getContainer()->get(CampaignLimitService::class);

        $user = $this->createUserStub('test_user_' . uniqid());
        $limit = $this->createTestLimit(LimitType::USER_TAG);
        $limit->setValue('test_tag');

        $result = $service->checkLimit($user, $limit);

        // Should return false when user doesn't have the required tag
        $this->assertFalse($result);
    }

    public function testConsumeLimitWithChanceType(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignLimitService $service */
        $service = self::getContainer()->get(CampaignLimitService::class);

        $user = $this->createNormalUser('test_user_' . uniqid(), 'password');
        $limit = $this->createTestLimit(LimitType::CHANCE);

        // Create a test award and campaign for the limit
        $campaign = $this->createTestCampaign();
        $award = $this->createTestAward();
        $award->setCampaign($campaign);
        $limit->setAward($award);

        $result = $service->consumeLimit($user, $limit);

        // Should return false when no valid chance exists to consume
        $this->assertFalse($result);
    }

    public function testConsumeLimitWithUserTagType(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignLimitService $service */
        $service = self::getContainer()->get(CampaignLimitService::class);

        $user = $this->createUserStub('test_user_' . uniqid());
        $limit = $this->createTestLimit(LimitType::USER_TAG);
        $limit->setValue('test_tag');

        $result = $service->consumeLimit($user, $limit);

        // Should return true for user tag type (no consumption needed)
        $this->assertTrue($result);
    }

    public function testCheckLimitWithNullAward(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignLimitService $service */
        $service = self::getContainer()->get(CampaignLimitService::class);

        $user = $this->createUserStub('test_user_' . uniqid());
        $limit = $this->createTestLimit(LimitType::CHANCE);
        // No award set - should return false

        $result = $service->checkLimit($user, $limit);

        $this->assertFalse($result);
    }

    public function testConsumeLimitWithNullAward(): void
    {
        // Use container to get service instance following integration test principles
        /** @var CampaignLimitService $service */
        $service = self::getContainer()->get(CampaignLimitService::class);

        $user = $this->createUserStub('test_user_' . uniqid());
        $limit = $this->createTestLimit(LimitType::CHANCE);
        // No award set - should return false

        $result = $service->consumeLimit($user, $limit);

        $this->assertFalse($result);
    }

    /**
     * 创建测试限制实例
     */
    private function createTestLimit(LimitType $type = LimitType::CHANCE): Limit
    {
        $limit = new Limit();
        $limit->setType($type);
        $limit->setValue('test_value');

        return $limit;
    }

    /**
     * 创建测试奖励实例
     */
    private function createTestAward(): Award
    {
        $award = new Award();
        $award->setType(AwardType::COUPON);
        $award->setValue('test_coupon_123');
        $award->setTimes(1);
        $award->setPrizeQuantity(10);

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
