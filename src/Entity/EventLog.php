<?php

declare(strict_types=1);

namespace CampaignBundle\Entity;

use CampaignBundle\Repository\EventLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 表示营销活动事件日志的实体类。
 *
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: EventLogRepository::class, readOnly: true)]
#[ORM\Table(name: 'campaign_event_log', options: ['comment' => '参与日志'])]
class EventLog implements \Stringable, AdminArrayInterface
{
    use CreateTimeAware;
    use BlameableAware;
    use SnowflakeKeyAware;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Campaign::class, inversedBy: 'eventLogs', cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Campaign $campaign = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?UserInterface $user = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '事件'])]
    private string $event;

    /**
     * @var array<string, mixed>
     */
    #[Groups(groups: ['restful_read'])]
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '参数'])]
    private array $params = [];

    /**
     * 返回事件日志的字符串表示。
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getEvent();
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
     * 获取事件名称。
     *
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * 设置事件名称。
     *
     * @param string $event 事件名称
     */
    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    /**
     * 获取事件参数。
     *
     * @return array<string, mixed>|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * 设置事件参数。
     *
     * @param array<string, mixed>|null $params 事件参数数组
     */
    public function setParams(?array $params): void
    {
        $this->params = $params ?? [];
    }

    /**
     * 获取事件参数的预览字符串。
     *
     * @return string
     */
    public function getParamsPreview(): string
    {
        $params = $this->getParams();
        if (is_array($params) && [] !== $params) {
            $preview = json_encode($params, JSON_UNESCAPED_UNICODE);
            if (false === $preview) {
                return '';
            }

            return mb_strlen($preview) > 50 ? mb_substr($preview, 0, 50) . '...' : $preview;
        }

        return null === $params ? '' : '{}';
    }

    /**
     * 返回管理数组表示。
     *
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'event' => $this->getEvent(),
            'params' => $this->getParams(),
        ];
    }
}
