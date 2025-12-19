<?php

namespace CampaignBundle\Tests\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignAwardCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CampaignAwardCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CampaignAwardCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): CampaignAwardCrudController
    {
        return self::getService(CampaignAwardCrudController::class);
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '所属活动' => ['所属活动'];
        yield '触发事件' => ['触发事件'];
        yield '奖励类型' => ['奖励类型'];
        yield '奖励值/ID' => ['奖励值/ID'];
        yield '备注' => ['备注'];
        yield '总数量' => ['总数量'];
        yield '限制类型' => ['限制类型'];
        yield '限制次数' => ['限制次数'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '所属活动' => ['campaign'];
        yield '触发事件' => ['event'];
        yield '奖励类型' => ['type'];
        yield '奖励值/ID' => ['value'];
        yield '备注' => ['remark'];
        yield '总数量' => ['prizeQuantity'];
        yield '限制类型' => ['awardLimitType'];
        yield '限制次数' => ['times'];
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield '所属活动' => ['campaign'];
        yield '触发事件' => ['event'];
        yield '奖励类型' => ['type'];
        yield '奖励值/ID' => ['value'];
        yield '备注' => ['remark'];
        yield '总数量' => ['prizeQuantity'];
        yield '限制类型' => ['awardLimitType'];
        yield '限制次数' => ['times'];
    }

    public function testUnauthenticatedAccess(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access Denied. The user doesn\'t have ROLE_ADMIN.');

        $client->request('GET', '/admin');
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
