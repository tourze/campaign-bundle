<?php

declare(strict_types=1);

namespace CampaignBundle\Entity;

use CampaignBundle\Repository\AttributeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * 表示营销活动属性的实体类。
 *
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AttributeRepository::class)]
#[ORM\Table(name: 'campaign_attribute', options: ['comment' => '活动属性表'])]
class Attribute implements \Stringable, ApiArrayInterface
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '属性名称'])]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '属性内容'])]
    private ?string $value = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'attributes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Campaign $campaign = null;

    /**
     * 返回属性的字符串表示。
     *
     * @return string
     */
    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        return "{$this->name}:{$this->value}";
    }

    /**
     * 获取属性的唯一标识符。
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * 获取属性的备注。
     *
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * 设置属性的备注。
     *
     * @param string|null $remark 备注信息
     */
    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * 获取属性的值。
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * 设置属性的值。
     *
     * @param string $value 属性值
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * 获取属性的名称。
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * 设置属性的名称。
     *
     * @param string $name 属性名称
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
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
}
