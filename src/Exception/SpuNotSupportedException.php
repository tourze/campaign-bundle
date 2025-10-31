<?php

declare(strict_types=1);

namespace CampaignBundle\Exception;

class SpuNotSupportedException extends \RuntimeException
{
    public function __construct(string $message = '暂时不支持SPU业务', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
