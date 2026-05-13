<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Display a paginated list of all bookings with optional status filter.
     */
    public function index(Request $request): View
    {
        $query = Booking::with(['user', 'room']);

        $status = $request->input('status');

        if ($request->filled('status')) {
            $query->where('status', $status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.bookings.index', compact('bookings', 'status'));
    }

    /**
     * Display full booking details with user and room information.
     */
    public function show(Booking $booking): View
    {
        $booking->load(['user', 'room']);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Update booking status via BookingService with transition validation.
     */
    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:confirmed,cancelled,completed',
        ]);

        try {
            $this->bookingService->updateStatus($booking, $request->input('status'));

            return redirect()->back()->with('success', 'Booking status updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display all bookings with overlapping dates on the same room.
     */
    public function conflicts(): View
    {
        $activeBookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->orderBy('room_id')
            ->orderBy('check_in')
            ->get();

        $conflicts = collect();

        $grouped = $activeBookings->groupBy('room_id');

        foreach ($grouped as $roomId => $roomBookings) {
            if ($roomBookings->count() < 2) {
                continue;
            }

            $bookingsArray = $roomBookings->values();

            for ($i = 0; $i < $bookingsArray->count(); $i++) {
                for ($j = $i + 1; $j < $bookingsArray->count(); $j++) {
                    $a = $bookingsArray[$i];
                    $b = $bookingsArray[$j];

                    // Overlap: A.check_in < B.check_out AND A.check_out > B.check_in
                    if ($a->check_in < $b->check_out && $a->check_out > $b->check_in) {
                        $conflicts->push($a);
                        $conflicts->push($b);
                    }
                }
            }
        }

        $conflicts = $conflicts->unique('id')->values();
        $conflicts->load(['user', 'room']);

        // Group conflicts by room for the view
        $conflicts = $conflicts->groupBy('room_id');

        return view('admin.bookings.conflicts', compact('conflicts'));
    }
}
