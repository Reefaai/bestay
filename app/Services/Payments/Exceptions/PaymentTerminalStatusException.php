<?php

namespace App\Services\Payments\Exceptions;

class PaymentTerminalStatusException extends InvalidPaymentTransitionException
{
    public function __construct(string $from, string $to, string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        if ($message === '') {
            $message = "Cannot transition payment from terminal status '{$from}' to '{$to}'.";
        }

        parent::__construct($from, $to, $message, $code, $previous);
    }
}
