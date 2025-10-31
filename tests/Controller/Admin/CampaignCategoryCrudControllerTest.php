<?php

namespace CampaignBundle\Tests\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignCategoryCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CampaignCategoryCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CampaignCategoryCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @phpstan-ignore-next-line missingType.generics
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(CampaignCategoryCrudController::class);
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '分类名称' => ['分类名称'];
        yield '有效状态' => ['有效状态'];
        yield '排序' => ['排序'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '分类名称' => ['title'];
        yield '有效状态' => ['valid'];
        yield '排序' => ['sortNumber'];
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield '分类名称' => ['title'];
        yield '有效状态' => ['valid'];
        yield '排序' => ['sortNumber'];
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
        $this->assertSame('CampaignBundle\Entity\Category', CampaignCategoryCrudController::getEntityFqcn());
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
