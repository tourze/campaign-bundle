<?php

declare(strict_types=1);

namespace CampaignBundle\Entity;

use CampaignBundle\Enum\AwardLimitType;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Repository\AwardRepository;
use CampaignBundle\Traits\AwardBusinessTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * 表示权益奖励配置的实体类。
 *
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AwardRepository::class)]
#[ORM\Table(name: 'campaign_award', options: ['comment' => '权益奖励配置'])]
class Award implements \Stringable, AdminArrayInterface
{
    use TimestampableAware;
    use AwardBusinessTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Campaign::class, inversedBy: 'awards', cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Campaign $campaign;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '事件'])]
    private string $event = 'join';

    #[Assert\NotNull]
    #[Assert\Choice(callback: [AwardType::class, 'cases'])]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 30, enumType: AwardType::class, options: ['comment' => '权益类型'])]
    private AwardType $type;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '权益ID'])]
    private string $value;

    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    /**
     * 关联的限制条件集合。
     *
     * @var Collection<int, Limit>
     */
    #[ORM\OneToMany(mappedBy: 'award', targetEntity: Limit::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $limits;

    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '总数量'])]
    private ?int $prizeQuantity = 0;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [AwardLimitType::class, 'cases'])]
    #[ORM\Column(length: 100, enumType: AwardLimitType::class, options: ['comment' => '领取限制类型', 'default' => 'buy_total'])]
    private AwardLimitType $awardLimitType = AwardLimitType::BUY_TOTAL;

    #[Assert\PositiveOrZero]
    #[ORM\Column(nullable: true, options: ['comment' => '领取限制次数', 'default' => 1])]
    private ?int $times = null;

    public function __construct()
    {
        $this->limits = new ArrayCollection();
    }

    /**
     * 获取权益奖励的唯一标识符。
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * 获取关联的营销活动。
     *
     * @return Campaign
     */
    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }

    /**
     * 设置关联的营销活动。
     *
     * @param Campaign $campaign 营销活动对象
     */
    public function setCampaign(Campaign $campaign): void
    {
        $this->campaign = $campaign;
    }

    /**
     * 获取权益奖励的类型。
     *
     * @return AwardType
     */
    public function getType(): AwardType
    {
        return $this->type;
    }

    /**
     * 设置权益奖励的类型。
     *
     * @param AwardType $type 权益类型
     */
    public function setType(AwardType $type): void
    {
        $this->type = $type;
    }

    /**
     * 获取权益奖励的值。
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * 设置权益奖励的值。
     *
     * @param string $value 权益值
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * 获取权益奖励的备注。
     *
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * 设置权益奖励的备注。
     *
     * @param string|null $remark 备注信息
     */
    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * 获取触发奖励的事件名称。
     *
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * 设置触发奖励的事件名称。
     *
     * @param string $event 事件名称
     */
    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    /**
     * 获取关联的限制条件集合。
     *
     * @return Collection<int, Limit>
     */
    public function getLimits(): Collection
    {
        return $this->limits;
    }

    /**
     * 添加限制条件。
     *
     * @param Limit $limit 限制条件对象
     */
    public function addLimit(Limit $limit): void
    {
        if (!$this->limits->contains($limit)) {
            $this->limits->add($limit);
            $limit->setAward($this);
        }
    }

    /**
     * 移除限制条件。
     *
     * @param Limit $limit 限制条件对象
     */
    public function removeLimit(Limit $limit): void
    {
        if ($this->limits->removeElement($limit)) {
            // 设置关联方为null（除非已经更改）
            if ($limit->getAward() === $this) {
                $limit->setAward(null);
            }
        }
    }

    /**
     * 获取领取限制次数。
     *
     * @return int|null
     */
    public function getTimes(): ?int
    {
        return $this->times;
    }

    /**
     * 设置领取限制次数。
     *
     * @param int|null $times 限制次数
     */
    public function setTimes(?int $times): void
    {
        $this->times = $times;
    }

    /**
     * 获取领取限制类型。
     *
     * @return AwardLimitType|null
     */
    public function getAwardLimitType(): ?AwardLimitType
    {
        return $this->awardLimitType;
    }

    /**
     * 设置领取限制类型。
     *
     * @param AwardLimitType $awardLimitType 限制类型
     */
    public function setAwardLimitType(AwardLimitType $awardLimitType): void
    {
        $this->awardLimitType = $awardLimitType;
    }

    /**
     * 获取总数量。
     *
     * @return int|null
     */
    public function getPrizeQuantity(): ?int
    {
        return $this->prizeQuantity;
    }

    /**
     * 设置总数量。
     *
     * @param int|null $prizeQuantity 总数量
     */
    public function setPrizeQuantity(?int $prizeQuantity): void
    {
        $this->prizeQuantity = $prizeQuantity;
    }
}
