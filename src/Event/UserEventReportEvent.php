<?php

declare(strict_types=1);

namespace CampaignBundle\Event;

use CampaignBundle\Entity\EventLog;
use Symfony\Contracts\EventDispatcher\Event;

final class UserEventReportEvent extends Event
{
    /** @var array<string, mixed> */
    private array $result = [];

    /** @return array<string, mixed> */
    public function getResult(): array
    {
        return $this->result;
    }

    /** @param array<string, mixed> $result */
    public function setResult(array $result): void
    {
        $this->result = $result;
    }

    private string $event;

    /** @var array<string, mixed> */
    private array $params = [];

    private EventLog $log;

    /**
     * @var bool 标签这个事件是否有进行特殊处理
     */
    private bool $hook = false;

    public function getEvent(): string
    {
        return $this->event;
    }

    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    /** @return array<string, mixed> */
    public function getParams(): array
    {
        return $this->params;
    }

    /** @param array<string, mixed> $params */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getLog(): EventLog
    {
        return $this->log;
    }

    public function setLog(EventLog $log): void
    {
        $this->log = $log;
    }

    public function isHook(): bool
    {
        return $this->hook;
    }

    public function setHook(bool $hook): void
    {
        $this->hook = $hook;
    }
}
