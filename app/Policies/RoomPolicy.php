<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    /**
     * Determine whether the user can view any rooms.
     * Allow both authenticated and unauthenticated users (public listing).
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the room.
     * Allow both authenticated and unauthenticated users (public detail).
     */
    public function view(?User $user, Room $room): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create rooms.
     * Only admin users can create rooms.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the room.
     * Only admin users can update rooms.
     */
    public function update(User $user, Room $room): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the room.
     * Only admin users can delete rooms.
     */
    public function delete(User $user, Room $room): bool
    {
        return $user->isAdmin();
    }
}
