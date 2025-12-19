<?php

declare(strict_types=1);

namespace CampaignBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetCampaignConfigParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '活动代号')]
        public string $campaignCode,

        /**
         * @var array<string, mixed>
         */
        #[MethodParam(description: '路由参数')]
        public array $routerParams = [],
    ) {
    }
}
