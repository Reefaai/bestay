<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a booking confirmation notification to the booking owner.
     */
    public function sendBookingConfirmation(Booking $booking): void
    {
        $booking->loadMissing('room');

        $roomName = $booking->room->name;
        $checkIn = $booking->check_in->format('d M Y');
        $checkOut = $booking->check_out->format('d M Y');

        Notification::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'type' => 'booking_confirmed',
            'title' => 'Booking Dikonfirmasi',
            'message' => "Booking Anda untuk {$roomName} ({$checkIn} - {$checkOut}) telah dikonfirmasi.",
        ]);
    }

    /**
     * Send a booking cancellation notification to the booking owner.
     */
    public function sendBookingCancellation(Booking $booking): void
    {
        $booking->loadMissing('room');

        $roomName = $booking->room->name;
        $checkIn = $booking->check_in->format('d M Y');
        $checkOut = $booking->check_out->format('d M Y');

        Notification::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'type' => 'booking_cancelled',
            'title' => 'Booking Dibatalkan',
            'message' => "Booking Anda untuk {$roomName} ({$checkIn} - {$checkOut}) telah dibatalkan.",
        ]);
    }

    /**
     * Send a status update notification to the booking owner.
     */
    public function sendStatusUpdate(Booking $booking, string $oldStatus): void
    {
        Notification::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'type' => 'status_updated',
            'title' => 'Status Booking Diperbarui',
            'message' => "Status booking Anda telah diubah dari '{$oldStatus}' menjadi '{$booking->status}'.",
        ]);
    }

    /**
     * Send a notification when a payment succeeds.
     *
     * Contains the booking identifier and payment reference.
     * Failures are logged but never re-thrown (Requirement 10.4).
     */
    public function sendPaymentSucceeded(Payment $payment): void
    {
        try {
            $payment->loadMissing('booking');
            $booking = $payment->booking;

            Notification::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'type' => 'payment_succeeded',
                'title' => 'Pembayaran Berhasil',
                'message' => "Pembayaran untuk booking #{$booking->id} berhasil dengan referensi {$payment->reference}.",
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send payment succeeded notification', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send a notification when a payment fails.
     *
     * Contains the booking identifier and new status.
     * Failures are logged but never re-thrown (Requirement 10.4).
     */
    public function sendPaymentFailed(Payment $payment): void
    {
        try {
            $payment->loadMissing('booking');
            $booking = $payment->booking;

            Notification::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'type' => 'payment_failed',
                'title' => 'Pembayaran Gagal',
                'message' => "Pembayaran untuk booking #{$booking->id} gagal. Status: {$payment->status}.",
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send payment failed notification', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send a notification when a payment expires.
     *
     * Contains the booking identifier and expired status.
     * Failures are logged but never re-thrown (Requirement 10.4).
     */
    public function sendPaymentExpired(Payment $payment): void
    {
        try {
            $payment->loadMissing('booking');
            $booking = $payment->booking;

            Notification::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'type' => 'payment_expired',
                'title' => 'Pembayaran Kedaluwarsa',
                'message' => "Pembayaran untuk booking #{$booking->id} telah kedaluwarsa. Status: {$payment->status}.",
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send payment expired notification', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send a notification when a payment is refunded.
     *
     * Contains the booking identifier and refunded_at timestamp.
     * Failures are logged but never re-thrown (Requirement 10.4).
     */
    public function sendPaymentRefunded(Payment $payment): void
    {
        try {
            $payment->loadMissing('booking');
            $booking = $payment->booking;

            $refundedAt = $payment->refunded_at
                ? $payment->refunded_at->format('d M Y H:i')
                : now()->format('d M Y H:i');

            Notification::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'type' => 'payment_refunded',
                'title' => 'Pembayaran Direfund',
                'message' => "Pembayaran untuk booking #{$booking->id} telah direfund pada {$refundedAt}.",
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send payment refunded notification', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
