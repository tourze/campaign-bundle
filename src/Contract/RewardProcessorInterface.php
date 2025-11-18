<?php

declare(strict_types=1);

namespace CampaignBundle\Contract;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardType;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 奖励处理器接口
 *
 * 所有奖励类型的处理器必须实现此接口。
 * 通过 Symfony 的 Tagged Services 机制自动注册到 RewardProcessorRegistry。
 *
 * 实现示例：
 * ```php
 * class CouponRewardProcessor implements RewardProcessorInterface
 * {
 *     public function supports(AwardType $type): bool
 *     {
 *         return AwardType::COUPON === $type;
 *     }
 *
 *     public function process(UserInterface $user, Award $award, Reward $reward): void
 *     {
 *         // 处理优惠券发放逻辑
 *     }
 *
 *     public function getPriority(): int
 *     {
 *         return 0;
 *     }
 * }
 * ```
 *
 * @see \CampaignBundle\Service\RewardProcessorRegistry
 */
interface RewardProcessorInterface
{
    /**
     * 检查是否支持指定的奖励类型
     *
     * @param AwardType $type 奖励类型
     *
     * @return bool 是否支持该类型
     */
    public function supports(AwardType $type): bool;

    /**
     * 处理奖励发放
     *
     * 此方法负责执行奖励的具体发放逻辑，例如：
     * - 发送优惠券
     * - 增加积分
     * - 创建商品购买资格
     *
     * 处理过程中应该：
     * 1. 验证库存或额度
     * 2. 执行发放操作
     * 3. 更新 $reward 对象的 sn（流水号）等字段
     * 4. 记录日志
     *
     * @param UserInterface $user   接收奖励的用户
     * @param Award         $award  奖励配置（包含奖励类型、值等信息）
     * @param Reward        $reward 奖励记录（需要更新 sn 等信息）
     *
     * @throws \Exception 处理失败时应抛出具体的业务异常
     */
    public function process(UserInterface $user, Award $award, Reward $reward): void;

    /**
     * 获取处理器优先级
     *
     * 当多个处理器都支持同一类型时，优先级高的处理器将被优先使用。
     * 这允许在扩展包中覆盖默认实现。
     *
     * @return int 优先级（数字越大优先级越高，推荐范围 -100 到 100，默认 0）
     */
    public function getPriority(): int;
}
