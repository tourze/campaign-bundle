<?php

declare(strict_types=1);

namespace CampaignBundle\Tests\Param;

use CampaignBundle\Param\GetCampaignRewardsParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetCampaignRewardsParam::class)]
final class GetCampaignRewardsParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetCampaignRewardsParam(
            campaignCode: 'test-campaign',
            event: 'test-event',
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-campaign', $param->campaignCode);
        $this->assertSame('test-event', $param->event);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetCampaignRewardsParam(campaignCode: 'test-campaign');

        $this->assertSame('test-campaign', $param->campaignCode);
        $this->assertSame('', $param->event);
    }
}
