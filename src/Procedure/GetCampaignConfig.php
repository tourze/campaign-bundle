<?php

declare(strict_types=1);

namespace CampaignBundle\Procedure;

use CampaignBundle\Repository\CampaignRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\TextManageBundle\Service\TextFormatter;

#[MethodTag(name: '活动模块')]
#[MethodDoc(summary: '获取活动配置')]
#[MethodExpose(method: 'GetCampaignConfig')]
class GetCampaignConfig extends BaseProcedure
{
    #[MethodParam(description: '活动代号')]
    public string $campaignCode;

    /**
     * @var array<string, mixed>
     */
    #[MethodParam(description: '路由参数')]
    public array $routerParams = [];

    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly Security $security,
        private readonly TextFormatter $textFormatter,
    ) {
    }

    /** @return array<string, mixed> */
    public function execute(): array
    {
        $campaign = $this->campaignRepository->findOneBy([
            'code' => $this->campaignCode,
            'valid' => true,
        ]);
        if (null === $campaign) {
            throw new ApiException('找不到活动信息');
        }

        $result = $campaign->restfulReadArray();
        $result['visitUrl'] = (string) $campaign->getEntryUrl();

        // {webview:routerParams} 是特殊的占位符，代表当前路由的所有参数
        if (str_contains($result['visitUrl'], '{webview:routerParams}')) {
            $result['visitUrl'] = str_replace('{webview:routerParams}', http_build_query($this->routerParams), $result['visitUrl']);
        }

        if (null !== $this->security->getUser()) {
            $result['visitUrl'] = $this->textFormatter->formatText($result['visitUrl'], [
                'user' => $this->security->getUser(),
                'campaign' => $campaign,
            ]);
        }

        if (null !== $campaign->getShareImg()) {
            $config = [
                'title' => $campaign->getShareTitle(),
                'imageUrl' => $campaign->getShareImg(),
                'path' => "/pages/webview/campaign?code={$campaign->getCode()}",
            ];
            if (null !== $this->security->getUser()) {
                $config['path'] = "{$config['path']}&shareUser={$this->security->getUser()->getUserIdentifier()}";
            }

            $result['shareConfig'] = $config;
        }

        return $result;
    }
}
