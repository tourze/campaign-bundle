<?php

namespace CampaignBundle\Entity;

use CampaignBundle\Enum\CampaignStatus;
use CampaignBundle\Repository\CampaignRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: CampaignRepository::class)]
#[ORM\Table(name: 'campaign_main', options: ['comment' => '活动管理'])]
class Campaign implements \Stringable, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    private ?int $sortNumber = 0;

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    public function retrieveSortableArray(): array
    {
        return [
            'sortNumber' => $this->getSortNumber(),
        ];
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;



    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'campaigns')]
    private ?Category $category = null;

    #[Groups(['restful_read'])]
    #[SnowflakeColumn(prefix: 'CMP')]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '代号'])]
    private ?string $code = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '活动名'])]
    private ?string $name = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '副标题'])]
    private ?string $subtitle = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '缩略图'])]
    private ?string $thumbUrl = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '入口地址'])]
    private ?string $entryUrl = null;

    /**
     * @var Collection<Award>
     */
    #[Ignore]
    #[Groups(['restful_read'])]
    #[ORM\OneToMany(mappedBy: 'campaign', targetEntity: Award::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $awards;

    /**
     * @var Collection<EventLog>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'campaign', targetEntity: EventLog::class)]
    private Collection $eventLogs;

    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '分享图'])]
    private ?string $shareImg = null;

    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '分享文案'])]
    private ?string $shareTitle = null;

    /**
     * @var Collection<Reward>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'campaign', targetEntity: Reward::class)]
    private Collection $rewards;

    /**
     * @var Collection<int, Chance>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'campaign', targetEntity: Chance::class)]
    private Collection $chances;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '请求机会时执行表达式'])]
    private ?string $requestExpression = null;

    #[ORM\Column(nullable: false, options: ['comment' => '是否推荐'])]
    private ?bool $recommend = false;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getThumbUrl(): ?string
    {
        return $this->thumbUrl;
    }

    public function setThumbUrl(?string $thumbUrl): self
    {
        $this->thumbUrl = $thumbUrl;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return Collection<int, EventLog>
     */
    public function getEventLogs(): Collection
    {
        return $this->eventLogs;
    }

    public function addEventLog(EventLog $log): self
    {
        if (!$this->eventLogs->contains($log)) {
            $this->eventLogs[] = $log;
            $log->setCampaign($this);
        }

        return $this;
    }

    public function removeEventLog(EventLog $log): self
    {
        if ($this->eventLogs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getCampaign() === $this) {
                $log->setCampaign(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Award>
     */
    public function getAwards(): Collection
    {
        return $this->awards;
    }

    public function addAward(Award $award): self
    {
        if (!$this->awards->contains($award)) {
            $this->awards[] = $award;
            $award->setCampaign($this);
        }

        return $this;
    }

    public function removeAward(Award $award): self
    {
        $this->awards->removeElement($award);

        return $this;
    }

    /**
     * @return Collection<int, Reward>
     */
    public function getRewards(): Collection
    {
        return $this->rewards;
    }

    public function addReward(Reward $reward): self
    {
        if (!$this->rewards->contains($reward)) {
            $this->rewards[] = $reward;
            $reward->setCampaign($this);
        }

        return $this;
    }

    public function removeReward(Reward $reward): self
    {
        if ($this->rewards->removeElement($reward)) {
            // set the owning side to null (unless already changed)
            if ($reward->getCampaign() === $this) {
                $reward->setCampaign(null);
            }
        }

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getEntryUrl(): ?string
    {
        return $this->entryUrl;
    }

    public function setEntryUrl(?string $entryUrl): self
    {
        $this->entryUrl = $entryUrl;

        return $this;
    }

    public function getShareImg(): ?string
    {
        return $this->shareImg;
    }

    public function setShareImg(?string $shareImg): self
    {
        $this->shareImg = $shareImg;

        return $this;
    }

    public function getShareTitle(): ?string
    {
        return $this->shareTitle;
    }

    public function setShareTitle(?string $shareTitle): self
    {
        $this->shareTitle = $shareTitle;

        return $this;
    }

    /**
     * @return Collection<int, Chance>
     */
    public function getChances(): Collection
    {
        return $this->chances;
    }

    public function addChance(Chance $chance): self
    {
        if (!$this->chances->contains($chance)) {
            $this->chances->add($chance);
            $chance->setCampaign($this);
        }

        return $this;
    }

    public function removeChance(Chance $chance): self
    {
        if ($this->chances->removeElement($chance)) {
            // set the owning side to null (unless already changed)
            if ($chance->getCampaign() === $this) {
                $chance->setCampaign(null);
            }
        }

        return $this;
    }

    public function getRequestExpression(): ?string
    {
        return $this->requestExpression;
    }

    public function setRequestExpression(?string $requestExpression): self
    {
        $this->requestExpression = $requestExpression;

        return $this;
    }

    public function isRecommend(): ?bool
    {
        return $this->recommend;
    }

    public function setRecommend(?bool $recommend): self
    {
        $this->recommend = $recommend;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return int 开始倒计时
     */
    #[Groups(['restful_read'])]
    public function getStartCountdown(): int
    {
        if (CampaignStatus::PENDING === $this->getStatus()) {
            return abs(CarbonImmutable::now()->getTimestamp() - $this->getStartTime()->getTimestamp());
        }

        return 0;
    }

    /**
     * @return int 结束倒计时
     */
    #[Groups(['restful_read'])]
    public function getCloseCountdown(): int
    {
        if (CampaignStatus::RUNNING === $this->getStatus()) {
            return abs($this->getEndTime()->getTimestamp() - CarbonImmutable::now()->getTimestamp());
        }

        return 0;
    }

    #[Groups(['restful_read'])]
    public function getStatus(): CampaignStatus
    {
        $now = CarbonImmutable::now();
        if ($now->greaterThan($this->getEndTime())) {
            return CampaignStatus::CLOSED;
        }
        if ($now->lessThan($this->getStartTime())) {
            return CampaignStatus::PENDING;
        }

        return CampaignStatus::RUNNING;
    }

    #[Groups(['restful_read'])]
    public function getStatusText(): string
    {
        return $this->getStatus()->getLabel();
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            ...$this->retrieveSortableArray(),
            'code' => $this->getCode(),
            'name' => $this->getName(),
            'thumbUrl' => $this->getThumbUrl(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'subtitle' => $this->getSubtitle(),
            'tags' => $this->getTags(),
        ];
    }

    public function restfulReadArray(): array
    {
        $res = [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            ...$this->retrieveSortableArray(),
            'code' => $this->getCode(),
            'name' => $this->getName(),
            'thumbUrl' => $this->getThumbUrl(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'subtitle' => $this->getSubtitle(),
            'tags' => $this->getTags(),
            'startCountdown' => $this->getStartCountdown(),
            'closeCountdown' => $this->getCloseCountdown(),
            'status' => $this->getStatus(),
            'statusText' => $this->getStatusText(),
        ];

        $awards = [];
        foreach ($this->getAwards() as $award) {
            $awards[] = $award->restfulReadArray();
        }
        $res['awards'] = $awards;

        return $res;
    }

    /**
     * @return Collection<int, Attribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addAttribute(Attribute $attribute): static
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
            $attribute->setCampaign($this);
        }

        return $this;
    }

    public function removeAttribute(Attribute $attribute): static
    {
        if ($this->attributes->removeElement($attribute)) {
            // set the owning side to null (unless already changed)
            if ($attribute->getCampaign() === $this) {
                $attribute->setCampaign(null);
            }
        }

        return $this;
    }
}
