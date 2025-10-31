<?php

namespace CampaignBundle\Tests\ExpressionLanguage\Function;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\ExpressionLanguage\Function\ChanceFunctionProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(ChanceFunctionProvider::class)]
#[RunTestsInSeparateProcesses]
final class ChanceFunctionProviderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // ExpressionLanguage 测试不需要特殊的设置
    }

    public function testCountTotalChanceByCampaignAndUser(): void
    {
        $container = self::getContainer();
        $provider = $container->get(ChanceFunctionProvider::class);
        self::assertInstanceOf(ChanceFunctionProvider::class, $provider);

        $entityManager = $container->get('doctrine.orm.entity_manager');

        $campaign = new Campaign();
        $campaign->setName('Test Campaign');
        $campaign->setStartTime(new \DateTimeImmutable('-1 day'));
        $campaign->setEndTime(new \DateTimeImmutable('+1 day'));
        $entityManager->persist($campaign);

        $user = $this->createNormalUser('testuser@example.com', 'password123');
        $entityManager->flush();

        $result = $provider->countTotalChanceByCampaignAndUser([], $campaign, $user);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }
}
