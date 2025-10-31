<?php

declare(strict_types=1);

namespace CampaignBundle\Traits;

use CampaignBundle\Enum\CampaignStatus;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * 营销活动业务逻辑的特征类。
 *
 * 管理营销活动实体的业务逻辑，包括状态计算、倒计时计算和数组转换方法。
 * 遵循单一职责原则，专门处理业务相关的计算和转换操作。
 */
trait CampaignBusinessTrait
{
    /**
     * 返回管理数组表示。
     *
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            ...$this->retrieveSortableArray(),
            'code' => $this->getCode(),
            'name' => $this->getName(),
            'thumbUrl' => $this->getThumbUrl(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'subtitle' => $this->getSubtitle(),
            'tags' => $this->getTags(),
        ];
    }

    /**
     * 返回 RESTful API 读取数组。
     *
     * @return array<string, mixed>
     */
    public function restfulReadArray(): array
    {
        $res = [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            ...$this->retrieveSortableArray(),
            'code' => $this->getCode(),
            'name' => $this->getName(),
            'thumbUrl' => $this->getThumbUrl(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'subtitle' => $this->getSubtitle(),
            'tags' => $this->getTags(),
            'startCountdown' => $this->getStartCountdown(),
            'closeCountdown' => $this->getCloseCountdown(),
            'status' => $this->getStatus(),
            'statusText' => $this->getStatusText(),
        ];

        $awards = [];
        foreach ($this->getAwards() as $award) {
            $awards[] = $award->restfulReadArray();
        }
        $res['awards'] = $awards;

        return $res;
    }

    /**
     * 获取开始倒计时。
     *
     * @return int 开始倒计时秒数
     */
    #[Groups(groups: ['restful_read'])]
    public function getStartCountdown(): int
    {
        if (CampaignStatus::PENDING === $this->getStatus() && null !== $this->getStartTime()) {
            return abs((new \DateTimeImmutable())->getTimestamp() - $this->getStartTime()->getTimestamp());
        }

        return 0;
    }

    /**
     * 获取营销活动的状态。
     *
     * @return CampaignStatus
     */
    #[Groups(groups: ['restful_read'])]
    public function getStatus(): CampaignStatus
    {
        $now = new \DateTimeImmutable();
        if (null !== $this->getEndTime() && $now > $this->getEndTime()) {
            return CampaignStatus::CLOSED;
        }
        if (null !== $this->getStartTime() && $now < $this->getStartTime()) {
            return CampaignStatus::PENDING;
        }

        return CampaignStatus::RUNNING;
    }

    /**
     * 获取结束倒计时。
     *
     * @return int 结束倒计时秒数
     */
    #[Groups(groups: ['restful_read'])]
    public function getCloseCountdown(): int
    {
        if (CampaignStatus::RUNNING === $this->getStatus() && null !== $this->getEndTime()) {
            return abs($this->getEndTime()->getTimestamp() - (new \DateTimeImmutable())->getTimestamp());
        }

        return 0;
    }

    /**
     * 获取营销活动状态的文本描述。
     *
     * @return string
     */
    #[Groups(groups: ['restful_read'])]
    public function getStatusText(): string
    {
        return $this->getStatus()->getLabel();
    }
}
