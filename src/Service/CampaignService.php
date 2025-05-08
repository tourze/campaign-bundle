<?php

namespace CampaignBundle\Service;

use AppBundle\Entity\BizUser;
use AppBundle\Service\CreditManager;
use AppBundle\Service\UserTagService;
use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Limit;
use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardLimitType;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Enum\LimitType;
use CampaignBundle\Repository\AwardRepository;
use CampaignBundle\Repository\ChanceRepository;
use CampaignBundle\Repository\RewardRepository;
use Carbon\Carbon;
use CouponBundle\Service\CouponService;
use CreditBundle\Service\AccountService;
use CreditBundle\Service\CurrencyService;
use CreditBundle\Service\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use OrderBundle\Entity\OfferChance;
use OrderBundle\Entity\OfferSku;
use OrderBundle\Repository\OfferChanceRepository;
use OrderBundle\Repository\OfferSkuRepository;
use ProductBundle\Repository\SkuRepository;
use ProductBundle\Repository\SpuRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;

class CampaignService
{
    public function __construct(
        private readonly ChanceRepository $chanceRepository,
        private readonly RewardRepository $rewardRepository,
        private readonly AwardRepository $awardRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly ?CouponService $couponService,
        private readonly UserTagService $userTagService,
        private readonly ?SkuRepository $skuRepository,
        private readonly ?SpuRepository $spuRepository,
        private readonly ?OfferChanceRepository $offerChanceRepository,
        private readonly CreditManager $creditManager,
        private readonly ?CurrencyService $currencyService,
        private readonly ?AccountService $accountService,
        private readonly ?TransactionService $transactionService,
    ) {
    }

    public function checkLimit(UserInterface $user, Limit $limit): bool
    {
        // 看这个人是不是有可以使用的机会
        if (LimitType::CHANCE === $limit->getType()) {
            $chance = $this->chanceRepository->findOneBy([
                'campaign' => $limit->getAward()->getCampaign(),
                'user' => $user,
                'valid' => true,
            ]);
            if (!$chance) {
                return false;
            }
        }

        // 检查标签情况
        if (LimitType::USER_TAG === $limit->getType()) {
            if (!in_array($limit->getValue(), $this->userTagService->getTagIdsByUser($user))) {
                return false;
            }
        }

        return true;
    }

    public function consumeLimit(UserInterface $user, Limit $limit): bool
    {
        // 消耗一个机会
        if (LimitType::CHANCE === $limit->getType()) {
            $chance = $this->chanceRepository->findOneBy([
                'campaign' => $limit->getAward()->getCampaign(),
                'user' => $user,
                'valid' => true,
            ]);
            if (!$chance) {
                return false;
            }
            $chance->setUseTime(Carbon::now());
            $chance->setValid(false);
            $this->entityManager->persist($chance);
            $this->entityManager->flush();
        }

        // 标签不用处理

        return true;
    }

    #[Transactional]
    public function rewardUser(UserInterface $user, Award $award): Reward
    {
        $campaign = $award->getCampaign();
        // 判断总数量
        if ($award->getPrizeQuantity() <= 0) {
            throw new ApiException('奖品已领取完毕', 1007);
        }

        // 判断是否已满足领取限制
        if ($award->getTimes() > 0) {
            $qb = $this->rewardRepository->createQueryBuilder('r')
                ->select('count(r)')
                ->where('r.award = :award and r.user = :user')
                ->setParameter('award', $award)
                ->setParameter('user', $user);
            $now = Carbon::now();
            switch ($award->getAwardLimitType()) {
                case AwardLimitType::BUY_DAILY:
                    $qb = $qb->andWhere('r.createTime between :start and :end')
                        ->setParameter('start', $now->clone()->startOfDay())
                        ->setParameter('end', $now->clone()->endOfDay());
                    break;
                case AwardLimitType::BUY_WEEK:
                    $qb = $qb->andWhere('r.createTime between :start and :end')
                        ->setParameter('start', $now->clone()->startOfWeek())
                        ->setParameter('end', $now->clone()->endOfWeek());
                    break;
                case AwardLimitType::BUY_MONTH:
                    $qb = $qb->andWhere('r.createTime between :start and :end')
                        ->setParameter('start', $now->clone()->startOfMonth())
                        ->setParameter('end', $now->clone()->endOfMonth());
                    break;
                case AwardLimitType::BUY_YEAR:
                    $qb = $qb->andWhere('r.createTime between :start and :end')
                        ->setParameter('start', $now->clone()->startOfYear())
                        ->setParameter('end', $now->clone()->endOfYear());
                    break;
                case AwardLimitType::BUY_QUARTER:
                    $qb = $qb->andWhere('r.createTime between :start and :end')
                        ->setParameter('start', $now->clone()->startOfQuarter())
                        ->setParameter('end', $now->clone()->endOfQuarter());
                    break;
                case AwardLimitType::BUY_TOTAL:
                default:
                    break;
            }
            $rewardCount = $qb->getQuery()->getSingleScalarResult();
            if ($rewardCount >= $award->getTimes()) {
                $message = match ($award->getAwardLimitType()) {
                    AwardLimitType::BUY_DAILY => "每人每日只能领取{$award->getTimes()}次",
                    AwardLimitType::BUY_WEEK => "每人每周只能领取{$award->getTimes()}次",
                    AwardLimitType::BUY_MONTH => "每人每月只能领取{$award->getTimes()}次",
                    AwardLimitType::BUY_QUARTER => "每人每季度只能领取{$award->getTimes()}次",
                    AwardLimitType::BUY_YEAR => "每人每季年只能领取{$award->getTimes()}次",
                    default => '已达到领取限制',
                };
                throw new ApiException($message, 1006);
            }
        }

        $reward = new Reward();
        $reward->setCampaign($campaign);
        $reward->setAward($award);
        $reward->setType($award->getType());
        $reward->setValue($award->getValue());
        $reward->setUser($user);
        $reward->setValid(true);

        $reward->setSn(uniqid());

        // 优惠券
        if (AwardType::COUPON === $award->getType()) {
            if (!$this->couponService) {
                throw new \Exception('暂时不支持优惠券业务');
            }
            $coupon = $this->couponService->detectCoupon($award->getValue());
            try {
                $stock = $this->couponService->getCouponValidStock($coupon);
                if ($stock <= 0) {
                    throw new ApiException('暂无库存', 1003);
                }
                $code = $this->couponService->sendCode($user, $coupon);
                $reward->setSn($code->getSn());
            } catch (\Throwable $exception) {
                $this->logger->debug('优惠券发送失败');
            }
        }

        // SKU购买资格
        if (in_array($award->getType(), [AwardType::SKU_QUALIFICATION, AwardType::SPU_QUALIFICATION])) {
            if (AwardType::SKU_QUALIFICATION === $award->getType()) {
                if (!$this->skuRepository) {
                    throw new \Exception('暂时不支持SKU业务');
                }
                $sku = $this->skuRepository->findOneBy([
                    'id' => $award->getValue(),
                    'valid' => true,
                ]);
            }

            if (AwardType::SPU_QUALIFICATION === $award->getType()) {
                if (!$this->spuRepository) {
                    throw new \Exception('暂时不支持SPU业务');
                }
                $spu = $this->spuRepository->findOneBy([
                    'id' => $award->getValue(),
                    'valid' => true,
                ]);
                $sku = $spu->getSkus()->first();
            }

            // 判断sku库存
            if ($sku->getValidStock() <= 0) {
                throw new ApiException('商品无库存', 1007);
            }

            if (!$this->offerChanceRepository) {
                throw new \Exception('暂时不支持订单业务');
            }
            $offerChance = new OfferChance();
            $offerChance->setTitle("{$campaign->getName()}赠送SKU资格[{$award->getValue()}]");
            $offerChance->setUser($user);
            $offerChance->setStartTime(Carbon::now());
            $offerChance->setEndTime($campaign->getEndTime());
            $offerChance->setValid(true);

            $offerSku = new OfferSku();
            $offerSku->setChance($offerChance);
            $offerSku->setSku($sku);
            $offerSku->setQuantity(1);

            $this->entityManager->persist($offerChance);
            $this->entityManager->persist($offerSku);
            $this->entityManager->flush();

            $reward->setSn($offerChance->getId());
        }

        if (AwardType::CREDIT === $award->getType()) {
            // 给积分，point取奖项里的值
            $integralName = $this->creditManager->getOneCredit();
            $currency = $this->currencyService->getCurrencyByCode($integralName);
            $inAccount = $this->accountService->getAccountByUser($user, $currency);
            $remark = $_ENV['CAMPAIGN_AWARD_CREDIT_REMARK'] ?? $award->getCampaign()->getName();
            $this->transactionService->increase(
                'CAMPAIGN-' . $award->getId() . '-' . Uuid::v4()->toRfc4122(),
                $inAccount,
                intval(intval($award->getValue())),
                $remark,
            );
        }

        $this->entityManager->persist($reward);
        $award->setPrizeQuantity($award->getPrizeQuantity() - 1);
        $this->entityManager->persist($award);
        $this->entityManager->flush();

        return $reward;
    }
}
