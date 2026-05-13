<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected PaymentService $paymentService,
    ) {}
    /**
     * Check if a room is available for the given date range.
     *
     * Returns true if no active bookings overlap with the requested dates.
     */
    public function checkAvailability(int $roomId, string $checkIn, string $checkOut): bool
    {
        return $this->getConflictingBookings($roomId, $checkIn, $checkOut)->isEmpty();
    }

    /**
     * Get all active bookings that conflict with the given date range for a room.
     *
     * A conflict exists when: existing check_in < checkOut AND existing check_out > checkIn
     * Only considers bookings with status 'pending' or 'confirmed'.
     */
    public function getConflictingBookings(int $roomId, string $checkIn, string $checkOut): Collection
    {
        return Booking::where('room_id', $roomId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->get();
    }

    /**
     * Create a new booking within a database transaction.
     *
     * Validates that the room is active and no conflicting bookings exist.
     * Calculates total_price as nights × price_per_night.
     *
     * @throws \InvalidArgumentException If room is not active
     * @throws \RuntimeException If conflicting bookings exist
     */
    public function createBooking(array $data, User $user): Booking
    {
        $room = Room::findOrFail($data['room_id']);

        if (!$room->is_active) {
            throw new \InvalidArgumentException('Room is not available for booking.');
        }

        $booking = DB::transaction(function () use ($data, $user, $room) {
            $conflicts = $this->getConflictingBookings(
                $data['room_id'],
                $data['check_in'],
                $data['check_out']
            );

            if ($conflicts->isNotEmpty()) {
                throw new \RuntimeException(
                    json_encode([
                        'message' => 'Kamar tidak tersedia untuk tanggal yang dipilih',
                        'conflicts' => $conflicts->map(function ($booking) {
                            return [
                                'id' => $booking->id,
                                'check_in' => $booking->check_in->format('Y-m-d'),
                                'check_out' => $booking->check_out->format('Y-m-d'),
                                'status' => $booking->status,
                            ];
                        })->toArray(),
                    ])
                );
            }

            $nights = Carbon::parse($data['check_in'])->diffInDays(Carbon::parse($data['check_out']));
            $totalPrice = $nights * $room->price_per_night;

            $booking = Booking::create([
                'user_id' => $user->id,
                'room_id' => $data['room_id'],
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
                'total_price' => $totalPrice,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            $this->paymentService->createForBooking($booking);

            return $booking;
        });

        return $booking;
    }

    /**
     * Cancel a booking by updating its status to 'cancelled'.
     *
     * Only active bookings (pending/confirmed) can be cancelled.
     *
     * @throws \InvalidArgumentException If booking is already cancelled or completed
     */
    public function cancelBooking(Booking $booking): Booking
    {
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            throw new \InvalidArgumentException(
                "Cannot cancel booking with status '{$booking->status}'. Only pending or confirmed bookings can be cancelled."
            );
        }

        return DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'cancelled']);

            $this->paymentService->refundOnBookingCancellation($booking);

            return $booking->fresh();
        });
    }

    /**
     * Update the status of a booking with transition validation.
     *
     * Valid transitions:
     * - pending → confirmed
     * - pending → cancelled
     * - confirmed → cancelled
     * - confirmed → completed
     *
     * @throws \InvalidArgumentException If the status transition is not allowed
     */
    public function updateStatus(Booking $booking, string $status): Booking
    {
        $validTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['cancelled', 'completed'],
        ];

        $currentStatus = $booking->status;
        $allowedTransitions = $validTransitions[$currentStatus] ?? [];

        if (!in_array($status, $allowedTransitions)) {
            throw new \InvalidArgumentException(
                "Invalid status transition from '{$currentStatus}' to '{$status}'. Allowed transitions from '{$currentStatus}': " .
                (empty($allowedTransitions) ? 'none (terminal status)' : implode(', ', $allowedTransitions)) . '.'
            );
        }

        $booking->update(['status' => $status]);

        return $booking->fresh();
    }

    /**
     * Get all bookings with optional filters and pagination.
     *
     * Supported filters: status, user_id, room_id.
     * Returns 15 results per page.
     */
    public function getAllBookings(array $filters = []): LengthAwarePaginator
    {
        $query = Booking::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }
}
