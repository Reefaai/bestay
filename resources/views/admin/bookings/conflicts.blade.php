@extends('layouts.app')

@section('title', 'Booking Conflicts')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Back Link --}}
    <div class="mb-6">
        <a href="{{ url('/admin/bookings') }}" class="inline-flex items-center gap-1 text-sm text-muted hover:text-ink transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Back to Bookings
        </a>
    </div>

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-ink">Booking Conflicts</h1>
        <p class="text-muted mt-1">Bookings with overlapping dates on the same room</p>
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

    {{-- Conflicts Grouped by Room --}}
    @if($conflicts->count() > 0)
        <div class="space-y-8">
            @foreach($conflicts as $roomId => $roomBookings)
                @php
                    $room = $roomBookings->first()->room;
                @endphp
                <div class="border border-hairline rounded-md overflow-hidden">
                    {{-- Room Header --}}
                    <div class="bg-surface-soft px-6 py-4 border-b border-hairline">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <div>
                                <h2 class="text-base font-semibold text-ink">{{ $room->name ?? 'Unknown Room' }}</h2>
                                <p class="text-xs text-muted">{{ $roomBookings->count() }} conflicting {{ Str::plural('booking', $roomBookings->count()) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Conflicting Bookings Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-surface-soft/50 border-b border-hairline-soft">
                                <tr>
                                    <th class="text-left px-6 py-2 font-medium text-muted">Guest</th>
                                    <th class="text-left px-6 py-2 font-medium text-muted">Check-in</th>
                                    <th class="text-left px-6 py-2 font-medium text-muted">Check-out</th>
                                    <th class="text-left px-6 py-2 font-medium text-muted">Status</th>
                                    <th class="text-right px-6 py-2 font-medium text-muted">Total</th>
                                    <th class="text-right px-6 py-2 font-medium text-muted">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-hairline-soft">
                                @foreach($roomBookings as $booking)
                                    <tr class="hover:bg-surface-soft transition-colors">
                                        <td class="px-6 py-4 text-ink font-medium">
                                            {{ $booking->user->name ?? 'Unknown' }}
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
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-emerald-400 mb-6" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-semibold text-ink mb-1">No conflicts found</h3>
            <p class="text-muted">All bookings are scheduled without any date overlaps.</p>
        </div>
    @endif
</div>
@endsection
