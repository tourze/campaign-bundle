<?php

namespace CampaignBundle\Exception;

use RuntimeException;

class SkuNotSupportedException extends RuntimeException
{
    public function __construct(string $message = '暂时不支持SKU业务', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}