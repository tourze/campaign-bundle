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
enum LimitType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case USER_TAG = 'user-tag';
    case CHANCE = 'chance';

    public function getLabel(): string
    {
        return match ($this) {
            self::USER_TAG => '用户标签',
            self::CHANCE => '机会次数',
        };
    }
}
