<?php

declare(strict_types=1);

namespace CampaignBundle\Entity;

use CampaignBundle\Repository\CampaignRepository;
use CampaignBundle\Traits\CampaignBusinessTrait;
use CampaignBundle\Traits\CampaignRelationshipTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 表示营销活动的实体类。
 *
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: CampaignRepository::class)]
#[ORM\Table(name: 'campaign_main', options: ['comment' => '活动管理'])]
class Campaign implements \Stringable, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use CampaignRelationshipTrait;
    use CampaignBusinessTrait;

    #[Assert\PositiveOrZero]
    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    private ?int $sortNumber = 0;

    /**
     * 获取次序值。
     *
     * @return int|null
     */
    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    /**
     * 设置次序值。
     *
     * @param int|null $sortNumber 次序值
     */
    public function setSortNumber(?int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
    }

    /**
     * 返回排序数组。
     *
     * @return array<string, mixed>
     */
    public function retrieveSortableArray(): array
    {
        return [
            'sortNumber' => $this->getSortNumber(),
        ];
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Assert\Type(type: 'bool')]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    private ?Category $category = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(groups: ['restful_read'])]
    #[SnowflakeColumn(prefix: 'CMP')]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '代号'])]
    private ?string $code = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '活动名'])]
    private ?string $name = null;

    #[Assert\Length(max: 120)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '副标题'])]
    private ?string $subtitle = null;

    #[Assert\Length(max: 255)]
    #[Assert\Url]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '缩略图'])]
    private ?string $thumbUrl = null;

    #[Assert\NotNull]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: 'startTime')]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    /**
     * @var array<string>|null
     */
    #[Assert\Type(type: 'array')]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[Assert\Length(max: 65535)]
    #[Assert\Url]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '入口地址'])]
    private ?string $entryUrl = null;

    /**
     * 关联的权益奖励集合。
     *
     * @var Collection<int, Award>
     */
    #[Ignore]
    #[Groups(groups: ['restful_read'])]
    #[ORM\OneToMany(mappedBy: 'campaign', targetEntity: Award::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $awards;

    /**
     * 关联的事件日志集合。
     *
     * @var Collection<int, EventLog>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'campaign', targetEntity: EventLog::class)]
    private Collection $eventLogs;

    #[Assert\Length(max: 1000)]
    #[Assert\Url]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '分享图'])]
    private ?string $shareImg = null;

    #[Assert\Length(max: 1000)]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '分享文案'])]
    private ?string $shareTitle = null;

    /**
     * 关联的奖励集合。
     *
     * @var Collection<int, Reward>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'campaign', targetEntity: Reward::class)]
    private Collection $rewards;

    /**
     * 关联的机会集合。
     *
     * @var Collection<int, Chance>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'campaign', targetEntity: Chance::class, cascade: ['persist'])]
    private Collection $chances;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '请求机会时执行表达式'])]
    private ?string $requestExpression = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(nullable: false, options: ['comment' => '是否推荐'])]
    private ?bool $recommend = false;

    /**
     * 关联的属性集合。
     *
     * @var Collection<int, Attribute>
     */
    #[ORM\OneToMany(mappedBy: 'campaign', targetEntity: Attribute::class)]
    private Collection $attributes;

    public function __construct()
    {
        $this->eventLogs = new ArrayCollection();
        $this->awards = new ArrayCollection();
        $this->rewards = new ArrayCollection();
        $this->chances = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

    /**
     * 获取营销活动的唯一标识符。
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * 返回营销活动的字符串表示。
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    /**
     * 检查营销活动是否有效。
     *
     * @return bool|null
     */
    public function isValid(): ?bool
    {
        return $this->valid;
    }

    /**
     * 设置营销活动的有效性。
     *
     * @param bool|null $valid 是否有效
     */
    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * 获取营销活动的代号。
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * 设置营销活动的代号。
     *
     * @param string $code 活动代号
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * 获取营销活动的名称。
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * 设置营销活动的名称。
     *
     * @param string $name 活动名称
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * 获取营销活动的副标题。
     *
     * @return string|null
     */
    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    /**
     * 设置营销活动的副标题。
     *
     * @param string|null $subtitle 副标题
     */
    public function setSubtitle(?string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    /**
     * 获取营销活动的缩略图URL。
     *
     * @return string|null
     */
    public function getThumbUrl(): ?string
    {
        return $this->thumbUrl;
    }

    /**
     * 设置营销活动的缩略图URL。
     *
     * @param string|null $thumbUrl 缩略图URL
     */
    public function setThumbUrl(?string $thumbUrl): void
    {
        $this->thumbUrl = $thumbUrl;
    }

    /**
     * 获取营销活动的开始时间。
     *
     * @return \DateTimeInterface|null
     */
    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    /**
     * 设置营销活动的开始时间。
     *
     * @param \DateTimeInterface $startTime 开始时间
     */
    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * 获取营销活动的结束时间。
     *
     * @return \DateTimeInterface|null
     */
    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    /**
     * 设置营销活动的结束时间。
     *
     * @param \DateTimeInterface $endTime 结束时间
     */
    public function setEndTime(\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * 获取营销活动的标签。
     *
     * @return array<string>|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * 设置营销活动的标签。
     *
     * @param array<string>|null $tags 标签数组
     */
    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * 获取营销活动的入口地址。
     *
     * @return string|null
     */
    public function getEntryUrl(): ?string
    {
        return $this->entryUrl;
    }

    /**
     * 设置营销活动的入口地址。
     *
     * @param string|null $entryUrl 入口地址
     */
    public function setEntryUrl(?string $entryUrl): void
    {
        $this->entryUrl = $entryUrl;
    }

    /**
     * 获取营销活动的分享图片URL。
     *
     * @return string|null
     */
    public function getShareImg(): ?string
    {
        return $this->shareImg;
    }

    /**
     * 设置营销活动的分享图片URL。
     *
     * @param string|null $shareImg 分享图片URL
     */
    public function setShareImg(?string $shareImg): void
    {
        $this->shareImg = $shareImg;
    }

    /**
     * 获取营销活动的分享标题。
     *
     * @return string|null
     */
    public function getShareTitle(): ?string
    {
        return $this->shareTitle;
    }

    /**
     * 设置营销活动的分享标题。
     *
     * @param string|null $shareTitle 分享标题
     */
    public function setShareTitle(?string $shareTitle): void
    {
        $this->shareTitle = $shareTitle;
    }

    /**
     * 获取请求机会时执行的表达式。
     *
     * @return string|null
     */
    public function getRequestExpression(): ?string
    {
        return $this->requestExpression;
    }

    /**
     * 设置请求机会时执行的表达式。
     *
     * @param string|null $requestExpression 表达式
     */
    public function setRequestExpression(?string $requestExpression): void
    {
        $this->requestExpression = $requestExpression;
    }

    /**
     * 检查营销活动是否被推荐。
     *
     * @return bool|null
     */
    public function isRecommend(): ?bool
    {
        return $this->recommend;
    }

    /**
     * 设置营销活动的推荐状态。
     *
     * @param bool|null $recommend 是否推荐
     */
    public function setRecommend(?bool $recommend): void
    {
        $this->recommend = $recommend;
    }

    /**
     * 获取营销活动的分类。
     *
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * 设置营销活动的分类。
     *
     * @param Category|null $category 分类对象
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }
}
