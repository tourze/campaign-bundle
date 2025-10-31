<?php

namespace CampaignBundle\Tests\Controller\Admin;

use CampaignBundle\Controller\Admin\CampaignCampaignCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CampaignCampaignCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CampaignCampaignCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame('CampaignBundle\Entity\Campaign', CampaignCampaignCrudController::getEntityFqcn());
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

    protected function getControllerService(): CampaignCampaignCrudController
    {
        return self::getService(CampaignCampaignCrudController::class);
    }

    /** @return \Generator<string, array{string}, mixed, mixed> */
    public static function provideIndexPageHeaders(): \Generator
    {
        // 基于控制器的 configureFields 方法，返回在列表页显示的字段头部
        yield 'ID' => ['ID'];
        yield '活动代号' => ['活动代号'];
        yield '活动名称' => ['活动名称'];
        yield '副标题' => ['副标题'];
        yield '活动分类' => ['活动分类'];
        yield '有效状态' => ['有效状态'];
        yield '推荐' => ['推荐'];
        yield '排序' => ['排序'];
        yield '缩略图' => ['缩略图'];
        yield '分享图片' => ['分享图片'];
        yield '分享标题' => ['分享标题'];
        yield '入口地址' => ['入口地址'];
        yield '开始时间' => ['开始时间'];
        yield '结束时间' => ['结束时间'];
        yield '标签' => ['标签'];
        yield '请求表达式' => ['请求表达式'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /** @return \Generator<string, array{string}, mixed, mixed> */
    public static function provideNewPageFields(): \Generator
    {
        // 基于控制器的 configureFields 方法，返回在新建页显示的字段（排除 hideOnForm 的字段）
        yield 'id字段' => ['id'];
        yield '活动代号字段' => ['code'];
        yield '活动名称字段' => ['name'];
        yield '副标题字段' => ['subtitle'];
        yield '活动分类字段' => ['category'];
        yield '有效状态字段' => ['valid'];
        yield '推荐字段' => ['recommend'];
        yield '排序字段' => ['sortNumber'];
        yield '缩略图字段' => ['thumbUrl'];
        yield '分享图片字段' => ['shareImg'];
        yield '分享标题字段' => ['shareTitle'];
        yield '入口地址字段' => ['entryUrl'];
        yield '开始时间字段' => ['startTime'];
        yield '结束时间字段' => ['endTime'];
        yield '标签字段' => ['tags'];
        yield '请求表达式字段' => ['requestExpression'];
    }

    /** @return \Generator<string, array{string}, mixed, mixed> */
    public static function provideEditPageFields(): \Generator
    {
        // 编辑页的字段与新建页相同（排除 hideOnForm 的字段）
        yield 'id字段' => ['id'];
        yield '活动代号字段' => ['code'];
        yield '活动名称字段' => ['name'];
        yield '副标题字段' => ['subtitle'];
        yield '活动分类字段' => ['category'];
        yield '有效状态字段' => ['valid'];
        yield '推荐字段' => ['recommend'];
        yield '排序字段' => ['sortNumber'];
        yield '缩略图字段' => ['thumbUrl'];
        yield '分享图片字段' => ['shareImg'];
        yield '分享标题字段' => ['shareTitle'];
        yield '入口地址字段' => ['entryUrl'];
        yield '开始时间字段' => ['startTime'];
        yield '结束时间字段' => ['endTime'];
        yield '标签字段' => ['tags'];
        yield '请求表达式字段' => ['requestExpression'];
    }
}
