<?php

declare(strict_types=1);

namespace CampaignBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 活动状态
 */
enum CampaignStatus: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PENDING = 'pending';
    case RUNNING = 'running';
    case CLOSED = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => '未开始',
            self::RUNNING => '进行中',
            self::CLOSED => '已结束',
        };
    }
}
