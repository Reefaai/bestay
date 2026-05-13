<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Return paginated bookings for the authenticated user, sorted by created_at desc.
     */
    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->with('room')
            ->paginate(15);

        return response()->json($bookings);
    }

    /**
     * Create a new booking via BookingService.
     * Returns 201 on success, 409 on conflict, 422 if room is not active.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->createBooking(
                $request->validated(),
                $request->user()
            );

            return response()->json(['booking' => $booking->load('room')], 201);
        } catch (\RuntimeException $e) {
            $data = json_decode($e->getMessage(), true);

            return response()->json($data, 409);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Return booking with room details. Enforces ownership via BookingPolicy.
     */
    public function show(Booking $booking): JsonResponse
    {
        Gate::authorize('view', $booking);

        $booking->load('room');

        return response()->json(['booking' => $booking]);
    }

    /**
     * Cancel a booking via BookingService. Rejects if already cancelled/completed (422).
     */
    public function cancel(Booking $booking): JsonResponse
    {
        Gate::authorize('cancel', $booking);

        try {
            $updatedBooking = $this->bookingService->cancelBooking($booking);

            $this->notificationService->sendBookingCancellation($updatedBooking);

            return response()->json(['booking' => $updatedBooking->load('room')]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
