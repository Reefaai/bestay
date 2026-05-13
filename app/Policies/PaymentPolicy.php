<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether the user can view any payments.
     * Any authenticated user can list payments (own payments only enforced in controller).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the payment.
     * User can view only their own payments, admin can view any.
     */
    public function view(User $user, Payment $payment): bool
    {
        return $user->isAdmin() || $user->id === $payment->booking->user_id;
    }

    /**
     * Determine whether the user can process the payment.
     * Only the booking owner can process, and only while payment is pending.
     */
    public function process(User $user, Payment $payment): bool
    {
        return $user->id === $payment->booking->user_id
            && $payment->status === Payment::STATUS_PENDING;
    }

    /**
     * Determine whether the user can select a payment method.
     * Only the booking owner can select method, and only while payment is pending.
     */
    public function selectMethod(User $user, Payment $payment): bool
    {
        return $user->id === $payment->booking->user_id
            && $payment->status === Payment::STATUS_PENDING;
    }

    /**
     * Determine whether the user can perform an admin override on the payment.
     * Only admins can override payment status.
     */
    public function adminOverride(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }
}
