@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Total Bookings --}}
    <div class="bg-canvas border border-hairline rounded-md p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-muted uppercase tracking-wide">Total Bookings</span>
            <div class="w-8 h-8 rounded-sm bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-ink">{{ number_format($stats['total_bookings']) }}</p>
        <p class="text-xs text-muted mt-1">
            <span class="text-amber-600 font-medium">{{ $stats['pending_bookings'] }} pending</span>
            · {{ $stats['confirmed_bookings'] }} confirmed
        </p>
    </div>

    {{-- Revenue --}}
    <div class="bg-canvas border border-hairline rounded-md p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-muted uppercase tracking-wide">Total Revenue</span>
            <div class="w-8 h-8 rounded-sm bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-ink">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
        <p class="text-xs text-muted mt-1">From paid payments</p>
    </div>

    {{-- Users --}}
    <div class="bg-canvas border border-hairline rounded-md p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-muted uppercase tracking-wide">Registered Users</span>
            <div class="w-8 h-8 rounded-sm bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-ink">{{ number_format($stats['total_users']) }}</p>
        <p class="text-xs text-muted mt-1">Guest accounts</p>
    </div>

    {{-- Rooms --}}
    <div class="bg-canvas border border-hairline rounded-md p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-muted uppercase tracking-wide">Rooms</span>
            <div class="w-8 h-8 rounded-sm bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-ink">{{ $stats['active_rooms'] }} <span class="text-base font-normal text-muted">/ {{ $stats['total_rooms'] }}</span></p>
        <p class="text-xs text-muted mt-1">Active rooms</p>
    </div>
</div>

{{-- Alert banners --}}
@if($stats['conflict_count'] > 0)
    <div class="mb-6 flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-md px-5 py-4">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
        <div class="flex-1">
            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">
                {{ $stats['conflict_count'] }} booking conflict{{ $stats['conflict_count'] > 1 ? 's' : '' }} detected
            </p>
            <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">Multiple bookings overlap on the same room and dates.</p>
        </div>
        <a href="{{ route('admin.bookings.conflicts') }}" class="text-xs font-medium text-amber-800 dark:text-amber-200 underline hover:no-underline flex-shrink-0">
            View Conflicts →
        </a>
    </div>
@endif

@if($stats['pending_payments'] > 0)
    <div class="mb-6 flex items-center gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md px-5 py-4">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
        </svg>
        <div class="flex-1">
            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                {{ $stats['pending_payments'] }} payment{{ $stats['pending_payments'] > 1 ? 's' : '' }} awaiting action
            </p>
        </div>
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="text-xs font-medium text-blue-800 dark:text-blue-200 underline hover:no-underline flex-shrink-0">
            Review →
        </a>
    </div>
@endif

{{-- Charts row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Monthly Bookings Chart --}}
    <div class="lg:col-span-2 bg-canvas border border-hairline rounded-md p-5">
        <h2 class="text-sm font-semibold text-ink mb-4">Bookings — Last 6 Months</h2>
        <div class="relative h-52">
            <canvas id="bookingsChart"></canvas>
        </div>
    </div>

    {{-- Payment Status Donut --}}
    <div class="bg-canvas border border-hairline rounded-md p-5">
        <h2 class="text-sm font-semibold text-ink mb-4">Payment Status</h2>
        <div class="relative h-40 flex items-center justify-center">
            <canvas id="paymentDonut"></canvas>
        </div>
        <div class="mt-4 space-y-1.5">
            @php
                $statusColors = [
                    'pending'  => ['dot' => 'bg-amber-400',  'label' => 'Pending'],
                    'paid'     => ['dot' => 'bg-emerald-500','label' => 'Paid'],
                    'failed'   => ['dot' => 'bg-red-500',    'label' => 'Failed'],
                    'expired'  => ['dot' => 'bg-gray-400',   'label' => 'Expired'],
                    'refunded' => ['dot' => 'bg-purple-500', 'label' => 'Refunded'],
                ];
            @endphp
            @foreach($statusColors as $key => $meta)
                @if(isset($paymentStats[$key]) && $paymentStats[$key] > 0)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $meta['dot'] }} flex-shrink-0"></span>
                            <span class="text-muted">{{ $meta['label'] }}</span>
                        </div>
                        <span class="font-medium text-ink">{{ $paymentStats[$key] }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

{{-- Revenue Chart --}}
<div class="bg-canvas border border-hairline rounded-md p-5 mb-6">
    <h2 class="text-sm font-semibold text-ink mb-4">Revenue — Last 6 Months</h2>
    <div class="relative h-48">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

{{-- Recent activity --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Bookings --}}
    <div class="bg-canvas border border-hairline rounded-md">
        <div class="flex items-center justify-between px-5 py-4 border-b border-hairline">
            <h2 class="text-sm font-semibold text-ink">Recent Bookings</h2>
            <a href="{{ route('admin.bookings.index') }}" class="text-xs text-rausch hover:text-rausch-active font-medium transition-colors">View all →</a>
        </div>
        <div class="divide-y divide-hairline">
            @forelse($recentBookings as $booking)
                <a href="{{ route('admin.bookings.show', $booking) }}" class="flex items-center gap-3 px-5 py-3.5 hover:bg-surface-soft transition-colors">
                    <div class="w-8 h-8 rounded-full bg-rausch/10 flex items-center justify-center text-rausch font-semibold text-xs flex-shrink-0">
                        {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink truncate">{{ $booking->user->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-muted truncate">{{ $booking->room->name ?? 'Deleted Room' }} · {{ $booking->check_in->format('d M') }}–{{ $booking->check_out->format('d M Y') }}</p>
                    </div>
                    @include('components.status-badge', ['status' => $booking->status])
                </a>
            @empty
                <p class="px-5 py-6 text-sm text-muted text-center">No bookings yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="bg-canvas border border-hairline rounded-md">
        <div class="flex items-center justify-between px-5 py-4 border-b border-hairline">
            <h2 class="text-sm font-semibold text-ink">Recent Payments</h2>
            <a href="{{ route('admin.payments.index') }}" class="text-xs text-rausch hover:text-rausch-active font-medium transition-colors">View all →</a>
        </div>
        <div class="divide-y divide-hairline">
            @forelse($recentPayments as $payment)
                <a href="{{ route('admin.payments.show', $payment) }}" class="flex items-center gap-3 px-5 py-3.5 hover:bg-surface-soft transition-colors">
                    <div class="w-8 h-8 rounded-sm bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink truncate">{{ $payment->reference }}</p>
                        <p class="text-xs text-muted truncate">{{ $payment->booking->user->name ?? 'Unknown' }} · Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    </div>
                    @include('components.status-badge', ['status' => $payment->status])
                </a>
            @empty
                <p class="px-5 py-6 text-sm text-muted text-center">No payments yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor  = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
    const labelColor = isDark ? '#a3a3a3' : '#6a6a6a';

    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: labelColor, font: { size: 11 } } },
            y: { grid: { color: gridColor }, ticks: { color: labelColor, font: { size: 11 } }, beginAtZero: true },
        },
    };

    // Bookings bar chart
    const bookingLabels  = @json(array_column($monthlyBookings, 'label'));
    const bookingCounts  = @json(array_column($monthlyBookings, 'count'));

    new Chart(document.getElementById('bookingsChart'), {
        type: 'bar',
        data: {
            labels: bookingLabels,
            datasets: [{
                label: 'Bookings',
                data: bookingCounts,
                backgroundColor: 'rgba(255, 56, 92, 0.15)',
                borderColor: '#ff385c',
                borderWidth: 2,
                borderRadius: 4,
            }],
        },
        options: { ...defaultOptions },
    });

    // Revenue line chart
    const revenueLabels  = @json(array_column($monthlyRevenue, 'label'));
    const revenueAmounts = @json(array_column($monthlyRevenue, 'amount'));

    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Revenue (Rp)',
                data: revenueAmounts,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                borderWidth: 2,
                pointBackgroundColor: '#10b981',
                pointRadius: 4,
                fill: true,
                tension: 0.3,
            }],
        },
        options: {
            ...defaultOptions,
            scales: {
                ...defaultOptions.scales,
                y: {
                    ...defaultOptions.scales.y,
                    ticks: {
                        ...defaultOptions.scales.y.ticks,
                        callback: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v),
                    },
                },
            },
        },
    });

    // Payment donut
    const paymentStats = @json($paymentStats);
    const donutLabels  = Object.keys(paymentStats);
    const donutData    = Object.values(paymentStats);
    const donutColors  = {
        pending:  '#fbbf24',
        paid:     '#10b981',
        failed:   '#ef4444',
        expired:  '#9ca3af',
        refunded: '#a855f7',
    };

    new Chart(document.getElementById('paymentDonut'), {
        type: 'doughnut',
        data: {
            labels: donutLabels,
            datasets: [{
                data: donutData,
                backgroundColor: donutLabels.map(l => donutColors[l] || '#9ca3af'),
                borderWidth: 0,
                hoverOffset: 4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.label}: ${ctx.parsed}`,
                    },
                },
            },
            cutout: '65%',
        },
    });
})();
</script>
@endpush
