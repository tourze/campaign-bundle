<?php

namespace CampaignBundle\Entity;

use CampaignBundle\Repository\EventLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: EventLogRepository::class, readOnly: true)]
#[ORM\Table(name: 'campaign_event_log', options: ['comment' => '参与日志'])]
class EventLog implements \Stringable, AdminArrayInterface
{
    use CreateTimeAware;
    use BlameableAware;
    use SnowflakeKeyAware;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Campaign::class, inversedBy: 'eventLogs')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Campaign $campaign = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?UserInterface $user = null;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '事件'])]
    private string $event;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '参数'])]
    private array $params = [];

    public function __toString(): string
    {
        return $this->getEvent();
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

    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    public function setCampaign(?Campaign $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function setParams(?array $params): self
    {
        $this->params = $params;

        return $this;
    }

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
