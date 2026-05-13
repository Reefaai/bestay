@extends('layouts.app')

@section('title', 'Booking Detail — ' . ($booking->room->name ?? 'Booking'))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6">
        <ol class="flex items-center gap-1 text-sm text-muted">
            <li><a href="{{ route('dashboard') }}" class="hover:text-ink transition-colors">My Bookings</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-ink font-medium">{{ $booking->room->name ?? 'Booking' }}</li>
        </ol>
    </nav>

    {{-- Booking Header --}}
    <div class="bg-canvas border border-hairline rounded-md p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-ink">{{ $booking->room->name ?? 'Room Unavailable' }}</h1>
                <p class="text-sm text-muted mt-1">{{ $booking->room->type ? ucfirst($booking->room->type) . ' Room' : '' }}</p>
            </div>
            @include('components.status-badge', ['status' => $booking->status])
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div>
                <p class="text-xs text-muted font-medium uppercase tracking-wide">Check-in</p>
                <p class="text-sm text-ink mt-1 font-medium">{{ $booking->check_in->format('D, d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-muted font-medium uppercase tracking-wide">Check-out</p>
                <p class="text-sm text-ink mt-1 font-medium">{{ $booking->check_out->format('D, d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-muted font-medium uppercase tracking-wide">Duration</p>
                <p class="text-sm text-ink mt-1 font-medium">{{ $booking->check_in->diffInDays($booking->check_out) }} {{ Str::plural('night', $booking->check_in->diffInDays($booking->check_out)) }}</p>
            </div>
            <div>
                <p class="text-xs text-muted font-medium uppercase tracking-wide">Total Price</p>
                <p class="text-sm text-ink mt-1 font-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
            </div>
        </div>

        @if($booking->notes)
            <div class="mt-4 pt-4 border-t border-hairline">
                <p class="text-xs text-muted font-medium uppercase tracking-wide">Notes</p>
                <p class="text-sm text-ink mt-1">{{ $booking->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Payment History --}}
    <div class="bg-canvas border border-hairline rounded-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-ink mb-4">Payment History</h2>

        @if($booking->payments->count() > 0)
            <div class="space-y-3">
                @foreach($booking->payments as $payment)
                    <div class="flex items-center justify-between p-4 border border-hairline rounded-sm">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-mono text-ink">{{ $payment->reference }}</span>
                                @php
                                    $payBadge = match($payment->status) {
                                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'failed' => 'bg-red-100 text-red-800 border-red-200',
                                        'expired' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        'refunded' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $payBadge }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 mt-1 text-xs text-muted">
                                <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                @if($payment->method)
                                    <span>{{ str_replace('_', ' ', ucwords($payment->method, '_')) }}</span>
                                @endif
                                <span>{{ $payment->created_at->format('d M Y H:i') }}</span>
                            </div>
                            @if($payment->failure_reason)
                                <p class="text-xs text-red-500 mt-1">{{ $payment->failure_reason }}</p>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0 ml-4">
                            @if($payment->paid_at)
                                <p class="text-xs text-muted">Paid {{ $payment->paid_at->format('d M Y H:i') }}</p>
                            @elseif($payment->refunded_at)
                                <p class="text-xs text-muted">Refunded {{ $payment->refunded_at->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-muted">No payment records found.</p>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        @if($booking->status === 'pending')
            <a href="{{ route('bookings.payment', $booking) }}" class="px-6 py-2 text-sm font-medium text-on-primary bg-rausch rounded-sm hover:bg-rausch-active transition-colors">
                Pay Now
            </a>
        @endif

        @if(in_array($booking->status, ['pending', 'confirmed']))
            <form method="POST" action="{{ route('bookings.cancel', $booking) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-6 py-2 text-sm font-medium text-error border border-error rounded-sm hover:bg-surface-soft transition-colors" onclick="return confirm('Are you sure you want to cancel this booking?')">
                    Cancel Booking
                </button>
            </form>
        @endif

        <a href="{{ route('dashboard') }}" class="px-6 py-2 text-sm font-medium text-muted border border-hairline rounded-sm hover:bg-surface-soft transition-colors">
            Back to Bookings
        </a>
    </div>
</div>
@endsection
