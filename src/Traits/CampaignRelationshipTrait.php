<?php

declare(strict_types=1);

namespace CampaignBundle\Traits;

use CampaignBundle\Entity\Attribute;
use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Chance;
use CampaignBundle\Entity\EventLog;
use CampaignBundle\Entity\Reward;
use Doctrine\Common\Collections\Collection;

/**
 * 营销活动实体关联关系管理的特征类。
 *
 * 管理营销活动实体的关联关系，包括权益奖励、奖励记录、机会、事件日志和属性。
 * 遵循单一职责原则，专门处理实体间的关联操作。
 */
trait CampaignRelationshipTrait
{
    /**
     * 获取事件日志集合。
     *
     * @return Collection<int, EventLog>
     */
    public function getEventLogs(): Collection
    {
        return $this->eventLogs;
    }

    /**
     * 添加事件日志。
     *
     * @param EventLog $log 事件日志对象
     * @return self
     */
    public function addEventLog(EventLog $log): self
    {
        if (!$this->eventLogs->contains($log)) {
            $this->eventLogs->add($log);
            $log->setCampaign($this);
        }

        return $this;
    }

    /**
     * 移除事件日志。
     *
     * @param EventLog $log 事件日志对象
     * @return self
     */
    public function removeEventLog(EventLog $log): self
    {
        if ($this->eventLogs->removeElement($log)) {
            // 设置关联方为null（除非已经更改）
            if ($log->getCampaign() === $this) {
                $log->setCampaign(null);
            }
        }

        return $this;
    }

    /**
     * 获取权益奖励集合。
     *
     * @return Collection<int, Award>
     */
    public function getAwards(): Collection
    {
        return $this->awards;
    }

    /**
     * 添加权益奖励。
     *
     * @param Award $award 权益奖励对象
     * @return self
     */
    public function addAward(Award $award): self
    {
        if (!$this->awards->contains($award)) {
            $this->awards->add($award);
            $award->setCampaign($this);
        }

        return $this;
    }

    /**
     * 移除权益奖励。
     *
     * @param Award $award 权益奖励对象
     * @return self
     */
    public function removeAward(Award $award): self
    {
        $this->awards->removeElement($award);

        return $this;
    }

    /**
     * 获取奖励记录集合。
     *
     * @return Collection<int, Reward>
     */
    public function getRewards(): Collection
    {
        return $this->rewards;
    }

    /**
     * 添加奖励记录。
     *
     * @param Reward $reward 奖励记录对象
     * @return self
     */
    public function addReward(Reward $reward): self
    {
        if (!$this->rewards->contains($reward)) {
            $this->rewards->add($reward);
            $reward->setCampaign($this);
        }

        return $this;
    }

    /**
     * 移除奖励记录。
     *
     * @param Reward $reward 奖励记录对象
     * @return self
     */
    public function removeReward(Reward $reward): self
    {
        if ($this->rewards->removeElement($reward)) {
            // 设置关联方为null（除非已经更改）
            if ($reward->getCampaign() === $this) {
                $reward->setCampaign(null);
            }
        }

        return $this;
    }

    /**
     * 获取机会集合。
     *
     * @return Collection<int, Chance>
     */
    public function getChances(): Collection
    {
        return $this->chances;
    }

    /**
     * 添加机会。
     *
     * @param Chance $chance 机会对象
     * @return self
     */
    public function addChance(Chance $chance): self
    {
        if (!$this->chances->contains($chance)) {
            $this->chances->add($chance);
            $chance->setCampaign($this);
        }

        return $this;
    }

    /**
     * 移除机会。
     *
     * @param Chance $chance 机会对象
     * @return self
     */
    public function removeChance(Chance $chance): self
    {
        if ($this->chances->removeElement($chance)) {
            // 设置关联方为null（除非已经更改）
            if ($chance->getCampaign() === $this) {
                $chance->setCampaign(null);
            }
        }

        return $this;
    }

    /**
     * 获取属性集合。
     *
     * @return Collection<int, Attribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * 添加属性。
     *
     * @param Attribute $attribute 属性对象
     * @return static
     */
    public function addAttribute(Attribute $attribute): static
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
            $attribute->setCampaign($this);
        }

        return $this;
    }

    /**
     * 移除属性。
     *
     * @param Attribute $attribute 属性对象
     * @return static
     */
    public function removeAttribute(Attribute $attribute): static
    {
        if ($this->attributes->removeElement($attribute)) {
            // 设置关联方为null（除非已经更改）
            if ($attribute->getCampaign() === $this) {
                $attribute->setCampaign(null);
            }
        }

        return $this;
    }
}
