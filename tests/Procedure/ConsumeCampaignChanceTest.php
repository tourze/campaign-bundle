<?php

namespace CampaignBundle\Tests\Procedure;

use CampaignBundle\Procedure\ConsumeCampaignChance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(ConsumeCampaignChance::class)]
#[RunTestsInSeparateProcesses]
final class ConsumeCampaignChanceTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 父类 setUp() 已经在 AbstractIntegrationTestCase 中调用
        // 这里不需要再次调用，否则会导致无限递归
    }

    public function testExecute(): void
    {
        // 验证 execute 方法的返回类型
        $reflection = new \ReflectionMethod(ConsumeCampaignChance::class, 'execute');
        $this->assertSame('array', (string) $reflection->getReturnType());
    }
}
