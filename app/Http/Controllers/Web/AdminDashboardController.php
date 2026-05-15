<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with summary statistics and charts.
     */
    public function index(): View
    {
        // Summary stats
        $stats = [
            'total_bookings'     => Booking::count(),
            'pending_bookings'   => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'total_users'        => User::where('role', 'user')->count(),
            'total_rooms'        => Room::count(),
            'active_rooms'       => Room::where('is_active', true)->count(),
            'total_revenue'      => Payment::where('status', 'paid')->sum('amount'),
            'pending_payments'   => Payment::where('status', 'pending')->count(),
        ];

        // Booking conflicts count
        $stats['conflict_count'] = $this->countConflicts();

        // Monthly bookings for the last 6 months (for chart)
        $monthlyBookings = $this->getMonthlyBookings();

        // Monthly revenue for the last 6 months (for chart)
        $monthlyRevenue = $this->getMonthlyRevenue();

        // Payment status distribution
        $paymentStats = Payment::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent bookings (last 5)
        $recentBookings = Booking::with(['user', 'room'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent payments (last 5)
        $recentPayments = Payment::with(['booking.user', 'booking.room'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyBookings',
            'monthlyRevenue',
            'paymentStats',
            'recentBookings',
            'recentPayments',
        ));
    }

    /**
     * Count active bookings with overlapping dates on the same room.
     */
    private function countConflicts(): int
    {
        $activeBookings = Booking::whereIn('status', ['pending', 'confirmed'])
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
                    if ($a->check_in < $b->check_out && $a->check_out > $b->check_in) {
                        $conflictIds->push($a->id);
                        $conflictIds->push($b->id);
                    }
                }
            }
        }

        return $conflictIds->unique()->count();
    }

    /**
     * Get booking counts grouped by month for the last 6 months.
     */
    private function getMonthlyBookings(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = [
                'label' => $date->format('M Y'),
                'count' => Booking::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }
        return $months;
    }

    /**
     * Get revenue totals grouped by month for the last 6 months.
     */
    private function getMonthlyRevenue(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = [
                'label'  => $date->format('M Y'),
                'amount' => (float) Payment::where('status', 'paid')
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->sum('amount'),
            ];
        }
        return $months;
    }
}
