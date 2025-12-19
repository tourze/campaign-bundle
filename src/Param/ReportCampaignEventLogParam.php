<?php

declare(strict_types=1);

namespace CampaignBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class ReportCampaignEventLogParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '活动ID')]
        public string $campaignCode,

        #[MethodParam(description: '事件')]
        public string $event,

        /**
         * @var array<string, mixed>
         */
        #[MethodParam(description: '参数')]
        public array $params = [],
    ) {}
}
