<?php

namespace CampaignBundle\Procedure;

use CampaignBundle\Entity\Chance;
use CampaignBundle\Repository\CampaignRepository;
use CampaignBundle\Repository\ChanceRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\EcolBundle\Service\Engine;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

/**
 * 对于前端来说，他需要关心很多个状态：
 * 1. 没资格参与活动，例如已经参与过其他互斥的活动；
 * 2. 参与过，剩余参数次数不足，不能继续参与；
 * 3. 没参与过，可以继续；
 */
#[MethodTag('活动模块')]
#[MethodDoc('请求指定活动的资格')]
#[MethodExpose('RequestCampaignChance')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Log]
class RequestCampaignChance extends LockableProcedure
{
    #[MethodParam('活动代号')]
    public string $campaignCode;

    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly ChanceRepository $chanceRepository,
        private readonly Engine $engine,
        private readonly LoggerInterface $procedureLogger,
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
        if (!$campaign) {
            throw new ApiException('找不到活动信息');
        }

        // 检查用户是否有有效的机会
        $chance = $this->chanceRepository->findOneBy([
            'user' => $this->security->getUser(),
            'campaign' => $campaign,
            'valid' => true,
        ], ['id' => 'DESC']);

        // 没机会的话，尝试下分配机会
        if (!$chance) {
            if (empty($campaign->getRequestExpression())) {
                throw new ApiException('请联系客服设置机会条件');
            }

            /** @var BizUser $user */
            $user = $this->security->getUser();

            $values = [
                'user' => $user,
                'member' => $user,
                'member_id' => $user->getId(),
                'campaign' => $campaign,
                'env' => $_ENV,
            ];

            $checkRes = false;
            try {
                $this->procedureLogger->debug('执行活动配置中的表达式', [
                    'expression' => $campaign->getRequestExpression(),
                    ...$values,
                ]);
                $checkRes = $this->engine->evaluate($campaign->getRequestExpression(), $values);
            } catch (ApiException $exception) {
                throw $exception;
            } catch (\Throwable $exception) {
                $this->procedureLogger->error('执行表达式分发资格时发生异常', [
                    'exception' => $exception,
                    'values' => $values,
                    'expression' => $campaign->getRequestExpression(),
                ]);
            }

            if (!$checkRes) {
                throw new ApiException('您当前不符合活动资格');
            }

            // 新建一次机会
            $chance = new Chance();
            $chance->setCampaign($campaign);
            $chance->setUser($this->security->getUser());
            $chance->setStartTime(Carbon::now());
            $chance->setExpireTime($campaign->getEndTime());
            $chance->setValid(true);
            $this->entityManager->persist($chance);
            $this->entityManager->flush();
        }

        return $chance->retrieveApiArray();
    }
}
