<?php

declare(strict_types=1);

namespace CampaignBundle\Exception;

class RewardLimitExceededException extends \RuntimeException
{
    public function __construct(string $message = '已达到领取限制', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
