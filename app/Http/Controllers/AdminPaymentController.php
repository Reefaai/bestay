<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminPaymentIndexRequest;
use App\Http\Requests\AdminUpdatePaymentStatusRequest;
use App\Models\Payment;
use App\Services\Payments\Exceptions\InvalidPaymentTransitionException;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class AdminPaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    /**
     * List all payments with optional filters, paginated at 20 per page.
     */
    public function index(AdminPaymentIndexRequest $request): JsonResponse
    {
        $query = Payment::query()
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->method, fn ($q, $method) => $q->where('method', $method))
            ->when($request->booking_id, fn ($q, $bookingId) => $q->where('booking_id', $bookingId))
            ->orderBy('created_at', 'desc');

        $payments = $query->paginate(20);

        return response()->json($payments);
    }

    /**
     * Show a single payment (admin bypass via policy).
     */
    public function show(Payment $payment): JsonResponse
    {
        $this->authorize('view', $payment);

        $payment->load('booking');

        return response()->json(['payment' => $payment]);
    }

    /**
     * Admin override of payment status.
     */
    public function updateStatus(AdminUpdatePaymentStatusRequest $request, Payment $payment): JsonResponse
    {
        $this->authorize('adminOverride', $payment);

        try {
            $updatedPayment = $this->paymentService->adminOverride(
                $payment,
                $request->status,
                $request->user(),
                $request->reason,
            );

            return response()->json(['payment' => $updatedPayment->load('booking')]);
        } catch (InvalidPaymentTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
