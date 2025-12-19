<?php

declare(strict_types=1);

namespace CampaignBundle\Procedure;

use CampaignBundle\Entity\EventLog;
use CampaignBundle\Param\GetCampaignEventLogsParam;
use CampaignBundle\Repository\CampaignRepository;
use CampaignBundle\Repository\EventLogRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
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

    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly EventLogRepository $eventLogRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @phpstan-param GetCampaignEventLogsParam $param
     */
    public function execute(GetCampaignEventLogsParam|RpcParamInterface $param): ArrayResult
    {
        $campaign = $this->campaignRepository->findOneBy([
            'code' => $param->campaignCode,
            'valid' => true,
        ]);
        if (null === $campaign) {
            throw new ApiException('找不到活动信息');
        }

        $qb = $this->eventLogRepository
            ->createQueryBuilder('a')
            ->where('a.user = :user')
            ->setParameter('user', $this->security->getUser())
            ->orderBy('a.id', 'DESC')
        ;
        if ('' !== $param->event) {
            $qb->andWhere('a.event = :event');
            $qb->setParameter('event', $param->event);
        }

        return new ArrayResult($this->fetchList($qb, $this->formatItem(...), null, $param));
    }

    /**
     * @return array<string, mixed>
     */
    private function formatItem(EventLog $item): array
    {
        return new ArrayResult([
            'event' => $item->getEvent(),
            'params' => $item->getParams(),
        ]);
    }
}
