<?php

declare(strict_types=1);

namespace CampaignBundle\Tests\Param;

use CampaignBundle\Param\RequestCampaignChanceParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(RequestCampaignChanceParam::class)]
final class RequestCampaignChanceParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new RequestCampaignChanceParam(campaignCode: 'test-campaign');

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-campaign', $param->campaignCode);
    }

    public function testParamIsReadonly(): void
    {
        $param = new RequestCampaignChanceParam(campaignCode: 'test-123');

        $this->assertSame('test-123', $param->campaignCode);
    }
}
