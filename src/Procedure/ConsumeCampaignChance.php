<?php

namespace CampaignBundle\Procedure;

use CampaignBundle\Repository\CampaignRepository;
use CampaignBundle\Repository\ChanceRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '活动模块')]
#[MethodDoc(summary: '使用、消耗指定的活动机会信息')]
#[MethodExpose(method: 'ConsumeCampaignChance')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class ConsumeCampaignChance extends LockableProcedure
{
    #[MethodParam(description: '活动代号')]
    public string $campaignCode;

    #[MethodParam(description: '机会ID，不传入则自动查找')]
    public string $chanceId = '';

    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly ChanceRepository $chanceRepository,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(): array
    {
        $campaign = $this->campaignRepository->findOneBy([
            'code' => $this->campaignCode,
            'valid' => true,
        ]);
        if ($campaign === null) {
            throw new ApiException('找不到活动信息');
        }

        if (empty($this->chanceId)) {
            // 检查用户是否有有效的机会
            $chance = $this->chanceRepository->findOneBy([
                'user' => $this->security->getUser(),
                'campaign' => $campaign,
                'valid' => true,
            ], ['id' => 'DESC']);
        } else {
            $chance = $this->chanceRepository->findOneBy([
                'id' => $this->chanceId,
                'user' => $this->security->getUser(),
                'campaign' => $campaign,
                'valid' => true,
            ]);
        }

        if ($chance === null) {
            throw new ApiException('您暂时没有资格参与该活动');
        }

        // 消耗这个机会
        $chance->setUseTime(CarbonImmutable::now());
        $chance->setValid(false);
        $this->entityManager->persist($chance);
        $this->entityManager->flush();

        return [
            'maskImg' => 'https://rcroyalclubnmktach.blob.core.chinacloudapi.cn/upload-files/2022/11/TC.png',
        ];
    }
}
