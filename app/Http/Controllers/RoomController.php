<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
    /**
     * Display a paginated list of active rooms with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Room::active();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_night', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_night', '<=', $request->input('max_price'));
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', $request->input('capacity'));
        }

        $rooms = $query->paginate(15);

        return response()->json($rooms);
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room): JsonResponse
    {
        return response()->json(['room' => $room]);
    }

    /**
     * Store a newly created room (admin only).
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        Gate::authorize('create', Room::class);

        $room = Room::create($request->validated());

        return response()->json(['room' => $room], 201);
    }

    /**
     * Update the specified room (admin only).
     */
    public function update(UpdateRoomRequest $request, Room $room): JsonResponse
    {
        Gate::authorize('update', $room);

        $room->update($request->validated());

        return response()->json(['room' => $room->fresh()]);
    }

    /**
     * Soft-delete the specified room (set is_active=false) if no active bookings.
     */
    public function destroy(Room $room): JsonResponse
    {
        Gate::authorize('delete', $room);

        // Check if room has active bookings (pending or confirmed)
        $hasActiveBookings = $room->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBookings) {
            return response()->json([
                'message' => 'Cannot delete room with active bookings.',
            ], 409);
        }

        $room->update(['is_active' => false]);

        return response()->json(['message' => 'Room deleted successfully.']);
    }

    /**
     * Check room availability for a given date range.
     */
    public function availability(Room $room, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
        ]);

        $checkIn = $validated['check_in'];
        $checkOut = $validated['check_out'];

        // Overlap detection: any active booking where check_in < requested check_out
        // AND check_out > requested check_in
        $hasConflict = $room->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->exists();

        return response()->json([
            'available' => !$hasConflict,
            'room' => $room,
        ]);
    }
}
