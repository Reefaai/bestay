<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class NotificationServicePaymentTest extends TestCase
{
    use RefreshDatabase;

    private NotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NotificationService();
    }

    private function createPaymentWithBooking(string $status = 'paid', array $paymentOverrides = []): Payment
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'status' => 'pending',
        ]);

        $payment = Payment::create(array_merge([
            'booking_id' => $booking->id,
            'reference' => 'PAY-20250101-ABC123',
            'amount' => 500000.00,
            'method' => 'bank_transfer',
            'status' => $status,
            'expires_at' => now()->addHour(),
        ], $paymentOverrides));

        return $payment;
    }

    public function test_sendPaymentSucceeded_creates_notification_with_booking_id_and_reference(): void
    {
        $payment = $this->createPaymentWithBooking('paid');

        $this->service->sendPaymentSucceeded($payment);

        $this->assertDatabaseCount('notifications', 1);

        $notification = Notification::first();
        $this->assertEquals($payment->booking->user_id, $notification->user_id);
        $this->assertEquals($payment->booking->id, $notification->booking_id);
        $this->assertEquals('payment_succeeded', $notification->type);
        $this->assertStringContainsString((string) $payment->booking->id, $notification->message);
        $this->assertStringContainsString($payment->reference, $notification->message);
    }

    public function test_sendPaymentFailed_creates_notification_with_booking_id_and_status(): void
    {
        $payment = $this->createPaymentWithBooking('failed');

        $this->service->sendPaymentFailed($payment);

        $this->assertDatabaseCount('notifications', 1);

        $notification = Notification::first();
        $this->assertEquals($payment->booking->user_id, $notification->user_id);
        $this->assertEquals($payment->booking->id, $notification->booking_id);
        $this->assertEquals('payment_failed', $notification->type);
        $this->assertStringContainsString((string) $payment->booking->id, $notification->message);
        $this->assertStringContainsString($payment->status, $notification->message);
    }

    public function test_sendPaymentExpired_creates_notification_with_booking_id_and_expired_status(): void
    {
        $payment = $this->createPaymentWithBooking('expired');

        $this->service->sendPaymentExpired($payment);

        $this->assertDatabaseCount('notifications', 1);

        $notification = Notification::first();
        $this->assertEquals($payment->booking->user_id, $notification->user_id);
        $this->assertEquals($payment->booking->id, $notification->booking_id);
        $this->assertEquals('payment_expired', $notification->type);
        $this->assertStringContainsString((string) $payment->booking->id, $notification->message);
        $this->assertStringContainsString($payment->status, $notification->message);
    }

    public function test_sendPaymentRefunded_creates_notification_with_booking_id_and_refunded_at(): void
    {
        $refundedAt = now();
        $payment = $this->createPaymentWithBooking('refunded', [
            'refunded_at' => $refundedAt,
        ]);

        $this->service->sendPaymentRefunded($payment);

        $this->assertDatabaseCount('notifications', 1);

        $notification = Notification::first();
        $this->assertEquals($payment->booking->user_id, $notification->user_id);
        $this->assertEquals($payment->booking->id, $notification->booking_id);
        $this->assertEquals('payment_refunded', $notification->type);
        $this->assertStringContainsString((string) $payment->booking->id, $notification->message);
        $this->assertStringContainsString($refundedAt->format('d M Y H:i'), $notification->message);
    }

    public function test_sendPaymentSucceeded_does_not_throw_on_failure(): void
    {
        // Create a payment with an invalid booking_id to trigger an error
        $payment = new Payment();
        $payment->id = 999;
        $payment->reference = 'PAY-TEST';
        $payment->status = 'paid';

        // Force a relation that returns null booking to trigger error in Notification::create
        $payment->setRelation('booking', null);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'Failed to send payment succeeded notification');
            });

        // Should not throw
        $this->service->sendPaymentSucceeded($payment);
    }

    public function test_sendPaymentFailed_does_not_throw_on_failure(): void
    {
        $payment = new Payment();
        $payment->id = 999;
        $payment->reference = 'PAY-TEST';
        $payment->status = 'failed';

        $payment->setRelation('booking', null);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'Failed to send payment failed notification');
            });

        $this->service->sendPaymentFailed($payment);
    }

    public function test_sendPaymentExpired_does_not_throw_on_failure(): void
    {
        $payment = new Payment();
        $payment->id = 999;
        $payment->reference = 'PAY-TEST';
        $payment->status = 'expired';

        $payment->setRelation('booking', null);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'Failed to send payment expired notification');
            });

        $this->service->sendPaymentExpired($payment);
    }

    public function test_sendPaymentRefunded_does_not_throw_on_failure(): void
    {
        $payment = new Payment();
        $payment->id = 999;
        $payment->reference = 'PAY-TEST';
        $payment->status = 'refunded';

        $payment->setRelation('booking', null);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'Failed to send payment refunded notification');
            });

        $this->service->sendPaymentRefunded($payment);
    }
}
