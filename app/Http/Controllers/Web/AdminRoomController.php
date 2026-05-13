<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminRoomController extends Controller
{
    /**
     * Display a listing of all rooms (active and inactive).
     */
    public function index(): View
    {
        $rooms = Room::orderBy('name')->get();

        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room.
     */
    public function create(): View
    {
        return view('admin.rooms.create');
    }

    /**
     * Store a newly created room.
     */
    public function store(StoreRoomRequest $request): RedirectResponse
    {
        Room::create($request->validated());

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(Room $room): View
    {
        return view('admin.rooms.edit', compact('room'));
    }

    /**
     * Update the specified room.
     */
    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    /**
     * Deactivate the specified room (soft-delete by setting is_active to false).
     * Checks for active bookings before deactivating.
     */
    public function destroy(Room $room): RedirectResponse
    {
        // Check if the room has active bookings (pending or confirmed)
        $hasActiveBookings = $room->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBookings) {
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Cannot deactivate room. It has active bookings (pending or confirmed).');
        }

        $room->update(['is_active' => false]);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deactivated successfully.');
    }
}
