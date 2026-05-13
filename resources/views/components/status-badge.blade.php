{{-- Status badge component --}}
{{-- Receives: $status (string: 'pending', 'confirmed', 'cancelled', 'completed') --}}
@php
    $badgeClasses = match($status) {
        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
        'confirmed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
        'completed' => 'bg-gray-100 text-gray-700 border-gray-200',
        default => 'bg-gray-100 text-gray-700 border-gray-200',
    };
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClasses }}">
    {{ ucfirst($status) }}
</span>
