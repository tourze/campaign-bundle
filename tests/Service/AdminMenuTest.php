<?php

namespace CampaignBundle\Tests\Service;

use CampaignBundle\Service\AdminMenu;
use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * 对应组件的测试类。
 *
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 测试设置逻辑
    }

    public function testServiceCreation(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testImplementsMenuProviderInterface(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $this->assertInstanceOf(MenuProviderInterface::class, $adminMenu);
    }

    public function testInvokeShouldBeCallable(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $reflection = new \ReflectionClass($adminMenu);
        $this->assertTrue($reflection->hasMethod('__invoke'));
    }

    public function testInvokeWithMenu(): void
    {
        $adminMenu = self::getService(AdminMenu::class);

        // 使用真实的 MenuFactory 创建菜单项
        $factory = new MenuFactory();
        $item = $factory->createItem('root');

        // 调用 __invoke 方法
        $adminMenu($item);

        // 验证菜单结构
        $campaignMenu = $item->getChild('通用活动');
        $this->assertNotNull($campaignMenu, '应该创建"通用活动"菜单');

        // 验证子菜单项是否创建
        $this->assertNotNull($campaignMenu->getChild('活动分类'), '应该创建"活动分类"子菜单');
        $this->assertNotNull($campaignMenu->getChild('活动管理'), '应该创建"活动管理"子菜单');
        $this->assertNotNull($campaignMenu->getChild('活动属性'), '应该创建"活动属性"子菜单');
        $this->assertNotNull($campaignMenu->getChild('奖励配置'), '应该创建"奖励配置"子菜单');
        $this->assertNotNull($campaignMenu->getChild('限制条件'), '应该创建"限制条件"子菜单');
        $this->assertNotNull($campaignMenu->getChild('参与机会'), '应该创建"参与机会"子菜单');
        $this->assertNotNull($campaignMenu->getChild('奖励记录'), '应该创建"奖励记录"子菜单');
        $this->assertNotNull($campaignMenu->getChild('参与日志'), '应该创建"参与日志"子菜单');
    }

    public function testMenuItemsHaveIcons(): void
    {
        $adminMenu = self::getService(AdminMenu::class);

        $factory = new MenuFactory();
        $item = $factory->createItem('root');

        $adminMenu($item);

        $campaignMenu = $item->getChild('通用活动');
        $this->assertNotNull($campaignMenu);

        // 验证子菜单项是否有图标属性
        $categoryMenu = $campaignMenu->getChild('活动分类');
        $this->assertNotNull($categoryMenu);
        $this->assertEquals('fas fa-folder', $categoryMenu->getAttribute('icon'));

        $campaignManageMenu = $campaignMenu->getChild('活动管理');
        $this->assertNotNull($campaignManageMenu);
        $this->assertEquals('fas fa-calendar-alt', $campaignManageMenu->getAttribute('icon'));
    }
}
