<?php

namespace CampaignBundle\Exception;

use RuntimeException;

class OrderNotSupportedException extends RuntimeException
{
    public function __construct(string $message = '暂时不支持订单业务', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}