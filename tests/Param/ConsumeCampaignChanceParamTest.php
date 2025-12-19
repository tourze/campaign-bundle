<?php

declare(strict_types=1);

namespace CampaignBundle\Tests\Param;

use CampaignBundle\Param\ConsumeCampaignChanceParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(ConsumeCampaignChanceParam::class)]
final class ConsumeCampaignChanceParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new ConsumeCampaignChanceParam(
            campaignCode: 'test-campaign',
            chanceId: 'chance-123',
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-campaign', $param->campaignCode);
        $this->assertSame('chance-123', $param->chanceId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new ConsumeCampaignChanceParam(campaignCode: 'test-campaign');

        $this->assertSame('test-campaign', $param->campaignCode);
        $this->assertSame('', $param->chanceId);
    }
}
