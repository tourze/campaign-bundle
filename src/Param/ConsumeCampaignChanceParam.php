<?php

declare(strict_types=1);

namespace CampaignBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class ConsumeCampaignChanceParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '活动代号')]
        public string $campaignCode,

        #[MethodParam(description: '机会ID,不传入则自动查找')]
        public string $chanceId = '',
    ) {}
}
