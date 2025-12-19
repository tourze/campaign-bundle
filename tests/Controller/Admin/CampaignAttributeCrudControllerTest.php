<?php

namespace CampaignBundle\Tests\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignAttributeCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CampaignAttributeCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CampaignAttributeCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
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

    protected function getControllerService(): CampaignAttributeCrudController
    {
        return self::getService(CampaignAttributeCrudController::class);
    }

    /** @return \Generator<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '所属活动' => ['所属活动'];
        yield '属性名称' => ['属性名称'];
        yield '属性值' => ['属性值'];
        yield '备注' => ['备注'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /** @return \Generator<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'campaign' => ['campaign'];
        yield 'name' => ['name'];
        yield 'value' => ['value'];
        yield 'remark' => ['remark'];
    }

    /** @return \Generator<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield 'campaign' => ['campaign'];
        yield 'name' => ['name'];
        yield 'value' => ['value'];
        yield 'remark' => ['remark'];
    }
}
