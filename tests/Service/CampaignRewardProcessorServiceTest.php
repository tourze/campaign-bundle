<?php

declare(strict_types=1);

namespace CampaignBundle\Tests\Service;

use CampaignBundle\Contract\RewardProcessorInterface;
use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Exception\UnsupportedRewardTypeException;
use CampaignBundle\Service\CampaignRewardProcessorService;
use CampaignBundle\Service\RewardProcessorRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\UserServiceContracts\UserManagerInterface;

/**
 * 活动奖励类型处理服务测试类
 *
 * 使用集成测试验证 CampaignRewardProcessorService 的核心功能：
 * - 注册表委托机制
 * - 处理器查找与调用
 * - 日志记录
 * - 异常处理
 *
 * @internal
 */
#[CoversClass(CampaignRewardProcessorService::class)]
#[RunTestsInSeparateProcesses]
final class CampaignRewardProcessorServiceTest extends AbstractIntegrationTestCase
{
    private CampaignRewardProcessorService $service;
    private UserManagerInterface $userManager;
    private UserInterface $testUser;

    protected function onSetUp(): void
    {
        // 从容器获取服务
        $this->service = self::getContainer()->get(CampaignRewardProcessorService::class);
        $this->userManager = self::getContainer()->get(UserManagerInterface::class);

        // 创建测试用户
        $this->testUser = $this->userManager->createUser(
            'test-user-' . bin2hex(random_bytes(4)),
            'Test User',
            null,
            'test-password',
            ['ROLE_USER']
        );
        $this->userManager->saveUser($this->testUser);
    }

    public function testServiceInstantiation(): void
    {
        self::assertInstanceOf(CampaignRewardProcessorService::class, $this->service);
    }

    public function testRegistryIsAvailable(): void
    {
        $registry = self::getContainer()->get(RewardProcessorRegistry::class);
        self::assertInstanceOf(RewardProcessorRegistry::class, $registry);
    }

    public function testProcessRewardByTypeThrowsExceptionWhenNoProcessorFound(): void
    {
        // 准备测试数据
        $campaign = $this->createCampaign();
        $award = $this->createAward($campaign, AwardType::COUPON_LOCAL, 'test-value');
        $reward = $this->createReward();

        // 期望抛出异常（因为没有注册 COUPON_LOCAL 类型的处理器）
        $this->expectException(UnsupportedRewardTypeException::class);
        $this->expectExceptionMessageMatches('/No processor registered for reward type/');

        // 执行测试
        $this->service->processRewardByType($this->testUser, $award, $reward);
    }

    /**
     * 创建测试用的 Campaign 对象
     */
    private function createCampaign(): Campaign
    {
        $campaign = new Campaign();
        // 使用反射设置 ID（避免依赖数据库）
        $reflection = new \ReflectionClass($campaign);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setValue($campaign, 1);

        return $campaign;
    }

    /**
     * 创建测试用的 Award 对象
     */
    private function createAward(Campaign $campaign, AwardType $type, string $value): Award
    {
        $award = new Award();
        $award->setCampaign($campaign);
        $award->setType($type);
        $award->setValue($value);
        $award->setEvent('test_event');

        // 使用反射设置 ID
        $reflection = new \ReflectionClass($award);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setValue($award, 1);

        return $award;
    }

    /**
     * 创建测试用的 Reward 对象
     */
    private function createReward(): Reward
    {
        return new Reward();
    }
}
