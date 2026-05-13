@extends('layouts.app')

@section('title', 'Booking #' . $booking->id)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    {{-- Back Link --}}
    <div class="mb-6">
        <a href="{{ url('/admin/bookings') }}" class="inline-flex items-center gap-1 text-sm text-muted hover:text-ink transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Back to Bookings
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

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-ink">Booking #{{ $booking->id }}</h1>
            <div class="flex items-center gap-2 mt-1">
                @include('components.status-badge', ['status' => $booking->status])
                <span class="text-sm text-muted">Created {{ $booking->created_at->format('M d, Y \a\t g:i A') }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Booking Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Guest Information --}}
            <div class="border border-hairline rounded-md p-6">
                <h2 class="text-lg font-semibold text-ink mb-4">Guest Information</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-muted">Name</dt>
                        <dd class="text-sm font-medium text-ink mt-0.5">{{ $booking->user->name ?? 'Unknown' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted">Email</dt>
                        <dd class="text-sm font-medium text-ink mt-0.5">{{ $booking->user->email ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Room Information --}}
            <div class="border border-hairline rounded-md p-6">
                <h2 class="text-lg font-semibold text-ink mb-4">Room Information</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-muted">Room Name</dt>
                        <dd class="text-sm font-medium text-ink mt-0.5">{{ $booking->room->name ?? 'Deleted Room' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted">Room Type</dt>
                        <dd class="text-sm font-medium text-ink mt-0.5">{{ ucfirst($booking->room->type ?? 'N/A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted">Price per Night</dt>
                        <dd class="text-sm font-medium text-ink mt-0.5">Rp {{ number_format($booking->room->price_per_night ?? 0, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted">Capacity</dt>
                        <dd class="text-sm font-medium text-ink mt-0.5">{{ $booking->room->capacity ?? 'N/A' }} {{ Str::plural('guest', $booking->room->capacity ?? 0) }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Stay Details --}}
            <div class="border border-hairline rounded-md p-6">
                <h2 class="text-lg font-semibold text-ink mb-4">Stay Details</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-muted">Check-in</dt>
                        <dd class="text-sm font-medium text-ink mt-0.5">{{ $booking->check_in->format('l, M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted">Check-out</dt>
                        <dd class="text-sm font-medium text-ink mt-0.5">{{ $booking->check_out->format('l, M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted">Duration</dt>
                        <dd class="text-sm font-medium text-ink mt-0.5">{{ $booking->check_in->diffInDays($booking->check_out) }} {{ Str::plural('night', $booking->check_in->diffInDays($booking->check_out)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted">Total Price</dt>
                        <dd class="text-sm font-semibold text-ink mt-0.5">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</dd>
                    </div>
                </dl>

                @if($booking->notes)
                    <div class="mt-4 pt-4 border-t border-hairline-soft">
                        <dt class="text-sm text-muted">Notes</dt>
                        <dd class="text-sm text-body mt-0.5">{{ $booking->notes }}</dd>
                    </div>
                @endif
            </div>
        </div>

        {{-- Status Actions Sidebar --}}
        <div class="lg:col-span-1">
            <div class="border border-hairline rounded-md p-6 sticky top-8">
                <h2 class="text-lg font-semibold text-ink mb-4">Update Status</h2>

                <div class="space-y-2">
                    @php
                        $validTransitions = [
                            'pending' => ['confirmed', 'cancelled'],
                            'confirmed' => ['cancelled', 'completed'],
                        ];
                        $allowedTransitions = $validTransitions[$booking->status] ?? [];
                    @endphp

                    @if(in_array('confirmed', $allowedTransitions))
                        <form method="POST" action="{{ url('/admin/bookings/' . $booking->id . '/status') }}" x-data="{ submitting: false }" @submit="submitting = true">
                            @csrf
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" :disabled="submitting" class="w-full flex items-center justify-center gap-1 px-6 py-2 text-sm font-medium text-on-primary bg-emerald-600 rounded-sm hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <template x-if="!submitting">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        Confirm Booking
                                    </span>
                                </template>
                                <template x-if="submitting">
                                    <span class="flex items-center gap-1">
                                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Confirming...
                                    </span>
                                </template>
                            </button>
                        </form>
                    @endif

                    @if(in_array('completed', $allowedTransitions))
                        <form method="POST" action="{{ url('/admin/bookings/' . $booking->id . '/status') }}" x-data="{ submitting: false }" @submit="submitting = true">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" :disabled="submitting" class="w-full flex items-center justify-center gap-1 px-6 py-2 text-sm font-medium text-canvas bg-ink rounded-sm hover:bg-ink/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <template x-if="!submitting">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Complete Booking
                                    </span>
                                </template>
                                <template x-if="submitting">
                                    <span class="flex items-center gap-1">
                                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Completing...
                                    </span>
                                </template>
                            </button>
                        </form>
                    @endif

                    @if(in_array('cancelled', $allowedTransitions))
                        <div x-data="{ showConfirm: false }">
                            <button
                                @click="showConfirm = true"
                                class="w-full flex items-center justify-center gap-1 px-6 py-2 text-sm font-medium text-error border border-error rounded-sm hover:bg-red-50 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel Booking
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
                                <div class="absolute inset-0 bg-ink/50" @click="showConfirm = false"></div>
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
                                        Are you sure you want to cancel this booking? This action cannot be undone.
                                    </p>
                                    <div class="flex gap-2 justify-end">
                                        <button
                                            @click="showConfirm = false"
                                            class="px-6 py-2 text-sm font-medium text-muted border border-hairline rounded-sm hover:bg-surface-soft transition-colors"
                                        >
                                            Keep Booking
                                        </button>
                                        <form method="POST" action="{{ url('/admin/bookings/' . $booking->id . '/status') }}" x-data="{ submitting: false }" @submit="submitting = true">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" :disabled="submitting" class="px-6 py-2 text-sm font-medium text-on-primary bg-error rounded-sm hover:bg-error-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
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

                    @if(empty($allowedTransitions))
                        <p class="text-sm text-muted text-center py-4">
                            No status changes available. This booking is {{ $booking->status }}.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
