<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payments\Exceptions\InvalidPaymentTransitionException;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Display a paginated list of all payments with optional filters.
     */
    public function index(Request $request): View
    {
        $query = Payment::with(['booking.user', 'booking.room', 'verifier'])
            ->orderBy('created_at', 'desc');

        $status = $request->input('status');
        $method = $request->input('method');
        $search = $request->input('search');

        if ($request->filled('status')) {
            $query->where('status', $status);
        }

        if ($request->filled('method')) {
            $query->where('method', $method);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('booking.user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments', 'status', 'method', 'search'));
    }

    /**
     * Display full payment details with status log history.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['booking.user', 'booking.room', 'statusLogs.actor', 'verifier']);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Override payment status as admin (paid / failed / refunded).
     */
    public function updateStatus(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:paid,failed,refunded',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->paymentService->adminOverride(
                $payment,
                $request->input('status'),
                $request->user(),
                $request->input('reason'),
            );

            return redirect()->back()->with('success', 'Payment status updated successfully.');
        } catch (InvalidPaymentTransitionException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
