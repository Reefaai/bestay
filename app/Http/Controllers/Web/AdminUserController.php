<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a paginated list of all users.
     */
    public function index(Request $request): View
    {
        $query = User::withCount(['bookings'])
            ->orderBy('created_at', 'desc');

        $search = $request->input('search');
        $role   = $request->input('role');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $role);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    /**
     * Display a single user's profile with their booking history.
     */
    public function show(User $user): View
    {
        $user->load(['bookings' => function ($q) {
            $q->with(['room', 'activePayment'])->orderBy('created_at', 'desc');
        }]);

        return view('admin.users.show', compact('user'));
    }
}
