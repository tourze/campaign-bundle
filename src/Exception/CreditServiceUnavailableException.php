<?php

declare(strict_types=1);

namespace CampaignBundle\Exception;

class CreditServiceUnavailableException extends \RuntimeException
{
    public function __construct(string $message = '积分服务不可用', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
