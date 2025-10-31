<?php

declare(strict_types=1);

namespace CampaignBundle\Entity;

use CampaignBundle\Repository\ChanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 表示营销活动机会的实体类。
 *
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: ChanceRepository::class)]
#[ORM\Table(name: 'campaign_chance', options: ['comment' => '机会信息'])]
class Chance implements \Stringable, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;

    #[IndexColumn]
    #[TrackColumn]
    #[Assert\Type(type: 'bool')]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    private ?array $context = [];

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?UserInterface $user = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(inversedBy: 'chances', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Campaign $campaign = null;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '过期时间'])]
    private ?\DateTimeInterface $expireTime = null;

    #[Assert\DateTime]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '使用时间'])]
    private ?\DateTimeInterface $useTime = null;

    #[Groups(groups: ['admin_curd'])]
    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注', 'default' => ''])]
    private ?string $remark = null;

    /**
     * 返回机会的字符串表示。
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getId() ?? '';
    }

    /**
     * 检查机会是否有效。
     *
     * @return bool|null
     */
    public function isValid(): ?bool
    {
        return $this->valid;
    }

    /**
     * 设置机会的有效性。
     *
     * @param bool|null $valid 是否有效
     */
    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * 获取上下文数据。
     *
     * @return array<string, mixed>|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    /**
     * 设置上下文数据。
     *
     * @param array<string, mixed>|null $context 上下文数据
     */
    public function setContext(?array $context): void
    {
        $this->context = $context;
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
     * 获取开始时间。
     *
     * @return \DateTimeInterface|null
     */
    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    /**
     * 设置开始时间。
     *
     * @param \DateTimeInterface $startTime 开始时间
     */
    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * 获取过期时间。
     *
     * @return \DateTimeInterface|null
     */
    public function getExpireTime(): ?\DateTimeInterface
    {
        return $this->expireTime;
    }

    /**
     * 设置过期时间。
     *
     * @param \DateTimeInterface $expireTime 过期时间
     */
    public function setExpireTime(\DateTimeInterface $expireTime): void
    {
        $this->expireTime = $expireTime;
    }

    /**
     * 获取使用时间。
     *
     * @return \DateTimeInterface|null
     */
    public function getUseTime(): ?\DateTimeInterface
    {
        return $this->useTime;
    }

    /**
     * 设置使用时间。
     *
     * @param \DateTimeInterface|null $useTime 使用时间
     */
    public function setUseTime(?\DateTimeInterface $useTime): void
    {
        $this->useTime = $useTime;
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
     * 返回 API 数组表示。
     *
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'expireTime' => $this->getExpireTime()?->format('Y-m-d H:i:s'),
            'useTime' => $this->getUseTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
