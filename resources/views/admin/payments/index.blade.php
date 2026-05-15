@extends('layouts.admin')

@section('title', 'Payment Monitoring')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-ink">Payment Monitoring</h1>
        <p class="text-sm text-muted mt-0.5">Monitor and verify all guest payments</p>
    </div>
</div>

{{-- Flash Messages --}}
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

{{-- Filters --}}
<div class="bg-canvas border border-hairline rounded-md p-4 mb-5">
    <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-medium text-muted mb-1">Search</label>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Reference, guest name or email…"
                class="w-full border border-hairline rounded-sm px-3 py-2 text-sm text-ink bg-canvas focus:outline-none focus:ring-2 focus:ring-rausch/20 focus:border-rausch transition-colors"
            >
        </div>
        <div>
            <label class="block text-xs font-medium text-muted mb-1">Status</label>
            <select name="status" class="border border-hairline rounded-sm px-3 py-2 text-sm text-ink bg-canvas focus:outline-none focus:ring-2 focus:ring-rausch/20 focus:border-rausch transition-colors">
                <option value="">All Statuses</option>
                @foreach(['pending','paid','failed','expired','refunded'] as $s)
                    <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-muted mb-1">Method</label>
            <select name="method" class="border border-hairline rounded-sm px-3 py-2 text-sm text-ink bg-canvas focus:outline-none focus:ring-2 focus:ring-rausch/20 focus:border-rausch transition-colors">
                <option value="">All Methods</option>
                <option value="bank_transfer" {{ $method === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="e_wallet"      {{ $method === 'e_wallet'      ? 'selected' : '' }}>E-Wallet</option>
                <option value="credit_card"   {{ $method === 'credit_card'   ? 'selected' : '' }}>Credit Card</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-on-primary bg-rausch rounded-sm hover:bg-rausch-active transition-colors">
                Filter
            </button>
            @if($status || $method || $search)
                <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 text-sm font-medium text-muted border border-hairline rounded-sm hover:bg-surface-soft transition-colors">
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Table --}}
@if($payments->count() > 0)
    <div class="bg-canvas border border-hairline rounded-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-soft border-b border-hairline">
                    <tr>
                        <th class="text-left px-5 py-3.5 font-medium text-muted">Reference</th>
                        <th class="text-left px-5 py-3.5 font-medium text-muted">Guest</th>
                        <th class="text-left px-5 py-3.5 font-medium text-muted">Room</th>
                        <th class="text-left px-5 py-3.5 font-medium text-muted">Method</th>
                        <th class="text-left px-5 py-3.5 font-medium text-muted">Status</th>
                        <th class="text-right px-5 py-3.5 font-medium text-muted">Amount</th>
                        <th class="text-left px-5 py-3.5 font-medium text-muted">Date</th>
                        <th class="text-right px-5 py-3.5 font-medium text-muted">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @foreach($payments as $payment)
                        <tr class="hover:bg-surface-soft transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs text-ink">{{ $payment->reference }}</td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-ink">{{ $payment->booking->user->name ?? '—' }}</p>
                                <p class="text-xs text-muted">{{ $payment->booking->user->email ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-body">{{ $payment->booking->room->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-body">
                                @if($payment->method)
                                    {{ str_replace('_', ' ', ucfirst($payment->method)) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @include('components.status-badge', ['status' => $payment->status])
                            </td>
                            <td class="px-5 py-3.5 text-right font-medium text-ink">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-muted text-xs">
                                {{ $payment->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="text-sm font-medium text-rausch hover:text-rausch-active transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        @include('components.pagination', ['paginator' => $payments])
    </div>
@else
    <div class="bg-canvas border border-hairline rounded-md text-center py-16">
        <svg class="w-12 h-12 mx-auto text-muted-soft mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
        </svg>
        <h3 class="text-base font-semibold text-ink mb-1">No payments found</h3>
        <p class="text-sm text-muted">Try adjusting your filters.</p>
    </div>
@endif
@endsection
