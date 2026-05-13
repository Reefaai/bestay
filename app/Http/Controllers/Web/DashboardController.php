<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Display the user's dashboard with their bookings.
     */
    public function index(Request $request): View
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->with(['room', 'activePayment'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.index', compact('bookings'));
    }

    /**
     * Show booking detail with payment history.
     */
    public function show(Request $request, Booking $booking): View
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        $booking->load(['room', 'payments' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }]);

        return view('dashboard.show', compact('booking'));
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(Request $request, Booking $booking): RedirectResponse
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        try {
            $this->bookingService->cancelBooking($booking);

            return redirect()->back()->with('success', 'Booking cancelled successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
