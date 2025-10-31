<?php

declare(strict_types=1);

namespace CampaignBundle\Exception;

class InsufficientStockException extends \RuntimeException
{
    public function __construct(string $message = '库存不足', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
