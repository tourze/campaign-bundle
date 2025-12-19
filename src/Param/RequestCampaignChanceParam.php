<?php

declare(strict_types=1);

namespace CampaignBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class RequestCampaignChanceParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '活动代号')]
        public string $campaignCode,
    ) {}
}
