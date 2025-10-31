<?php

declare(strict_types=1);

namespace CampaignBundle\Exception;

class AwardUnavailableException extends \RuntimeException
{
    public function __construct(string $message = '奖品不可用', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
