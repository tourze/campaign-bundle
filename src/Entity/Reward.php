<?php

namespace CampaignBundle\Entity;

use CampaignBundle\Enum\AwardType;
use CampaignBundle\Repository\RewardRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 同一个人在同一个活动中，能否领取同一个奖品多次？
 */
#[ORM\Entity(repositoryClass: RewardRepository::class)]
#[ORM\Table(name: 'campaign_reward', options: ['comment' => '活动奖励记录'])]
class Reward implements \Stringable, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[ORM\Column(type: Types::STRING, length: 120, unique: true, options: ['comment' => '奖品序列号'])]
    private ?string $sn = null;

    #[ORM\ManyToOne(targetEntity: Campaign::class, inversedBy: 'rewards')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Campaign $campaign = null;

    #[ORM\ManyToOne(targetEntity: Award::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Award $award = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?UserInterface $user = null;

    #[ORM\Column(type: Types::STRING, length: 30, enumType: AwardType::class, options: ['comment' => '权益类型'])]
    private ?AwardType $type = null;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '权益数据'])]
    private ?string $value = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[ORM\Column(type: Types::STRING, length: 150, nullable: true, options: ['comment' => '渠道信息'])]
    private ?string $businessChannel = null;

    public function __toString(): string
    {
        if ($this->getId() === null || $this->getId() === '0') {
            return '';
        }

        return $this->getId();
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

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

    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    public function setCampaign(?Campaign $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }

    public function getAward(): ?Award
    {
        return $this->award;
    }

    public function setAward(?Award $award): self
    {
        $this->award = $award;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?AwardType
    {
        return $this->type;
    }

    public function setType(AwardType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(string $sn): self
    {
        $this->sn = $sn;

        return $this;
    }

    public function getBusinessChannel(): ?string
    {
        return $this->businessChannel;
    }

    public function setBusinessChannel(?string $businessChannel): self
    {
        $this->businessChannel = $businessChannel;

        return $this;
    }

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'sn' => $this->getSn(),
            'type' => $this->getType()?->value,
            'value' => $this->getValue(),
            'valid' => $this->isValid(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
