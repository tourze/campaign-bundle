<?php

namespace CampaignBundle\Procedure;

use CampaignBundle\Entity\EventLog;
use CampaignBundle\Repository\CampaignRepository;
use CampaignBundle\Repository\EventLogRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[MethodTag(name: '活动模块')]
#[MethodDoc(summary: '拉取活动记录日志')]
#[MethodExpose(method: 'GetCampaignEventLogs')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetCampaignEventLogs extends BaseProcedure
{
    use PaginatorTrait;

    #[MethodParam(description: '活动ID')]
    public string $campaignCode;

    #[MethodParam(description: '事件')]
    public string $event = '';

    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly EventLogRepository $eventLogRepository,
        private readonly Security $security,
        private readonly NormalizerInterface $normalizer,
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

        $qb = $this->eventLogRepository
            ->createQueryBuilder('a')
            ->where('a.user = :user')
            ->setParameter('user', $this->security->getUser())
            ->orderBy('a.id', Criteria::DESC);
        if (!empty($this->event)) {
            $qb->andWhere('a.event = :event');
            $qb->setParameter('event', $this->event);
        }

        return $this->fetchList($qb, $this->formatItem(...));
    }

    private function formatItem(EventLog $item): array
    {
        return $this->normalizer->normalize($item, 'array', ['groups' => 'restful_read']);
    }
}
