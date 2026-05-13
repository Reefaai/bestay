<?php

namespace App\Services\Payments\Exceptions;

class InvalidPaymentAmountException extends \InvalidArgumentException
{
    public function __construct(string $message = 'The payment amount is invalid.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
