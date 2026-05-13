<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Requests\SelectPaymentMethodRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Show the current active payment for a booking.
     *
     * Lazy-expires the payment if it is overdue before rendering.
     * If no active payment exists but there's a failed one, show it with retry option.
     */
    public function show(Booking $booking): View|RedirectResponse
    {
        Gate::authorize('view', $booking);

        $payment = $booking->activePayment;

        if ($payment === null) {
            // Check for the latest failed payment (to show retry option)
            $payment = $booking->payments()
                ->latest()
                ->first();

            if ($payment === null) {
                return redirect()->route('dashboard')
                    ->with('error', 'No payment found for this booking.');
            }

            // If the latest payment is in a terminal state that allows retry (failed),
            // show it so the user can retry
            if ($payment->status === Payment::STATUS_FAILED && $booking->status === 'pending') {
                return view('payments.show', [
                    'payment' => $payment,
                    'booking' => $booking,
                ]);
            }

            // For expired/refunded payments with no active payment
            return redirect()->route('dashboard')
                ->with('error', 'No active payment found for this booking.');
        }

        // Lazy-expire if overdue (Requirement 4.1)
        $payment = $this->paymentService->expireIfOverdue($payment, 'guest', request()->user());

        // If the payment just got expired, still show it (user sees the expired state)
        return view('payments.show', [
            'payment' => $payment,
            'booking' => $booking,
        ]);
    }

    /**
     * Select a payment method for the given payment.
     */
    public function selectMethod(SelectPaymentMethodRequest $request, Payment $payment): RedirectResponse
    {
        Gate::authorize('selectMethod', $payment);

        $this->paymentService->selectMethod($payment, $request->validated('method'));

        return redirect()->back()->with('success', 'Payment method selected successfully.');
    }

    /**
     * Render the payment confirmation form (success/fail).
     */
    public function confirmForm(Payment $payment): View
    {
        Gate::authorize('process', $payment);

        return view('payments.confirm', [
            'payment' => $payment,
        ]);
    }

    /**
     * Process the payment outcome (success or fail).
     */
    public function confirm(ProcessPaymentRequest $request, Payment $payment): RedirectResponse
    {
        Gate::authorize('process', $payment);

        try {
            $this->paymentService->processOutcome(
                $payment,
                $request->validated('outcome'),
                $request->validated('failure_reason')
            );

            $message = $request->validated('outcome') === 'success'
                ? 'Payment completed successfully!'
                : 'Payment marked as failed.';

            return redirect()->route('bookings.payment', $payment->booking_id)
                ->with('success', $message);
        } catch (\Throwable $e) {
            return redirect()->route('bookings.payment', $payment->booking_id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new payment attempt after a failed payment.
     */
    public function retry(Payment $payment): RedirectResponse
    {
        Gate::authorize('view', $payment);

        try {
            $this->paymentService->createForBooking($payment->booking);

            return redirect()->route('bookings.payment', $payment->booking_id)
                ->with('success', 'New payment attempt created.');
        } catch (\Throwable $e) {
            return redirect()->route('bookings.payment', $payment->booking_id)
                ->with('error', $e->getMessage());
        }
    }
}
