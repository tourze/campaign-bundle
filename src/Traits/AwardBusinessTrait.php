<?php

declare(strict_types=1);

namespace CampaignBundle\Traits;

/**
 * 权益奖励业务逻辑的特征类。
 *
 * 管理权益奖励实体的业务逻辑，包括字符串表示和数组转换方法。
 * 遵循单一职责原则，专门处理业务相关的表示和转换操作。
 */
trait AwardBusinessTrait
{
    /**
     * 返回权益奖励的字符串表示。
     *
     * @return string
     */
    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        $rs = "{$this->getEvent()} {$this->getType()->getLabel()} {$this->getValue()}";
        if (null !== $this->getRemark() && '' !== $this->getRemark()) {
            $rs = "{$rs}({$this->getRemark()})";
        }

        return $rs;
    }

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
            'type' => $this->getType(),
            'event' => $this->getEvent(),
            'value' => $this->getValue(),
            'remark' => $this->getRemark(),
            'limits' => $this->getLimits(),
            'times' => $this->getTimes(),
            'awardLimitType' => $this->getAwardLimitType(),
            'prizeQuantity' => $this->getPrizeQuantity(),
        ];
    }

    /**
     * 返回 RESTful API 读取数组。
     *
     * @return array<string, mixed>
     */
    public function restfulReadArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'type' => $this->getType(),
            'event' => $this->getEvent(),
            'value' => $this->getValue(),
        ];
    }
}
