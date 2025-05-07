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
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '权益奖励配置')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: AwardRepository::class)]
#[ORM\Table(name: 'campaign_award', options: ['comment' => '权益奖励配置'])]
class Award implements \Stringable, AdminArrayInterface
{
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[CreatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Campaign::class, inversedBy: 'awards')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Campaign $campaign;

    /**
     * @var string 这里事件的意思是，触发了这个事件就会得到指定的奖励
     */
    #[ListColumn]
    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '事件'])]
    private string $event = 'join';

    #[ListColumn]
    #[FormField(span: 6)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 30, enumType: AwardType::class, options: ['comment' => '权益类型'])]
    private AwardType $type;

    #[ListColumn]
    #[FormField(span: 8)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '权益ID'])]
    private string $value;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    /**
     * @DynamicFieldSet
     *
     * @var Collection<Limit>
     */
    #[ListColumn(title: '限制规则')]
    #[FormField(title: '限制规则')]
    #[ORM\OneToMany(mappedBy: 'award', targetEntity: Limit::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $limits;

    #[FormField(span: 8, required: true)]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '总数量'])]
    private ?int $prizeQuantity = 0;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 100, nullable: true, enumType: AwardLimitType::class, options: ['comment' => '领取限制类型'])]
    private ?AwardLimitType $awardLimitType = AwardLimitType::BUY_TOTAL;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(nullable: true, options: ['comment' => '领取限制次数', 'default' => 1])]
    private ?int $times = null;

    public function __construct()
    {
        $this->limits = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
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

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
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
