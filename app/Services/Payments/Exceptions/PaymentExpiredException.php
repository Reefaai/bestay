<?php

namespace App\Services\Payments\Exceptions;

class PaymentExpiredException extends \DomainException
{
    public function __construct(string $message = 'The payment has expired and can no longer be processed.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
