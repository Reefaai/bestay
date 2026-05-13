<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Determine whether the user can view any bookings.
     * Any authenticated user can list bookings (own bookings only enforced in controller).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the booking.
     * User can view only their own bookings, admin can view any.
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create bookings.
     * Any authenticated user can create bookings.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can cancel the booking.
     * User can cancel only their own bookings with active status (pending/confirmed),
     * or admin can cancel any booking.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $booking->user_id && $booking->is_active;
    }
}
