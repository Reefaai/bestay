<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Requests\SelectPaymentMethodRequest;
use App\Models\Payment;
use App\Services\Payments\Exceptions\ActivePaymentExistsException;
use App\Services\Payments\Exceptions\InvalidPaymentTransitionException;
use App\Services\Payments\Exceptions\PaymentExpiredException;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    /**
     * List the authenticated user's payments, paginated 20 per page, ordered by created_at desc.
     */
    public function index(Request $request): JsonResponse
    {
        $payments = Payment::whereHas('booking', fn ($q) => $q->where('user_id', $request->user()->id))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($payments);
    }

    /**
     * Show a single payment's details. Lazy-expires if overdue before returning.
     */
    public function show(Payment $payment): JsonResponse
    {
        Gate::authorize('view', $payment);

        // Lazy expiry: if the payment is pending and overdue, expire it before rendering
        $payment = $this->paymentService->expireIfOverdue($payment);

        return response()->json(['payment' => $payment]);
    }

    /**
     * Select a payment method for a pending payment.
     */
    public function selectMethod(SelectPaymentMethodRequest $request, Payment $payment): JsonResponse
    {
        Gate::authorize('selectMethod', $payment);

        try {
            $payment = $this->paymentService->selectMethod($payment, $request->validated('method'));

            return response()->json(['payment' => $payment]);
        } catch (InvalidPaymentTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Process the payment outcome (success or fail).
     */
    public function process(ProcessPaymentRequest $request, Payment $payment): JsonResponse
    {
        Gate::authorize('process', $payment);

        try {
            $validated = $request->validated();
            $payment = $this->paymentService->processOutcome(
                $payment,
                $validated['outcome'],
                $validated['failure_reason'] ?? null
            );

            return response()->json(['payment' => $payment]);
        } catch (PaymentExpiredException $e) {
            return response()->json(['message' => $e->getMessage()], 410);
        } catch (InvalidPaymentTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Retry a failed payment by creating a new payment for the same booking.
     */
    public function retry(Payment $payment): JsonResponse
    {
        Gate::authorize('view', $payment);

        // Validate retry conditions: payment must be failed and booking must be pending
        if ($payment->status !== Payment::STATUS_FAILED) {
            return response()->json([
                'message' => 'Only failed payments can be retried.',
            ], 422);
        }

        $booking = $payment->booking;

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'The associated booking must be in pending status to retry payment.',
            ], 422);
        }

        try {
            $newPayment = $this->paymentService->createForBooking($booking);

            return response()->json(['payment' => $newPayment], 201);
        } catch (ActivePaymentExistsException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
