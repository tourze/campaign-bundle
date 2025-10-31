<?php

declare(strict_types=1);

namespace CampaignBundle\Entity;

use CampaignBundle\Enum\AwardType;
use CampaignBundle\Repository\RewardRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 表示营销活动奖励记录的实体类。
 *
 * @implements ApiArrayInterface<string, mixed>
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
    #[Assert\Type(type: 'bool')]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[ORM\Column(type: Types::STRING, length: 120, unique: true, options: ['comment' => '奖品序列号'])]
    private ?string $sn = null;

    #[ORM\ManyToOne(targetEntity: Campaign::class, inversedBy: 'rewards', cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Campaign $campaign = null;

    #[ORM\ManyToOne(targetEntity: Award::class, cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Award $award = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?UserInterface $user = null;

    #[Assert\Choice(callback: [AwardType::class, 'cases'])]
    #[ORM\Column(type: Types::STRING, length: 30, enumType: AwardType::class, options: ['comment' => '权益类型'])]
    private ?AwardType $type = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '权益数据'])]
    private ?string $value = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[Assert\Length(max: 150)]
    #[ORM\Column(type: Types::STRING, length: 150, nullable: true, options: ['comment' => '渠道信息'])]
    private ?string $businessChannel = null;

    /**
     * 返回奖励记录的字符串表示。
     *
     * @return string
     */
    public function __toString(): string
    {
        if (null === $this->getId() || '0' === $this->getId()) {
            return '';
        }

        return $this->getId();
    }

    /**
     * 检查奖励记录是否有效。
     *
     * @return bool|null
     */
    public function isValid(): ?bool
    {
        return $this->valid;
    }

    /**
     * 设置奖励记录的有效性。
     *
     * @param bool|null $valid 是否有效
     */
    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * 获取备注信息。
     *
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * 设置备注信息。
     *
     * @param string|null $remark 备注信息
     */
    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * 获取关联的营销活动。
     *
     * @return Campaign|null
     */
    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    /**
     * 设置关联的营销活动。
     *
     * @param Campaign|null $campaign 营销活动对象
     */
    public function setCampaign(?Campaign $campaign): void
    {
        $this->campaign = $campaign;
    }

    /**
     * 获取关联的权益奖励。
     *
     * @return Award|null
     */
    public function getAward(): ?Award
    {
        return $this->award;
    }

    /**
     * 设置关联的权益奖励。
     *
     * @param Award|null $award 权益奖励对象
     */
    public function setAward(?Award $award): void
    {
        $this->award = $award;
    }

    /**
     * 获取关联的用户。
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * 设置关联的用户。
     *
     * @param UserInterface|null $user 用户对象
     */
    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * 获取权益类型。
     *
     * @return AwardType|null
     */
    public function getType(): ?AwardType
    {
        return $this->type;
    }

    /**
     * 设置权益类型。
     *
     * @param AwardType $type 权益类型
     */
    public function setType(AwardType $type): void
    {
        $this->type = $type;
    }

    /**
     * 获取权益数据。
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * 设置权益数据。
     *
     * @param string $value 权益数据
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * 获取奖品序列号。
     *
     * @return string|null
     */
    public function getSn(): ?string
    {
        return $this->sn;
    }

    /**
     * 设置奖品序列号。
     *
     * @param string $sn 序列号
     */
    public function setSn(string $sn): void
    {
        $this->sn = $sn;
    }

    /**
     * 获取渠道信息。
     *
     * @return string|null
     */
    public function getBusinessChannel(): ?string
    {
        return $this->businessChannel;
    }

    /**
     * 设置渠道信息。
     *
     * @param string|null $businessChannel 渠道信息
     */
    public function setBusinessChannel(?string $businessChannel): void
    {
        $this->businessChannel = $businessChannel;
    }

    /**
     * 返回 API 数组表示。
     *
     * @return array<string, mixed>
     */
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
