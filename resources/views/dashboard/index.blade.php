@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-ink">My Bookings</h1>
        <p class="text-muted mt-1">View and manage your reservations</p>
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

    {{-- Bookings List --}}
    @if($bookings->count() > 0)
        <div class="space-y-4">
            @foreach($bookings as $booking)
                <div class="bg-canvas border border-hairline rounded-md hover:shadow-sm hover:border-muted-soft transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6">
                        {{-- Clickable Booking Info --}}
                        <a href="{{ route('bookings.show', $booking) }}" class="flex-1 min-w-0 group">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-lg font-semibold text-ink truncate group-hover:text-rausch transition-colors">
                                    {{ $booking->room->name ?? 'Room Unavailable' }}
                                </h3>
                                @include('components.status-badge', ['status' => $booking->status])
                            </div>

                            <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-sm text-muted">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-muted-soft flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                    {{ $booking->check_in->format('d M') }} — {{ $booking->check_out->format('d M Y') }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-muted-soft flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                    </svg>
                                    {{ $booking->check_in->diffInDays($booking->check_out) }} {{ Str::plural('night', $booking->check_in->diffInDays($booking->check_out)) }}
                                </span>
                                <span class="flex items-center gap-1.5 font-medium text-ink">
                                    <svg class="w-4 h-4 text-muted-soft flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                </span>
                            </div>
                        </a>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            {{-- Pay Button (for pending bookings) --}}
                            @if($booking->status === 'pending')
                                <a
                                    href="{{ route('bookings.payment', $booking) }}"
                                    class="px-5 py-2 text-sm font-medium text-on-primary bg-rausch rounded-sm hover:bg-rausch-active transition-colors"
                                >
                                    Pay Now
                                </a>
                            @endif

                            {{-- Cancel Button (only for pending/confirmed bookings) --}}
                            @if(in_array($booking->status, ['pending', 'confirmed']))
                            <div x-data="{ showConfirm: false }" class="flex-shrink-0">
                                <button
                                    @click="showConfirm = true"
                                    class="px-6 py-2 text-sm font-medium text-error border border-error rounded-sm hover:bg-red-50 transition-colors"
                                >
                                    Cancel
                                </button>

                                {{-- Confirmation Dialog --}}
                                <div
                                    x-show="showConfirm"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                    @keydown.escape.window="showConfirm = false"
                                >
                                    {{-- Backdrop --}}
                                    <div class="absolute inset-0 bg-ink/50" @click="showConfirm = false"></div>

                                    {{-- Modal --}}
                                    <div
                                        x-show="showConfirm"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="relative bg-canvas rounded-md p-8 shadow-lg max-w-sm w-full"
                                    >
                                        <h3 class="text-lg font-semibold text-ink mb-2">Cancel Booking</h3>
                                        <p class="text-sm text-muted mb-6">
                                            Are you sure you want to cancel your booking at <strong>{{ $booking->room->name ?? 'this room' }}</strong>? This action cannot be undone.
                                        </p>

                                        <div class="flex gap-2 justify-end">
                                            <button
                                                @click="showConfirm = false"
                                                class="px-6 py-2 text-sm font-medium text-muted border border-hairline rounded-sm hover:bg-surface-soft transition-colors"
                                            >
                                                Keep Booking
                                            </button>

                                            <form method="POST" action="{{ url('/bookings/' . $booking->id . '/cancel') }}" x-data="{ submitting: false }" @submit="submitting = true">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    :disabled="submitting"
                                                    class="px-6 py-2 text-sm font-medium text-on-primary bg-error rounded-sm hover:bg-error-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                >
                                                    <span x-show="!submitting">Yes, Cancel</span>
                                                    <span x-show="submitting" class="flex items-center gap-1">
                                                        <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        Cancelling...
                                                    </span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-muted-soft mb-6" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
            <h3 class="text-lg font-semibold text-ink mb-1">No bookings yet</h3>
            <p class="text-muted mb-6">Start exploring rooms and make your first reservation.</p>
            <a href="{{ url('/rooms') }}" class="inline-block bg-rausch text-on-primary font-medium text-sm px-8 py-2 rounded-sm hover:bg-rausch-active transition-colors">
                Browse Rooms
            </a>
        </div>
    @endif

    {{-- Pagination --}}
    <div class="mt-8">
        @include('components.pagination', ['paginator' => $bookings])
    </div>
</div>
@endsection
