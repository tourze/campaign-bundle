<?php

declare(strict_types=1);

namespace CampaignBundle\Tests\Service;

use CampaignBundle\Contract\RewardProcessorInterface;
use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Service\RewardProcessorRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * RewardProcessorRegistry 集成测试
 *
 * @internal
 */
#[CoversClass(RewardProcessorRegistry::class)]
#[RunTestsInSeparateProcesses]
final class RewardProcessorRegistryTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试设置逻辑
    }

    public function testServiceExists(): void
    {
        $registry = self::getService(RewardProcessorRegistry::class);
        $this->assertInstanceOf(RewardProcessorRegistry::class, $registry);
    }

    public function testRegistryMethods(): void
    {
        $registry = self::getService(RewardProcessorRegistry::class);

        // 验证所有方法存在且可调用
        $this->assertTrue(method_exists($registry, 'getProcessor'));
        $this->assertTrue(method_exists($registry, 'hasProcessor'));
        $this->assertTrue(method_exists($registry, 'getAllProcessors'));
        $this->assertTrue(method_exists($registry, 'getProcessors'));
    }

    public function testGetAllProcessorsReturnsArray(): void
    {
        $registry = self::getService(RewardProcessorRegistry::class);

        $result = $registry->getAllProcessors();
        $this->assertIsArray($result);
    }

    public function testGetProcessorsReturnsArray(): void
    {
        $registry = self::getService(RewardProcessorRegistry::class);

        $result = $registry->getProcessors(AwardType::COUPON);
        $this->assertIsArray($result);
    }

    public function testHasProcessorReturnsBool(): void
    {
        $registry = self::getService(RewardProcessorRegistry::class);

        $result = $registry->hasProcessor(AwardType::COUPON);
        $this->assertIsBool($result);
    }

    public function testGetProcessorReturnsNullOrProcessor(): void
    {
        $registry = self::getService(RewardProcessorRegistry::class);

        $result = $registry->getProcessor(AwardType::COUPON);
        $this->assertTrue(
            null === $result || $result instanceof RewardProcessorInterface,
            'getProcessor 应该返回 null 或 RewardProcessorInterface'
        );
    }
}
