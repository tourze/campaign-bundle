<?php

declare(strict_types=1);

namespace CampaignBundle\Tests\Param;

use CampaignBundle\Param\GetCampaignConfigParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetCampaignConfigParam::class)]
final class GetCampaignConfigParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetCampaignConfigParam(
            campaignCode: 'test-campaign',
            routerParams: ['param1' => 'value1'],
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-campaign', $param->campaignCode);
        $this->assertSame(['param1' => 'value1'], $param->routerParams);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetCampaignConfigParam(
            campaignCode: 'test-campaign',
        );

        $this->assertSame('test-campaign', $param->campaignCode);
        $this->assertSame([], $param->routerParams);
    }
}
