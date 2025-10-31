<?php

declare(strict_types=1);

namespace CampaignBundle\Procedure;

use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Repository\AwardRepository;
use CampaignBundle\Repository\CampaignRepository;
use CampaignBundle\Repository\RewardRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[MethodTag(name: '活动模块')]
#[MethodDoc(summary: '获取活动获得的所有奖励')]
#[MethodExpose(method: 'GetCampaignRewards')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetCampaignRewards extends BaseProcedure
{
    use PaginatorTrait;

    #[MethodParam(description: '活动ID')]
    public string $campaignCode;

    #[MethodParam(description: '事件')]
    public string $event = '';

    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly RewardRepository $rewardRepository,
        private readonly AwardRepository $awardRepository,
        private readonly Security $security,
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

        $qb = $this->rewardRepository
            ->createQueryBuilder('a')
            ->where('a.user = :user and a.campaign = :campaign')
            ->setParameter('user', $this->security->getUser())
            ->setParameter('campaign', $campaign)
            ->orderBy('a.id', 'DESC')
        ;
        if ('' !== $this->event) {
            $award = $this->awardRepository->findBy([
                'campaign' => $campaign,
                'event' => $this->event,
            ]);

            $qb->andWhere('a.award in (:award)');
            $qb->setParameter('award', $award);
        }

        return $this->fetchList($qb, $this->formatItem(...));
    }

    /** @return array<string, mixed> */
    private function formatItem(Reward $reward): array
    {
        $re = $reward->retrieveApiArray();
        $re['valid'] = false;

        return $re;
    }
}
