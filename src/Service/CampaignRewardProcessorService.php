<?php

declare(strict_types=1);

namespace CampaignBundle\Service;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Exception\CouponNotSupportedException;
use CampaignBundle\Exception\CreditServiceUnavailableException;
use CampaignBundle\Exception\InsufficientStockException;
use CampaignBundle\Exception\InvalidQualificationTypeException;
use CampaignBundle\Exception\OrderNotSupportedException;
use CampaignBundle\Exception\SpuNotSupportedException;
use CreditBundle\Service\AccountService;
use CreditBundle\Service\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Tourze\CouponCoreBundle\Service\CouponService;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Service\SpuService;
use Tourze\ProductServiceContracts\SkuLoaderInterface;
use Tourze\SpecialOrderBundle\Entity\OfferChance;
use Tourze\SpecialOrderBundle\Entity\OfferSku;
use Tourze\SpecialOrderBundle\Repository\OfferChanceRepository;

/**
 * 活动奖励类型处理服务。
 *
 * 专门处理不同类型奖励的具体处理逻辑，包括优惠券、SKU资格、积分等。
 * 遵循单一职责原则，只负责奖励类型的具体处理。
 */
readonly class CampaignRewardProcessorService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private ?CouponService $couponService,
        private SkuLoaderInterface $skuLoader,
        private ?SpuService $spuService,
        private ?OfferChanceRepository $offerChanceRepository,
        private ?AccountService $accountService,
        private ?TransactionService $transactionService,
    ) {
    }

    /**
     * 根据奖励类型处理奖励。
     *
     * @param UserInterface $user   the user receiving the reward
     * @param Award         $award  the award being given
     * @param Reward        $reward the reward to process
     */
    public function processRewardByType(UserInterface $user, Award $award, Reward $reward): void
    {
        match ($award->getType()) {
            AwardType::COUPON => $this->processCouponReward($user, $award, $reward),
            AwardType::SKU_QUALIFICATION, AwardType::SPU_QUALIFICATION => $this->processSkuQualificationReward($user, $award, $reward),
            AwardType::CREDIT => $this->processCreditReward($user, $award, $reward),
            default => null,
        };
    }

    /**
     * 处理优惠券奖励。
     *
     * @param UserInterface $user   the user receiving the reward
     * @param Award         $award  the award being given
     * @param Reward        $reward the reward to process
     */
    private function processCouponReward(UserInterface $user, Award $award, Reward $reward): void
    {
        if (null === $this->couponService) {
            throw new CouponNotSupportedException();
        }

        $coupon = $this->couponService->detectCoupon($award->getValue());
        try {
            $stock = $this->couponService->getCouponValidStock($coupon);
            if ($stock <= 0) {
                throw new InsufficientStockException('暂无库存');
            }
            $code = $this->couponService->sendCode($user, $coupon);
            $sn = $code->getSn();
            if (null !== $sn) {
                $reward->setSn($sn);
            }
        } catch (\Throwable $exception) {
            $this->logger->error('优惠券发送失败', [
                'exception' => $exception,
            ]);
        }
    }

    /**
     * 处理SKU资格奖励。
     *
     * @param UserInterface $user   the user receiving the reward
     * @param Award         $award  the award being given
     * @param Reward        $reward the reward to process
     */
    private function processSkuQualificationReward(UserInterface $user, Award $award, Reward $reward): void
    {
        $sku = $this->getSkuForQualification($award);

        assert(method_exists($sku, 'getValidStock'), 'SKU must have getValidStock method');
        if ($sku->getValidStock() <= 0) {
            throw new InsufficientStockException('商品无库存');
        }

        $offerChance = $this->createOfferChance($user, $award, $sku);
        $offerChanceId = $offerChance->getId();
        if (null !== $offerChanceId) {
            $reward->setSn($offerChanceId);
        }
    }

    /**
     * 根据奖励类型获取资格对应的SKU。
     *
     * @param Award $award the award to get SKU for
     */
    private function getSkuForQualification(Award $award): object
    {
        if (AwardType::SKU_QUALIFICATION === $award->getType()) {
            $sku = $this->skuLoader->loadSkuByIdentifier($award->getValue());
            assert(null !== $sku, 'SKU must be a valid object');

            return $sku;
        }

        if (AwardType::SPU_QUALIFICATION === $award->getType()) {
            if (null === $this->spuService) {
                throw new SpuNotSupportedException();
            }
            $spu = $this->spuService->findValidSpuById($award->getValue());
            if (null === $spu) {
                throw new InvalidQualificationTypeException('SPU not found');
            }

            $sku = $spu->getSkus()->first();
            assert(false !== $sku && is_object($sku), 'SKU from SPU must be a valid object');

            return $sku;
        }

        throw new InvalidQualificationTypeException('Invalid qualification type');
    }

    /**
     * 为用户创建优惠机会。
     *
     * @param UserInterface $user  the user
     * @param Award         $award the award
     * @param object        $sku   the SKU
     *
     * @return OfferChance The created offer chance
     */
    private function createOfferChance(UserInterface $user, Award $award, object $sku): OfferChance
    {
        if (null === $this->offerChanceRepository) {
            throw new OrderNotSupportedException();
        }

        $campaign = $award->getCampaign();
        $offerChance = new OfferChance();
        $offerChance->setTitle("{$campaign->getName()}赠送SKU资格[{$award->getValue()}]");
        $offerChance->setUser($user);
        $offerChance->setStartTime(new \DateTimeImmutable());
        $endTime = $campaign->getEndTime();
        if (null !== $endTime) {
            $offerChance->setEndTime($endTime);
        }
        $offerChance->setValid(true);

        $offerSku = new OfferSku();
        $offerSku->setChance($offerChance);
        assert(method_exists($sku, 'getId'), 'SKU must have proper entity interface');
        // 假设SKU实现了预期的Sku实体接口
        /** @var Sku $sku */
        $offerSku->setSku($sku);
        $offerSku->setQuantity(1);

        $this->entityManager->persist($offerChance);
        $this->entityManager->persist($offerSku);
        $this->entityManager->flush();

        return $offerChance;
    }

    /**
     * 处理积分奖励
     *
     * 不考虑并发：积分服务已有原子性保障
     *
     * @param UserInterface $user   the user receiving the reward
     * @param Award         $award  the award being given
     * @param Reward        $reward the reward to process
     */
    private function processCreditReward(UserInterface $user, Award $award, Reward $reward): void
    {
        if (null === $this->accountService || null === $this->transactionService) {
            throw new CreditServiceUnavailableException('积分服务不可用');
        }

        $integralName = $_ENV['DEFAULT_CREDIT_CURRENCY_CODE'] ?? 'CREDIT';
        assert(is_string($integralName), 'Credit currency code must be string');
        $inAccount = $this->accountService->getAccountByUser($user, $integralName);
        $remark = $_ENV['CAMPAIGN_AWARD_CREDIT_REMARK'] ?? $award->getCampaign()->getName();
        assert(is_string($remark), 'Campaign award credit remark must be string');

        $this->transactionService->increase(
            'CAMPAIGN-' . $award->getId() . '-' . Uuid::v4()->toRfc4122(),
            $inAccount,
            intval($award->getValue()),
            $remark,
        );
    }
}
