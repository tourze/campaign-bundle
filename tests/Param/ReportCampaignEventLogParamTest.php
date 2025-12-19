<?php

declare(strict_types=1);

namespace CampaignBundle\Tests\Param;

use CampaignBundle\Param\ReportCampaignEventLogParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(ReportCampaignEventLogParam::class)]
final class ReportCampaignEventLogParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new ReportCampaignEventLogParam(
            campaignCode: 'test-campaign',
            event: 'test-event',
            params: ['key' => 'value'],
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-campaign', $param->campaignCode);
        $this->assertSame('test-event', $param->event);
        $this->assertSame(['key' => 'value'], $param->params);
    }

    public function testParamIsReadonly(): void
    {
        $param = new ReportCampaignEventLogParam(
            campaignCode: 'test-campaign',
            event: 'test-event',
        );

        $this->assertSame('test-campaign', $param->campaignCode);
        $this->assertSame('test-event', $param->event);
        $this->assertSame([], $param->params);
    }
}
