<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentStatusLog;
use App\Models\User;
use App\Services\Payments\Exceptions\ActivePaymentExistsException;
use App\Services\Payments\Exceptions\InvalidPaymentTransitionException;
use App\Services\Payments\Exceptions\PaymentExpiredException;
use App\Services\Payments\Exceptions\PaymentTerminalStatusException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Number of minutes before a pending payment expires.
     */
    public const EXPIRY_MINUTES = 60;

    /**
     * Maximum number of payment attempts allowed per booking.
     */
    public const MAX_ATTEMPTS = 5;

    /**
     * Allowed state transitions as [from, to] pairs.
     */
    public const ALLOWED_TRANSITIONS = [
        [Payment::STATUS_PENDING, Payment::STATUS_PAID],
        [Payment::STATUS_PENDING, Payment::STATUS_FAILED],
        [Payment::STATUS_PENDING, Payment::STATUS_EXPIRED],
        [Payment::STATUS_PAID, Payment::STATUS_REFUNDED],
    ];

    public function __construct(
        protected NotificationService $notificationService,
    ) {}

    /**
     * Check if a transition from one status to another is allowed.
     *
     * Pure static method — no side effects.
     */
    public static function canTransition(string $from, string $to): bool
    {
        foreach (self::ALLOWED_TRANSITIONS as [$allowedFrom, $allowedTo]) {
            if ($allowedFrom === $from && $allowedTo === $to) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a unique payment reference in PAY-YYYYMMDD-XXXXXX format.
     *
     * @param Carbon|null $now Optional timestamp for the date portion (defaults to now)
     */
    public static function generateReference(?Carbon $now = null): string
    {
        $now = $now ?? Carbon::now();
        $datePart = $now->format('Ymd');

        // Generate 6 random uppercase alphanumeric characters
        $randomPart = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < 6; $i++) {
            $randomPart .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return "PAY-{$datePart}-{$randomPart}";
    }

    /**
     * Transition a payment to a new status within a database transaction.
     *
     * Acquires a row-level lock, validates the transition, updates the payment,
     * creates an audit log entry, and optionally executes additional logic.
     *
     * @param Payment $payment The payment to transition
     * @param string $toStatus The target status
     * @param string $actorType The type of actor (guest|admin|system)
     * @param User|null $actor The user performing the action
     * @param string|null $reason Optional reason for the transition
     * @param callable|null $additionalLogic Optional closure to execute within the transaction
     * @return Payment The refreshed payment after transition
     */
    private function transition(
        Payment $payment,
        string $toStatus,
        string $actorType,
        ?User $actor = null,
        ?string $reason = null,
        ?callable $additionalLogic = null,
    ): Payment {
        return DB::transaction(function () use ($payment, $toStatus, $actorType, $actor, $reason, $additionalLogic) {
            // Acquire row-level lock
            $payment = Payment::where('id', $payment->id)->lockForUpdate()->firstOrFail();

            $fromStatus = $payment->status;

            // Validate transition
            if ($payment->isTerminal()) {
                throw new PaymentTerminalStatusException($fromStatus, $toStatus);
            }

            if (!self::canTransition($fromStatus, $toStatus)) {
                throw new InvalidPaymentTransitionException($fromStatus, $toStatus);
            }

            // Update payment status
            $payment->update(['status' => $toStatus]);

            // Create audit log
            PaymentStatusLog::create([
                'payment_id' => $payment->id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'actor_user_id' => $actor?->id,
                'actor_type' => $actorType,
                'reason' => $reason,
            ]);

            // Execute additional logic within the transaction
            if ($additionalLogic !== null) {
                $additionalLogic($payment);
            }

            return $payment->fresh();
        });
    }

    /**
     * Create a new payment for a booking.
     *
     * Checks that no active payment exists for the booking and that the
     * maximum number of attempts has not been exceeded.
     *
     * @throws ActivePaymentExistsException If an active payment already exists
     * @throws \RuntimeException If max attempts exceeded
     */
    public function createForBooking(Booking $booking): Payment
    {
        return DB::transaction(function () use ($booking) {
            // Lock existing payments for this booking to prevent race conditions
            $activePayment = Payment::where('booking_id', $booking->id)
                ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_PAID])
                ->lockForUpdate()
                ->first();

            if ($activePayment !== null) {
                throw new ActivePaymentExistsException();
            }

            // Check attempt count
            $attemptCount = Payment::where('booking_id', $booking->id)->count();
            if ($attemptCount >= self::MAX_ATTEMPTS) {
                throw new \RuntimeException(
                    "Maximum payment attempts (" . self::MAX_ATTEMPTS . ") reached for this booking."
                );
            }

            $now = Carbon::now();
            $reference = self::generateReference($now);

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'reference' => $reference,
                'amount' => $booking->total_price,
                'status' => Payment::STATUS_PENDING,
                'expires_at' => $now->copy()->addMinutes(self::EXPIRY_MINUTES),
            ]);

            // Create audit log for creation (from_status is null)
            PaymentStatusLog::create([
                'payment_id' => $payment->id,
                'from_status' => null,
                'to_status' => Payment::STATUS_PENDING,
                'actor_user_id' => $booking->user_id,
                'actor_type' => 'guest',
                'reason' => null,
            ]);

            return $payment;
        });
    }

    /**
     * Select a payment method for a pending payment.
     *
     * @throws InvalidPaymentTransitionException If payment is not pending
     * @throws \InvalidArgumentException If method is invalid
     */
    public function selectMethod(Payment $payment, string $method): Payment
    {
        if ($payment->status !== Payment::STATUS_PENDING) {
            throw new InvalidPaymentTransitionException(
                $payment->status,
                $payment->status,
                "Cannot change payment method when payment status is '{$payment->status}'."
            );
        }

        if (!in_array($method, Payment::METHODS)) {
            throw new \InvalidArgumentException(
                "Invalid payment method '{$method}'. Allowed methods: " . implode(', ', Payment::METHODS)
            );
        }

        $payment->update(['method' => $method]);

        return $payment->fresh();
    }

    /**
     * Process the outcome of a payment (success or fail).
     *
     * On success: transitions to paid, sets paid_at, confirms booking.
     * On fail: transitions to failed, stores failure reason.
     *
     * @param Payment $payment The payment to process
     * @param string $outcome Either 'success' or 'fail'
     * @param string|null $failureReason Required when outcome is 'fail' (1-500 chars)
     * @throws PaymentExpiredException If payment has expired
     * @throws InvalidPaymentTransitionException If payment is not pending
     * @throws \InvalidArgumentException If outcome or failure_reason is invalid
     */
    public function processOutcome(Payment $payment, string $outcome, ?string $failureReason = null): Payment
    {
        if (!in_array($outcome, ['success', 'fail'])) {
            throw new \InvalidArgumentException(
                "Invalid outcome '{$outcome}'. Allowed values: success, fail."
            );
        }

        if ($outcome === 'fail') {
            if ($failureReason === null || strlen($failureReason) < 1 || strlen($failureReason) > 500) {
                throw new \InvalidArgumentException(
                    'Failure reason is required and must be between 1 and 500 characters.'
                );
            }
        }

        // Check expiry before processing
        if ($payment->status === Payment::STATUS_PENDING && $payment->isExpired()) {
            // Expire the payment first
            $this->expireIfOverdue($payment, 'system');
            throw new PaymentExpiredException();
        }

        if ($outcome === 'success') {
            $result = $this->transition(
                $payment,
                Payment::STATUS_PAID,
                'guest',
                null,
                null,
                function (Payment $payment) {
                    $payment->update(['paid_at' => Carbon::now()]);

                    // Confirm the booking directly within the same transaction
                    $booking = Booking::where('id', $payment->booking_id)->lockForUpdate()->first();
                    if ($booking && $booking->status === 'pending') {
                        $booking->update(['status' => 'confirmed']);
                    }
                }
            );

            // Notify after commit
            try {
                $this->notificationService->sendPaymentSucceeded($result);
            } catch (\Throwable $e) {
                // Notification failure must not reverse the payment transition
            }

            return $result;
        }

        // outcome === 'fail'
        $result = $this->transition(
            $payment,
            Payment::STATUS_FAILED,
            'guest',
            null,
            $failureReason,
            function (Payment $payment) use ($failureReason) {
                $payment->update(['failure_reason' => $failureReason]);
            }
        );

        // Notify after commit
        try {
            $this->notificationService->sendPaymentFailed($result);
        } catch (\Throwable $e) {
            // Notification failure must not reverse the payment transition
        }

        return $result;
    }

    /**
     * Expire a payment if it is overdue.
     *
     * Only expires if the payment is pending and expires_at < now.
     * Cancels the associated booking if it is still pending.
     *
     * @param Payment $payment The payment to check/expire
     * @param string $actorType The type of actor (system|guest|admin)
     * @param User|null $actor The user performing the action
     * @return Payment The payment (unchanged if not overdue, or refreshed if expired)
     */
    public function expireIfOverdue(Payment $payment, string $actorType = 'system', ?User $actor = null): Payment
    {
        // Only expire pending payments that are past their expiry
        if ($payment->status !== Payment::STATUS_PENDING) {
            return $payment;
        }

        if (!$payment->isExpired()) {
            return $payment;
        }

        $result = $this->transition(
            $payment,
            Payment::STATUS_EXPIRED,
            $actorType,
            $actor,
            null,
            function (Payment $payment) {
                // Cancel booking only if it is currently pending
                $booking = Booking::where('id', $payment->booking_id)->lockForUpdate()->first();
                if ($booking && $booking->status === 'pending') {
                    $booking->update(['status' => 'cancelled']);
                }
            }
        );

        // Notify after commit
        try {
            $this->notificationService->sendPaymentExpired($result);
        } catch (\Throwable $e) {
            // Notification failure must not reverse the payment transition
        }

        return $result;
    }

    /**
     * Refund or expire a payment when its booking is cancelled.
     *
     * Idempotent:
     * - If payment is paid → transition to refunded (set refunded_at)
     * - If payment is pending → transition to expired
     * - If payment is terminal → return null (no-op)
     *
     * @param Booking $booking The booking being cancelled
     * @param User|null $actor The user performing the cancellation
     * @return Payment|null The updated payment, or null if no action taken
     */
    public function refundOnBookingCancellation(Booking $booking, ?User $actor = null): ?Payment
    {
        $payment = Payment::where('booking_id', $booking->id)
            ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_PAID])
            ->first();

        if ($payment === null) {
            return null;
        }

        $actorType = $actor !== null ? ($actor->isAdmin() ? 'admin' : 'guest') : 'system';

        if ($payment->status === Payment::STATUS_PAID) {
            $result = $this->transition(
                $payment,
                Payment::STATUS_REFUNDED,
                $actorType,
                $actor,
                null,
                function (Payment $payment) {
                    $payment->update(['refunded_at' => Carbon::now()]);
                }
            );

            // Notify after commit
            try {
                $this->notificationService->sendPaymentRefunded($result);
            } catch (\Throwable $e) {
                // Notification failure must not reverse the payment transition
            }

            return $result;
        }

        if ($payment->status === Payment::STATUS_PENDING) {
            $result = $this->transition(
                $payment,
                Payment::STATUS_EXPIRED,
                $actorType,
                $actor,
                'Booking cancelled',
            );

            // Notify after commit
            try {
                $this->notificationService->sendPaymentExpired($result);
            } catch (\Throwable $e) {
                // Notification failure must not reverse the payment transition
            }

            return $result;
        }

        // Terminal status — no-op
        return null;
    }

    /**
     * Admin override of payment status.
     *
     * Validates preconditions:
     * - Target 'paid': current must be pending
     * - Target 'failed': current must be pending
     * - Target 'refunded': current must be paid AND booking must be cancelled
     *
     * @param Payment $payment The payment to override
     * @param string $targetStatus The target status (paid|failed|refunded)
     * @param User $admin The admin performing the override
     * @param string|null $reason Optional reason for the override
     * @throws InvalidPaymentTransitionException If preconditions are not met
     * @throws \InvalidArgumentException If target status is invalid
     */
    public function adminOverride(Payment $payment, string $targetStatus, User $admin, ?string $reason = null): Payment
    {
        $allowedTargets = [Payment::STATUS_PAID, Payment::STATUS_FAILED, Payment::STATUS_REFUNDED];

        if (!in_array($targetStatus, $allowedTargets)) {
            throw new \InvalidArgumentException(
                "Invalid target status '{$targetStatus}'. Allowed values: " . implode(', ', $allowedTargets)
            );
        }

        // Validate preconditions based on target
        if ($targetStatus === Payment::STATUS_PAID) {
            if ($payment->status !== Payment::STATUS_PENDING) {
                throw new InvalidPaymentTransitionException(
                    $payment->status,
                    $targetStatus,
                    "Admin override to 'paid' requires current status to be 'pending'."
                );
            }
        }

        if ($targetStatus === Payment::STATUS_FAILED) {
            if ($payment->status !== Payment::STATUS_PENDING) {
                throw new InvalidPaymentTransitionException(
                    $payment->status,
                    $targetStatus,
                    "Admin override to 'failed' requires current status to be 'pending'."
                );
            }
        }

        if ($targetStatus === Payment::STATUS_REFUNDED) {
            if ($payment->status !== Payment::STATUS_PAID) {
                throw new InvalidPaymentTransitionException(
                    $payment->status,
                    $targetStatus,
                    "Admin override to 'refunded' requires current status to be 'paid'."
                );
            }

            $booking = $payment->booking;
            if ($booking->status !== 'cancelled') {
                throw new InvalidPaymentTransitionException(
                    $payment->status,
                    $targetStatus,
                    "Admin override to 'refunded' requires the associated booking to be cancelled."
                );
            }
        }

        $additionalLogic = null;

        if ($targetStatus === Payment::STATUS_PAID) {
            $additionalLogic = function (Payment $payment) use ($admin) {
                $payment->update([
                    'paid_at' => Carbon::now(),
                    'verified_by' => $admin->id,
                    'verified_at' => Carbon::now(),
                ]);

                // Confirm the booking directly
                $booking = Booking::where('id', $payment->booking_id)->lockForUpdate()->first();
                if ($booking && $booking->status === 'pending') {
                    $booking->update(['status' => 'confirmed']);
                }
            };
        } elseif ($targetStatus === Payment::STATUS_FAILED) {
            $additionalLogic = function (Payment $payment) use ($admin, $reason) {
                $payment->update([
                    'failure_reason' => $reason,
                    'verified_by' => $admin->id,
                    'verified_at' => Carbon::now(),
                ]);
            };
        } elseif ($targetStatus === Payment::STATUS_REFUNDED) {
            $additionalLogic = function (Payment $payment) use ($admin) {
                $payment->update([
                    'refunded_at' => Carbon::now(),
                    'verified_by' => $admin->id,
                    'verified_at' => Carbon::now(),
                ]);
            };
        }

        $result = $this->transition(
            $payment,
            $targetStatus,
            'admin',
            $admin,
            $reason,
            $additionalLogic,
        );

        // Notify after commit based on target status
        try {
            if ($targetStatus === Payment::STATUS_PAID) {
                $this->notificationService->sendPaymentSucceeded($result);
            } elseif ($targetStatus === Payment::STATUS_FAILED) {
                $this->notificationService->sendPaymentFailed($result);
            } elseif ($targetStatus === Payment::STATUS_REFUNDED) {
                $this->notificationService->sendPaymentRefunded($result);
            }
        } catch (\Throwable $e) {
            // Notification failure must not reverse the payment transition
        }

        return $result;
    }
}
