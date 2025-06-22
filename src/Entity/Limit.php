<?php

namespace CampaignBundle\Entity;

use CampaignBundle\Enum\LimitType;
use CampaignBundle\Repository\LimitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: LimitRepository::class)]
#[ORM\Table(name: 'campaign_limit', options: ['comment' => '限制条件'])]
class Limit implements \Stringable, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;


    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'limits')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Award $award = null;

    #[ORM\Column(length: 30, enumType: LimitType::class, options: ['comment' => '限制类型'])]
    private LimitType $type;

    #[ORM\Column(length: 100, options: ['comment' => '条件值'])]
    private string $value = '1';

    #[ORM\Column(length: 1000, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    public function __toString(): string
    {
        if ($this->getId() === null || $this->getId() === 0) {
            return '';
        }

        return "[{$this->getType()->getLabel()}] {$this->getValue()}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): LimitType
    {
        return $this->type;
    }

    public function setType(LimitType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): static
    {
        $this->remark = $remark;

        return $this;
    }

    public function getAward(): ?Award
    {
        return $this->award;
    }

    public function setAward(?Award $award): static
    {
        $this->award = $award;

        return $this;
    }


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
