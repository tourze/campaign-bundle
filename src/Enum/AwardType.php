<?php

namespace CampaignBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 奖励类型
 */
enum AwardType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    // 旧的几个枚举，因为定制那边使用了，所以暂时不能删除，我们只能先这样写死
    case COUPON = 'coupon';
    case COUPON_LOCAL = 'coupon_local';
    case SPU_QUALIFICATION = 'spu-qualification';
    case SKU_QUALIFICATION = 'sku-qualification';
    case CREDIT = 'credit';

    public function getLabel(): string
    {
        return match ($this) {
            self::COUPON => '优惠券',
            self::COUPON_LOCAL => '本地优惠券',
            self::SPU_QUALIFICATION => 'SPU资格',
            self::SKU_QUALIFICATION => 'SKU资格',
            self::CREDIT => '积分',
        };
    }
}
