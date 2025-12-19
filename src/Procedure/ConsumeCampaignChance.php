<?php

declare(strict_types=1);

namespace CampaignBundle\Procedure;

use CampaignBundle\Param\ConsumeCampaignChanceParam;
use CampaignBundle\Repository\CampaignRepository;
use CampaignBundle\Repository\ChanceRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
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
    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly ChanceRepository $chanceRepository,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @phpstan-param ConsumeCampaignChanceParam $param
     */
    public function execute(ConsumeCampaignChanceParam|RpcParamInterface $param): ArrayResult
    {
        $campaign = $this->campaignRepository->findOneBy([
            'code' => $param->campaignCode,
            'valid' => true,
        ]);
        if (null === $campaign) {
            throw new ApiException('找不到活动信息');
        }

        if ('' === $param->chanceId) {
            // 检查用户是否有有效的机会
            $chance = $this->chanceRepository->findOneBy([
                'user' => $this->security->getUser(),
                'campaign' => $campaign,
                'valid' => true,
            ], ['id' => 'DESC']);
        } else {
            $chance = $this->chanceRepository->findOneBy([
                'id' => $param->chanceId,
                'user' => $this->security->getUser(),
                'campaign' => $campaign,
                'valid' => true,
            ]);
        }

        if (null === $chance) {
            throw new ApiException('您暂时没有资格参与该活动');
        }

        // 消耗这个机会
        $chance->setUseTime(CarbonImmutable::now());
        $chance->setValid(false);
        $this->entityManager->persist($chance);
        $this->entityManager->flush();

        return new ArrayResult([
            'maskImg' => 'https://rcroyalclubnmktach.blob.core.chinacloudapi.cn/upload-files/2022/11/TC.png',
        ]);
    }
}
