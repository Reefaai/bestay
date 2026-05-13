<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStatusRequest;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Return a paginated list of all bookings (15 per page).
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'user_id', 'room_id']);

        $bookings = $this->bookingService->getAllBookings($filters);
        $bookings->load(['user', 'room']);

        return response()->json($bookings);
    }

    /**
     * Return complete booking details including user and room info.
     */
    public function show(Booking $booking): JsonResponse
    {
        $booking->load(['user', 'room']);

        return response()->json(['booking' => $booking]);
    }

    /**
     * Validate transition via BookingService, update status, trigger notification.
     */
    public function updateStatus(UpdateStatusRequest $request, Booking $booking): JsonResponse
    {
        try {
            $oldStatus = $booking->status;

            $updatedBooking = $this->bookingService->updateStatus($booking, $request->validated()['status']);

            $this->notificationService->sendStatusUpdate($updatedBooking, $oldStatus);

            return response()->json(['booking' => $updatedBooking->load(['user', 'room'])]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Query and return all active bookings with overlapping dates on the same room.
     */
    public function conflicts(Request $request): JsonResponse
    {
        // Get all active bookings grouped by room
        $activeBookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->orderBy('room_id')
            ->orderBy('check_in')
            ->get();

        $conflicts = collect();

        // Group by room_id and check for overlaps within each group
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

        // Remove duplicates and load relationships
        $uniqueConflicts = $conflicts->unique('id')->values();
        $uniqueConflicts->load(['user', 'room']);

        return response()->json(['conflicts' => $uniqueConflicts]);
    }
}
