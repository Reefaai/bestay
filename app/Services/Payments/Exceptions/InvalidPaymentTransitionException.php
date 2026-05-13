<?php

namespace App\Services\Payments\Exceptions;

class InvalidPaymentTransitionException extends \DomainException
{
    public string $from;
    public string $to;

    public function __construct(string $from, string $to, string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->from = $from;
        $this->to = $to;

        if ($message === '') {
            $message = "Invalid payment transition from '{$from}' to '{$to}'.";
        }

        parent::__construct($message, $code, $previous);
    }
}
