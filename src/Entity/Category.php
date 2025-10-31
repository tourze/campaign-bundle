<?php

declare(strict_types=1);

namespace CampaignBundle\Entity;

use CampaignBundle\Repository\CategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 表示营销活动分类的实体类。
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'campaign_category', options: ['comment' => '目录'])]
class Category implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[Assert\NotBlank]
    #[Assert\Length(max: 60)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(length: 60, unique: true, options: ['comment' => '目录名'])]
    private ?string $title = null;

    #[Assert\Type(type: 'bool')]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Assert\PositiveOrZero]
    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    private ?int $sortNumber = 0;

    /**
     * 返回分类的字符串表示。
     *
     * @return string
     */
    public function __toString(): string
    {
        if (null === $this->getId() || '0' === $this->getId()) {
            return '';
        }

        return $this->getTitle() ?? '';
    }

    /**
     * 检查分类是否有效。
     *
     * @return bool|null
     */
    public function isValid(): ?bool
    {
        return $this->valid;
    }

    /**
     * 设置分类的有效性。
     *
     * @param bool|null $valid 是否有效
     */
    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * 获取分类的名称。
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * 设置分类的名称。
     *
     * @param string $title 分类名称
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * 获取分类的次序值。
     *
     * @return int|null
     */
    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    /**
     * 设置分类的次序值。
     *
     * @param int|null $sortNumber 次序值
     */
    public function setSortNumber(?int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
    }
}
