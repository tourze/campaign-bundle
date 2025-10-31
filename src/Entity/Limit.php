<?php

declare(strict_types=1);

namespace CampaignBundle\Entity;

use CampaignBundle\Enum\LimitType;
use CampaignBundle\Repository\LimitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 表示营销活动限制条件的实体类。
 *
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: LimitRepository::class)]
#[ORM\Table(name: 'campaign_limit', options: ['comment' => '限制条件'])]
class Limit implements \Stringable, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    /**
     * 关联的奖励对象。
     */
    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'limits')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Award $award = null;

    #[Assert\Choice(callback: [LimitType::class, 'cases'])]
    #[ORM\Column(length: 30, enumType: LimitType::class, options: ['comment' => '限制类型'])]
    private LimitType $type;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, options: ['comment' => '条件值'])]
    private string $value = '1';

    #[Assert\Length(max: 1000)]
    #[ORM\Column(length: 1000, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    /**
     * 返回此限制条件的字符串表示。
     *
     * @return string
     */
    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        return "[{$this->getType()->getLabel()}] {$this->getValue()}";
    }

    /**
     * 获取此限制条件的唯一标识符。
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * 获取此限制条件的类型。
     *
     * @return LimitType
     */
    public function getType(): LimitType
    {
        return $this->type;
    }

    /**
     * 设置此限制条件的类型。
     *
     * @param LimitType $type 限制条件类型
     */
    public function setType(LimitType $type): void
    {
        $this->type = $type;
    }

    /**
     * 获取此限制条件的值。
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * 设置此限制条件的值。
     *
     * @param string $value 限制条件的值
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * 获取此限制条件的备注。
     *
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * 设置此限制条件的备注。
     *
     * @param string|null $remark 备注信息
     */
    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * 获取关联的奖励对象。
     *
     * @return Award|null
     */
    public function getAward(): ?Award
    {
        return $this->award;
    }

    /**
     * 设置关联的奖励对象。
     *
     * @param Award|null $award 奖励对象
     */
    public function setAward(?Award $award): void
    {
        $this->award = $award;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'value' => $this->getValue(),
            'type' => $this->getType(),
            'remark' => $this->getRemark(),
        ];
    }
}
