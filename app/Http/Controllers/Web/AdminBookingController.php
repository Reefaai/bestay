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
        $booking->load(['user', 'room', 'payments' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }]);

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
        // Eager-load relationships upfront so no lazy load needed later
        $activeBookings = Booking::with(['user', 'room'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('room_id')
            ->orderBy('check_in')
            ->get();

        $conflictIds = collect();

        $grouped = $activeBookings->groupBy('room_id');

        foreach ($grouped as $roomBookings) {
            if ($roomBookings->count() < 2) {
                continue;
            }

            $arr = $roomBookings->values();

            for ($i = 0; $i < $arr->count(); $i++) {
                for ($j = $i + 1; $j < $arr->count(); $j++) {
                    $a = $arr[$i];
                    $b = $arr[$j];

                    // Overlap: A.check_in < B.check_out AND A.check_out > B.check_in
                    if ($a->check_in < $b->check_out && $a->check_out > $b->check_in) {
                        $conflictIds->push($a->id);
                        $conflictIds->push($b->id);
                    }
                }
            }
        }

        // Filter the already-loaded collection to only conflict bookings, then group by room
        $conflicts = $activeBookings
            ->whereIn('id', $conflictIds->unique()->values()->all())
            ->groupBy('room_id');

        return view('admin.bookings.conflicts', compact('conflicts'));
    }
}
