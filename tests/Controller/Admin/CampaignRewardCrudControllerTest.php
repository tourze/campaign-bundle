<?php

namespace CampaignBundle\Tests\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignRewardCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CampaignRewardCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CampaignRewardCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @phpstan-ignore-next-line missingType.generics
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(CampaignRewardCrudController::class);
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '奖品序列号' => ['奖品序列号'];
        yield '所属活动' => ['所属活动'];
        yield '奖励配置' => ['奖励配置'];
        yield '获奖用户' => ['获奖用户'];
        yield '有效状态' => ['有效状态'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '奖品序列号' => ['sn'];
        yield '所属活动' => ['campaign'];
        yield '奖励配置' => ['award'];
        yield '获奖用户' => ['user'];
        yield '有效状态' => ['valid'];
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield '奖品序列号' => ['sn'];
        yield '所属活动' => ['campaign'];
        yield '奖励配置' => ['award'];
        yield '获奖用户' => ['user'];
        yield '有效状态' => ['valid'];
    }

    public function testUnauthenticatedAccess(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access Denied. The user doesn\'t have ROLE_ADMIN.');

        $client->request('GET', '/admin');
    }

    public function testGetEntityFqcn(): void
    {
        $this->assertSame('CampaignBundle\Entity\Reward', CampaignRewardCrudController::getEntityFqcn());
    }

    public function testControllerCreation(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser('admin@test.com', 'password'));

        $client->request('GET', '/admin');
        $response = $client->getResponse();

        $this->assertTrue(
            $response->isSuccessful(),
            'Response should be successful for authenticated user'
        );
    }
}
