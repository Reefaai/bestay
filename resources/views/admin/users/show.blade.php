@extends('layouts.admin')

@section('title', $user->name)

@section('content')
{{-- Back --}}
<div class="mb-5">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-sm text-muted hover:text-ink transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
        Back to Users
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- User profile card --}}
    <div class="lg:col-span-1">
        <div class="bg-canvas border border-hairline rounded-md p-5">
            <div class="flex flex-col items-center text-center mb-5">
                <div class="w-16 h-16 rounded-full bg-rausch/10 flex items-center justify-center text-rausch font-bold text-2xl mb-3">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="text-base font-semibold text-ink">{{ $user->name }}</h2>
                <p class="text-sm text-muted">{{ $user->email }}</p>
                <div class="mt-2">
                    @if($user->role === 'admin')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-rausch/10 text-rausch border-rausch/20">Admin</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700">User</span>
                    @endif
                </div>
            </div>

            <dl class="space-y-3 border-t border-hairline pt-4">
                <div class="flex justify-between">
                    <dt class="text-xs text-muted">Joined</dt>
                    <dd class="text-xs font-medium text-ink">{{ $user->created_at->format('d M Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-xs text-muted">Total Bookings</dt>
                    <dd class="text-xs font-medium text-ink">{{ $user->bookings->count() }}</dd>
                </div>
                @php
                    $activeCount = $user->bookings->whereIn('status', ['pending', 'confirmed'])->count();
                    $completedCount = $user->bookings->where('status', 'completed')->count();
                @endphp
                <div class="flex justify-between">
                    <dt class="text-xs text-muted">Active</dt>
                    <dd class="text-xs font-medium text-ink">{{ $activeCount }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-xs text-muted">Completed</dt>
                    <dd class="text-xs font-medium text-ink">{{ $completedCount }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Booking history --}}
    <div class="lg:col-span-2">
        <div class="bg-canvas border border-hairline rounded-md">
            <div class="px-5 py-4 border-b border-hairline">
                <h2 class="text-base font-semibold text-ink">Booking History</h2>
            </div>

            @if($user->bookings->count() > 0)
                <div class="divide-y divide-hairline">
                    @foreach($user->bookings as $booking)
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-surface-soft transition-colors">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-sm font-medium text-ink truncate">{{ $booking->room->name ?? 'Deleted Room' }}</p>
                                    @include('components.status-badge', ['status' => $booking->status])
                                </div>
                                <p class="text-xs text-muted">
                                    {{ $booking->check_in->format('d M Y') }} — {{ $booking->check_out->format('d M Y') }}
                                    · {{ $booking->check_in->diffInDays($booking->check_out) }} night(s)
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-semibold text-ink">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                <p class="text-xs text-muted">{{ $booking->created_at->format('d M Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-sm text-muted">This user has no bookings yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
