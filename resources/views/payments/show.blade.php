@extends('layouts.app')

@section('title', 'Payment — ' . $payment->reference)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6">
        <ol class="flex items-center gap-1 text-sm text-muted">
            <li><a href="{{ route('dashboard') }}" class="hover:text-ink transition-colors">My Bookings</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-ink font-medium">Payment {{ $payment->reference }}</li>
        </ol>
    </nav>

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

    {{-- Payment Details Card --}}
    <div class="bg-canvas border border-hairline rounded-md p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-ink">Payment Details</h1>
            @php
                $badgeClasses = match($payment->status) {
                    'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'failed' => 'bg-red-100 text-red-800 border-red-200',
                    'expired' => 'bg-gray-100 text-gray-700 border-gray-200',
                    'refunded' => 'bg-blue-100 text-blue-800 border-blue-200',
                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                };
            @endphp
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClasses }}">
                {{ ucfirst($payment->status) }}
            </span>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-muted font-medium">Reference</dt>
                <dd class="text-ink mt-0.5">{{ $payment->reference }}</dd>
            </div>

            <div>
                <dt class="text-muted font-medium">Amount</dt>
                <dd class="text-ink mt-0.5 font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd>
            </div>

            <div>
                <dt class="text-muted font-medium">Method</dt>
                <dd class="text-ink mt-0.5">
                    @if($payment->method)
                        {{ str_replace('_', ' ', ucwords($payment->method, '_')) }}
                    @else
                        <span class="text-muted italic">Not selected</span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-muted font-medium">Created</dt>
                <dd class="text-ink mt-0.5">{{ $payment->created_at->format('M d, Y H:i') }}</dd>
            </div>

            @if($payment->paid_at)
                <div>
                    <dt class="text-muted font-medium">Paid At</dt>
                    <dd class="text-ink mt-0.5">{{ $payment->paid_at->format('M d, Y H:i') }}</dd>
                </div>
            @endif

            @if($payment->failure_reason)
                <div class="sm:col-span-2">
                    <dt class="text-muted font-medium">Failure Reason</dt>
                    <dd class="text-red-700 mt-0.5">{{ $payment->failure_reason }}</dd>
                </div>
            @endif
        </dl>
    </div>

    {{-- Expiry Countdown --}}
    @if($payment->status === 'pending' && $payment->expires_at)
        <div class="bg-canvas border border-hairline rounded-md p-6 mb-6" x-data="{
            expiresAt: '{{ $payment->expires_at->toIso8601String() }}',
            remaining: '',
            expired: false,
            init() {
                this.updateCountdown();
                setInterval(() => this.updateCountdown(), 1000);
            },
            updateCountdown() {
                const now = new Date();
                const expiry = new Date(this.expiresAt);
                const diff = expiry - now;

                if (diff <= 0) {
                    this.expired = true;
                    this.remaining = 'Expired';
                    return;
                }

                const minutes = Math.floor(diff / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);
                this.remaining = minutes + 'm ' + seconds + 's remaining';
            }
        }">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-ink">Payment Expiry</p>
                    <p class="text-sm" :class="expired ? 'text-red-600 font-semibold' : 'text-muted'" x-text="remaining"></p>
                </div>
            </div>
        </div>
    @elseif($payment->status === 'expired')
        <div class="bg-canvas border border-red-200 rounded-md p-6 mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-red-800">Payment Expired</p>
                    <p class="text-sm text-red-600">This payment has expired and can no longer be completed.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Method Selection Form (only for pending payments) --}}
    @if($payment->status === 'pending')
        <div class="bg-canvas border border-hairline rounded-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-ink mb-4">Select Payment Method</h2>

            <form method="POST" action="{{ route('payments.method', $payment) }}">
                @csrf

                <div class="space-y-3 mb-6">
                    <label class="flex items-center gap-3 p-3 border border-hairline rounded-sm cursor-pointer hover:bg-surface-soft transition-colors has-[:checked]:border-rausch has-[:checked]:bg-surface-soft">
                        <input type="radio" name="method" value="bank_transfer" class="text-rausch focus:ring-rausch" {{ $payment->method === 'bank_transfer' ? 'checked' : '' }}>
                        <div>
                            <p class="text-sm font-medium text-ink">Bank Transfer</p>
                            <p class="text-xs text-muted">Simulated bank transfer payment</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 border border-hairline rounded-sm cursor-pointer hover:bg-surface-soft transition-colors has-[:checked]:border-rausch has-[:checked]:bg-surface-soft">
                        <input type="radio" name="method" value="e_wallet" class="text-rausch focus:ring-rausch" {{ $payment->method === 'e_wallet' ? 'checked' : '' }}>
                        <div>
                            <p class="text-sm font-medium text-ink">E-Wallet</p>
                            <p class="text-xs text-muted">Simulated e-wallet payment</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 border border-hairline rounded-sm cursor-pointer hover:bg-surface-soft transition-colors has-[:checked]:border-rausch has-[:checked]:bg-surface-soft">
                        <input type="radio" name="method" value="credit_card" class="text-rausch focus:ring-rausch" {{ $payment->method === 'credit_card' ? 'checked' : '' }}>
                        <div>
                            <p class="text-sm font-medium text-ink">Credit Card</p>
                            <p class="text-xs text-muted">Simulated credit card payment</p>
                        </div>
                    </label>
                </div>

                @error('method')
                    <p class="text-sm text-red-600 mb-4">{{ $message }}</p>
                @enderror

                <button type="submit" class="w-full bg-ink text-canvas font-medium text-sm px-6 py-2 rounded-sm hover:bg-body transition-colors">
                    Save Payment Method
                </button>
            </form>
        </div>

        {{-- Proceed to Confirm (only if method is selected) --}}
        @if($payment->method)
            <div class="bg-canvas border border-hairline rounded-md p-6">
                <a href="{{ route('payments.confirm', $payment) }}" class="block w-full bg-rausch text-on-primary font-semibold text-sm px-6 py-3 rounded-sm hover:bg-rausch-active transition-colors text-center">
                    Proceed to Payment Confirmation
                </a>
            </div>
        @endif
    @endif

    {{-- Retry Button (for failed payments) --}}
    @if($payment->status === 'failed')
        <div class="bg-canvas border border-hairline rounded-md p-6">
            <p class="text-sm text-muted mb-4">This payment has failed. You can create a new payment attempt.</p>
            <form method="POST" action="{{ route('payments.retry', $payment) }}">
                @csrf
                <button type="submit" class="w-full bg-ink text-canvas font-medium text-sm px-6 py-2 rounded-sm hover:bg-body transition-colors">
                    Retry Payment
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
