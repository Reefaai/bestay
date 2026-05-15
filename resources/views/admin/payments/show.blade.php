@extends('layouts.admin')

@section('title', 'Payment ' . $payment->reference)

@section('content')
{{-- Back --}}
<div class="mb-5">
    <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-1 text-sm text-muted hover:text-ink transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
        Back to Payments
    </a>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="mb-5 rounded-sm border border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 dark:border-emerald-800 px-5 py-3.5">
        <p class="text-sm text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
    </div>
@endif
@if(session('error'))
    <div class="mb-5 rounded-sm border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-5 py-3.5">
        <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: details --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Payment Info --}}
        <div class="bg-canvas border border-hairline rounded-md p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-ink">Payment Details</h2>
                @include('components.status-badge', ['status' => $payment->status])
            </div>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-muted">Reference</dt>
                    <dd class="text-sm font-mono font-medium text-ink mt-0.5">{{ $payment->reference }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted">Amount</dt>
                    <dd class="text-sm font-semibold text-ink mt-0.5">Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted">Method</dt>
                    <dd class="text-sm text-ink mt-0.5">{{ $payment->method ? str_replace('_', ' ', ucfirst($payment->method)) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted">Created</dt>
                    <dd class="text-sm text-ink mt-0.5">{{ $payment->created_at->format('d M Y, H:i') }}</dd>
                </div>
                @if($payment->expires_at)
                    <div>
                        <dt class="text-xs text-muted">Expires At</dt>
                        <dd class="text-sm text-ink mt-0.5 {{ $payment->isExpired() && $payment->status === 'pending' ? 'text-error' : '' }}">
                            {{ $payment->expires_at->format('d M Y, H:i') }}
                            @if($payment->isExpired() && $payment->status === 'pending')
                                <span class="text-xs text-error">(expired)</span>
                            @endif
                        </dd>
                    </div>
                @endif
                @if($payment->paid_at)
                    <div>
                        <dt class="text-xs text-muted">Paid At</dt>
                        <dd class="text-sm text-ink mt-0.5">{{ $payment->paid_at->format('d M Y, H:i') }}</dd>
                    </div>
                @endif
                @if($payment->refunded_at)
                    <div>
                        <dt class="text-xs text-muted">Refunded At</dt>
                        <dd class="text-sm text-ink mt-0.5">{{ $payment->refunded_at->format('d M Y, H:i') }}</dd>
                    </div>
                @endif
                @if($payment->failure_reason)
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-muted">Failure Reason</dt>
                        <dd class="text-sm text-error mt-0.5">{{ $payment->failure_reason }}</dd>
                    </div>
                @endif
                @if($payment->verifier)
                    <div>
                        <dt class="text-xs text-muted">Verified By</dt>
                        <dd class="text-sm text-ink mt-0.5">{{ $payment->verifier->name }} at {{ $payment->verified_at?->format('d M Y, H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Booking Info --}}
        <div class="bg-canvas border border-hairline rounded-md p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-ink">Booking</h2>
                <a href="{{ route('admin.bookings.show', $payment->booking) }}" class="text-xs text-rausch hover:text-rausch-active font-medium transition-colors">
                    View Booking →
                </a>
            </div>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-muted">Guest</dt>
                    <dd class="text-sm font-medium text-ink mt-0.5">{{ $payment->booking->user->name ?? '—' }}</dd>
                    <dd class="text-xs text-muted">{{ $payment->booking->user->email ?? '' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted">Room</dt>
                    <dd class="text-sm font-medium text-ink mt-0.5">{{ $payment->booking->room->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted">Check-in</dt>
                    <dd class="text-sm text-ink mt-0.5">{{ $payment->booking->check_in->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted">Check-out</dt>
                    <dd class="text-sm text-ink mt-0.5">{{ $payment->booking->check_out->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted">Booking Status</dt>
                    <dd class="mt-0.5">@include('components.status-badge', ['status' => $payment->booking->status])</dd>
                </div>
            </dl>
        </div>

        {{-- Status Log --}}
        <div class="bg-canvas border border-hairline rounded-md p-5">
            <h2 class="text-base font-semibold text-ink mb-4">Status History</h2>
            @if($payment->statusLogs->count() > 0)
                <ol class="relative border-l border-hairline ml-2 space-y-4">
                    @foreach($payment->statusLogs->sortByDesc('created_at') as $log)
                        <li class="ml-5">
                            <span class="absolute -left-1.5 mt-1 w-3 h-3 rounded-full border-2 border-canvas bg-hairline"></span>
                            <div class="flex flex-wrap items-center gap-2 mb-0.5">
                                @if($log->from_status)
                                    @include('components.status-badge', ['status' => $log->from_status])
                                    <svg class="w-3 h-3 text-muted" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                @endif
                                @include('components.status-badge', ['status' => $log->to_status])
                                <span class="text-xs text-muted">by {{ ucfirst($log->actor_type) }}{{ $log->actor ? ' (' . $log->actor->name . ')' : '' }}</span>
                            </div>
                            @if($log->reason)
                                <p class="text-xs text-muted italic">{{ $log->reason }}</p>
                            @endif
                            <p class="text-xs text-muted-soft mt-0.5">{{ $log->created_at->format('d M Y, H:i') }}</p>
                        </li>
                    @endforeach
                </ol>
            @else
                <p class="text-sm text-muted">No status history available.</p>
            @endif
        </div>
    </div>

    {{-- Right: Admin actions --}}
    <div class="lg:col-span-1">
        <div class="bg-canvas border border-hairline rounded-md p-5 sticky top-6">
            <h2 class="text-base font-semibold text-ink mb-4">Admin Override</h2>

            @php
                $canOverride = in_array($payment->status, ['pending', 'paid']);
                $allowedTargets = [];
                if ($payment->status === 'pending') {
                    $allowedTargets = ['paid', 'failed'];
                } elseif ($payment->status === 'paid' && $payment->booking->status === 'cancelled') {
                    $allowedTargets = ['refunded'];
                }
            @endphp

            @if(count($allowedTargets) > 0)
                <form
                    method="POST"
                    action="{{ route('admin.payments.updateStatus', $payment) }}"
                    x-data="{ submitting: false, target: '' }"
                    @submit="submitting = true"
                >
                    @csrf
                    @method('PATCH')

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1">New Status</label>
                            <select
                                name="status"
                                x-model="target"
                                required
                                class="w-full border border-hairline rounded-sm px-3 py-2 text-sm text-ink bg-canvas focus:outline-none focus:ring-2 focus:ring-rausch/20 focus:border-rausch transition-colors"
                            >
                                <option value="">Select status…</option>
                                @foreach($allowedTargets as $t)
                                    <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-muted mb-1">Reason <span class="text-muted-soft">(optional)</span></label>
                            <textarea
                                name="reason"
                                rows="3"
                                placeholder="Reason for override…"
                                class="w-full border border-hairline rounded-sm px-3 py-2 text-sm text-ink bg-canvas focus:outline-none focus:ring-2 focus:ring-rausch/20 focus:border-rausch transition-colors resize-none"
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            :disabled="submitting || !target"
                            class="w-full px-4 py-2 text-sm font-medium text-on-primary bg-rausch rounded-sm hover:bg-rausch-active transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span x-show="!submitting">Apply Override</span>
                            <span x-show="submitting" class="flex items-center justify-center gap-1">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing…
                            </span>
                        </button>
                    </div>
                </form>
            @else
                <p class="text-sm text-muted">
                    @if($payment->isTerminal())
                        This payment is in a terminal state ({{ $payment->status }}) and cannot be changed.
                    @elseif($payment->status === 'paid' && $payment->booking->status !== 'cancelled')
                        Refund is only available when the associated booking is cancelled.
                    @else
                        No override actions available for this payment.
                    @endif
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
