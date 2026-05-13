@extends('layouts.app')

@section('title', 'Confirm Payment — ' . $payment->reference)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6">
        <ol class="flex items-center gap-1 text-sm text-muted">
            <li><a href="{{ route('dashboard') }}" class="hover:text-ink transition-colors">My Bookings</a></li>
            <li><span class="mx-1">/</span></li>
            <li><a href="{{ route('bookings.payment', $payment->booking_id) }}" class="hover:text-ink transition-colors">Payment</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-ink font-medium">Confirm</li>
        </ol>
    </nav>

    {{-- Payment Summary --}}
    <div class="bg-canvas border border-hairline rounded-md p-6 mb-6">
        <h1 class="text-2xl font-bold text-ink mb-4">Confirm Payment</h1>

        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
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
                <dd class="text-ink mt-0.5">{{ str_replace('_', ' ', ucwords($payment->method, '_')) }}</dd>
            </div>
        </dl>
    </div>

    {{-- Simulation Form --}}
    <div class="bg-canvas border border-hairline rounded-md p-6" x-data="{ outcome: '' }">
        <h2 class="text-lg font-semibold text-ink mb-2">Simulate Payment Outcome</h2>
        <p class="text-sm text-muted mb-6">This is a dummy payment. Choose the outcome you want to simulate.</p>

        <form method="POST" action="{{ route('payments.confirm.submit', $payment) }}">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                {{-- Success Button --}}
                <label class="relative flex flex-col items-center gap-2 p-6 border-2 rounded-md cursor-pointer transition-colors"
                    :class="outcome === 'success' ? 'border-emerald-500 bg-surface-soft' : 'border-hairline hover:border-emerald-300 hover:bg-surface-soft'">>
                    <input type="radio" name="outcome" value="success" class="sr-only" x-model="outcome">
                    <svg class="w-8 h-8" :class="outcome === 'success' ? 'text-emerald-600' : 'text-muted'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-semibold" :class="outcome === 'success' ? 'text-emerald-800' : 'text-ink'">Simulate Success</span>
                    <span class="text-xs text-muted text-center">Payment will be marked as paid</span>
                </label>

                {{-- Failure Button --}}
                <label class="relative flex flex-col items-center gap-2 p-6 border-2 rounded-md cursor-pointer transition-colors"
                    :class="outcome === 'fail' ? 'border-red-500 bg-surface-soft' : 'border-hairline hover:border-red-300 hover:bg-surface-soft'">
                    <input type="radio" name="outcome" value="fail" class="sr-only" x-model="outcome">
                    <svg class="w-8 h-8" :class="outcome === 'fail' ? 'text-red-600' : 'text-muted'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-semibold" :class="outcome === 'fail' ? 'text-red-800' : 'text-ink'">Simulate Failure</span>
                    <span class="text-xs text-muted text-center">Payment will be marked as failed</span>
                </label>
            </div>

            @error('outcome')
                <p class="text-sm text-red-600 mb-4">{{ $message }}</p>
            @enderror

            {{-- Failure Reason Textarea (shown only when fail is selected) --}}
            <div x-show="outcome === 'fail'" x-transition class="mb-6">
                <label for="failure_reason" class="block text-sm font-medium text-ink mb-1">Failure Reason</label>
                <textarea
                    id="failure_reason"
                    name="failure_reason"
                    rows="3"
                    maxlength="500"
                    placeholder="Describe why the payment failed (required, max 500 characters)"
                    class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-rausch focus:border-rausch resize-none"
                >{{ old('failure_reason') }}</textarea>
                <p class="text-xs text-muted mt-1">Required when simulating failure. Maximum 500 characters.</p>

                @error('failure_reason')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                :disabled="!outcome"
                class="w-full font-semibold text-sm px-6 py-3 rounded-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                :class="outcome === 'success' ? 'bg-emerald-600 text-white hover:bg-emerald-700' : (outcome === 'fail' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-ink text-canvas')"
            >
                <span x-show="outcome === 'success'">Confirm Payment Success</span>
                <span x-show="outcome === 'fail'">Confirm Payment Failure</span>
                <span x-show="!outcome">Select an outcome above</span>
            </button>
        </form>
    </div>
</div>
@endsection
