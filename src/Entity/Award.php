<?php

namespace CampaignBundle\Entity;

use AntdCpBundle\Builder\Field\DynamicFieldSet;
use CampaignBundle\Enum\AwardLimitType;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Repository\AwardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: AwardRepository::class)]
#[ORM\Table(name: 'campaign_award', options: ['comment' => '权益奖励配置'])]
class Award implements \Stringable, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;



    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Campaign::class, inversedBy: 'awards')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Campaign $campaign;

    /**
     * @var string 这里事件的意思是，触发了这个事件就会得到指定的奖励
     */
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '事件'])]
    private string $event = 'join';

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 30, enumType: AwardType::class, options: ['comment' => '权益类型'])]
    private AwardType $type;

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '权益ID'])]
    private string $value;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    /**
     * @var Collection<Limit>
     */
    #[ORM\OneToMany(mappedBy: 'award', targetEntity: Limit::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $limits;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '总数量'])]
    private ?int $prizeQuantity = 0;

    #[ORM\Column(length: 100, enumType: AwardLimitType::class, options: ['comment' => '领取限制类型', 'default' => 'buy_total'])]
    private AwardLimitType $awardLimitType = AwardLimitType::BUY_TOTAL;

    #[ORM\Column(nullable: true, options: ['comment' => '领取限制次数', 'default' => 1])]
    private ?int $times = null;

    public function __construct()
    {
        $this->limits = new ArrayCollection();
    }

    public function __toString(): string
    {
        if ($this->getId() === null || $this->getId() === 0) {
            return '';
        }

        $rs = "{$this->getEvent()} {$this->getType()->getLabel()} {$this->getValue()}";
        if (!empty($this->getRemark())) {
            $rs = "{$rs}({$this->getRemark()})";
        }

        return $rs;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }

    public function setCampaign(Campaign $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }

    public function getType(): AwardType
    {
        return $this->type;
    }

    public function setType(AwardType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): self
    {
        $this->remark = $remark;

        return $this;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Collection<int, Limit>
     */
    public function getLimits(): Collection
    {
        return $this->limits;
    }

    public function addLimit(Limit $limit): static
    {
        if (!$this->limits->contains($limit)) {
            $this->limits->add($limit);
            $limit->setAward($this);
        }

        return $this;
    }

    public function removeLimit(Limit $limit): static
    {
        if ($this->limits->removeElement($limit)) {
            // set the owning side to null (unless already changed)
            if ($limit->getAward() === $this) {
                $limit->setAward(null);
            }
        }

        return $this;
    }

    public function getTimes(): ?int
    {
        return $this->times;
    }

    public function setTimes(?int $times): static
    {
        $this->times = $times;

        return $this;
    }

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

    public function getAwardLimitType(): ?AwardLimitType
    {
        return $this->awardLimitType;
    }

    public function setAwardLimitType(AwardLimitType $awardLimitType): static
    {
        $this->awardLimitType = $awardLimitType;

        return $this;
    }

    public function getPrizeQuantity(): ?int
    {
        return $this->prizeQuantity;
    }

    public function setPrizeQuantity(?int $prizeQuantity): void
    {
        $this->prizeQuantity = $prizeQuantity;
    }
}
