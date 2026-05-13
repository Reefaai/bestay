@extends('layouts.app')

@section('title', 'Manage Bookings')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-ink">Manage Bookings</h1>
            <p class="text-muted mt-1">View and manage all guest reservations</p>
        </div>
        <a href="{{ url('/admin/bookings/conflicts') }}" class="inline-flex items-center gap-1 px-6 py-2 text-sm font-medium text-ink border border-hairline rounded-sm hover:bg-surface-soft transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            View Conflicts
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 rounded-sm border border-emerald-200 bg-emerald-50 px-6 py-4">
            <p class="text-sm text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-sm border border-red-200 bg-red-50 px-6 py-4">
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Status Filter --}}
    <div class="mb-6">
        <form method="GET" action="{{ url('/admin/bookings') }}" class="flex items-center gap-2">
            <label for="status-filter" class="text-sm font-medium text-muted">Filter by status:</label>
            <select
                id="status-filter"
                name="status"
                onchange="this.form.submit()"
                class="border border-hairline rounded-sm px-4 py-2 text-sm text-ink bg-canvas focus:outline-none focus:ring-2 focus:ring-rausch/20 focus:border-rausch transition-colors"
            >
                <option value="" {{ $status === null ? 'selected' : '' }}>All Statuses</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </form>
    </div>

    {{-- Bookings Table --}}
    @if($bookings->count() > 0)
        <div class="overflow-x-auto border border-hairline rounded-md">
            <table class="w-full text-sm">
                <thead class="bg-surface-soft border-b border-hairline">
                    <tr>
                        <th class="text-left px-6 py-4 font-medium text-muted">Guest</th>
                        <th class="text-left px-6 py-4 font-medium text-muted">Room</th>
                        <th class="text-left px-6 py-4 font-medium text-muted">Check-in</th>
                        <th class="text-left px-6 py-4 font-medium text-muted">Check-out</th>
                        <th class="text-left px-6 py-4 font-medium text-muted">Status</th>
                        <th class="text-right px-6 py-4 font-medium text-muted">Total</th>
                        <th class="text-right px-6 py-4 font-medium text-muted">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    @foreach($bookings as $booking)
                        <tr class="hover:bg-surface-soft transition-colors">
                            <td class="px-6 py-4 text-ink font-medium">
                                {{ $booking->user->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-body">
                                {{ $booking->room->name ?? 'Deleted Room' }}
                            </td>
                            <td class="px-6 py-4 text-body">
                                {{ $booking->check_in->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-body">
                                {{ $booking->check_out->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @include('components.status-badge', ['status' => $booking->status])
                            </td>
                            <td class="px-6 py-4 text-right text-ink font-medium">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ url('/admin/bookings/' . $booking->id) }}" class="text-sm font-medium text-rausch hover:text-rausch-active transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-muted-soft mb-6" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
            <h3 class="text-lg font-semibold text-ink mb-1">No bookings found</h3>
            <p class="text-muted">
                @if($status)
                    No bookings with status "{{ $status }}". Try a different filter.
                @else
                    There are no bookings in the system yet.
                @endif
            </p>
        </div>
    @endif

    {{-- Pagination --}}
    <div class="mt-8">
        @include('components.pagination', ['paginator' => $bookings])
    </div>
</div>
@endsection
