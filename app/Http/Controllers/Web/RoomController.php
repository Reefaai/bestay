<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Display a paginated listing of active rooms with optional filters.
     */
    public function index(Request $request): View
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

        $rooms = $query->paginate(15)->withQueryString();

        return view('rooms.index', compact('rooms'));
    }

    /**
     * Display the specified room's details.
     */
    public function show(Room $room): View
    {
        // Amenities berdasarkan tipe kamar
        $amenitiesByType = [
            'standard' => [
                ['icon' => 'wifi', 'label' => 'WiFi Gratis'],
                ['icon' => 'tv', 'label' => 'TV LED 32"'],
                ['icon' => 'ac', 'label' => 'AC'],
                ['icon' => 'bath', 'label' => 'Kamar Mandi Dalam'],
                ['icon' => 'water', 'label' => 'Air Panas'],
                ['icon' => 'coffee', 'label' => 'Coffee/Tea Maker'],
            ],
            'deluxe' => [
                ['icon' => 'wifi', 'label' => 'WiFi Gratis Super Cepat'],
                ['icon' => 'tv', 'label' => 'TV LED 43"'],
                ['icon' => 'ac', 'label' => 'AC'],
                ['icon' => 'bath', 'label' => 'Bathtub & Rain Shower'],
                ['icon' => 'breakfast', 'label' => 'Sarapan Termasuk'],
                ['icon' => 'minibar', 'label' => 'Minibar'],
                ['icon' => 'safe', 'label' => 'Brankas'],
                ['icon' => 'desk', 'label' => 'Meja Kerja'],
            ],
            'suite' => [
                ['icon' => 'wifi', 'label' => 'WiFi Premium'],
                ['icon' => 'tv', 'label' => 'TV LED 55" + Netflix'],
                ['icon' => 'ac', 'label' => 'AC Dual Zone'],
                ['icon' => 'bath', 'label' => 'Jacuzzi Pribadi'],
                ['icon' => 'breakfast', 'label' => 'Sarapan Mewah'],
                ['icon' => 'minibar', 'label' => 'Minibar Premium'],
                ['icon' => 'safe', 'label' => 'Brankas'],
                ['icon' => 'living', 'label' => 'Ruang Tamu Terpisah'],
                ['icon' => 'butler', 'label' => 'Butler Service 24/7'],
                ['icon' => 'view', 'label' => 'Pemandangan Panorama'],
            ],
            'family' => [
                ['icon' => 'wifi', 'label' => 'WiFi Gratis'],
                ['icon' => 'tv', 'label' => 'TV LED 43" + Channel Anak'],
                ['icon' => 'ac', 'label' => 'AC'],
                ['icon' => 'bath', 'label' => 'Kamar Mandi Dalam'],
                ['icon' => 'breakfast', 'label' => 'Sarapan Keluarga'],
                ['icon' => 'kids', 'label' => 'Area Bermain Anak'],
                ['icon' => 'bed', 'label' => 'Extra Bed Tersedia'],
                ['icon' => 'kitchen', 'label' => 'Kitchenette'],
            ],
        ];

        $amenities = $amenitiesByType[$room->type] ?? $amenitiesByType['standard'];

        // Kebijakan dan info lain
        $policies = [
            'check_in' => '14:00',
            'check_out' => '12:00',
            'cancellation' => 'Pembatalan gratis hingga 24 jam sebelum check-in',
            'children' => 'Ramah anak',
            'pets' => 'Tidak diperbolehkan membawa hewan peliharaan',
            'smoking' => 'Kamar non-smoking',
        ];

        // Rating dummy (sampai fitur review dibuat)
        $rating = [
            'average' => 4.8,
            'total_reviews' => 127,
            'cleanliness' => 4.9,
            'comfort' => 4.8,
            'location' => 4.7,
            'service' => 4.9,
            'value' => 4.6,
        ];

        return view('rooms.show', compact('room', 'amenities', 'policies', 'rating'));
    }
}
