<?php

declare(strict_types=1);

namespace CampaignBundle\Exception;

class InvalidQualificationTypeException extends \InvalidArgumentException
{
    public function __construct(string $message = 'Invalid qualification type', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
