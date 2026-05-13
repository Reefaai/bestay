<?php

namespace App\Services\Payments\Exceptions;

class ActivePaymentExistsException extends \RuntimeException
{
    public function __construct(string $message = 'An active payment already exists for this booking.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
