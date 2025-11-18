<?php

declare(strict_types=1);

namespace CampaignBundle\Tests\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignLimitCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CampaignLimitCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CampaignLimitCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @phpstan-ignore missingType.generics
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @var CampaignLimitCrudController */
        return self::getService(CampaignLimitCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '所属奖励' => ['所属奖励'];
        yield '限制类型' => ['限制类型'];
        yield '条件值' => ['条件值'];
        yield '备注' => ['备注'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '所属奖励' => ['award'];
        yield '限制类型' => ['type'];
        yield '条件值' => ['value'];
        yield '备注' => ['remark'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield '所属奖励' => ['award'];
        yield '限制类型' => ['type'];
        yield '条件值' => ['value'];
        yield '备注' => ['remark'];
    }

    public function testUnauthenticatedAccess(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access Denied. The user doesn\'t have ROLE_ADMIN.');

        $client->request('GET', '/admin');
    }

    public function testIndexActionWithAuthentication(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser('admin@test.com', 'password'));

        $client->request('GET', $this->generateAdminUrl(Action::INDEX));
        $response = $client->getResponse();

        $this->assertTrue(
            $response->isSuccessful(),
            'Response should be successful for authenticated user'
        );
    }

    public function testNewPageAccessWithAuthentication(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser('admin@test.com', 'password'));

        $client->request('GET', $this->generateAdminUrl(Action::NEW));
        $response = $client->getResponse();

        $this->assertTrue(
            $response->isSuccessful(),
            'Response should be successful for authenticated access to new form'
        );
    }
}
