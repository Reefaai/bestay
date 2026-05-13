<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Store a new booking.
     *
     * Validates dates, calls BookingService to create the booking,
     * and redirects to dashboard with a success message.
     * On conflict (room not available), redirects back with an error.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in' => ['required', 'date', 'after:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        try {
            $this->bookingService->createBooking($validated, $request->user());

            return redirect('/dashboard')->with('success', 'Booking created successfully!');
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Room is not available for the selected dates.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
