@extends('layouts.app')

@section('title', 'Browse Rooms')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-ink">Browse Rooms</h1>
        <p class="text-muted mt-1">Find the perfect room for your stay</p>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ url('/rooms') }}" class="mb-8">
        <div x-data="{
            type: '{{ request('type', '') }}',
            min_price: '{{ request('min_price', '') }}',
            max_price: '{{ request('max_price', '') }}',
            capacity: '{{ request('capacity', '') }}'
        }" class="bg-surface-soft rounded-md p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                {{-- Room Type Filter --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-ink mb-1">Room Type</label>
                    <select
                        id="type"
                        name="type"
                        x-model="type"
                        class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-rausch focus:border-rausch"
                    >
                        <option value="">All Types</option>
                        <option value="standard">Standard</option>
                        <option value="deluxe">Deluxe</option>
                        <option value="suite">Suite</option>
                        <option value="family">Family</option>
                    </select>
                </div>

                {{-- Min Price Filter --}}
                <div>
                    <label for="min_price" class="block text-sm font-medium text-ink mb-1">Harga Min</label>
                    <input
                        type="number"
                        id="min_price"
                        name="min_price"
                        x-model="min_price"
                        placeholder="Rp 0"
                        min="0"
                        class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-rausch focus:border-rausch"
                    >
                </div>

                {{-- Max Price Filter --}}
                <div>
                    <label for="max_price" class="block text-sm font-medium text-ink mb-1">Harga Max</label>
                    <input
                        type="number"
                        id="max_price"
                        name="max_price"
                        x-model="max_price"
                        placeholder="Tanpa batas"
                        min="0"
                        class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-rausch focus:border-rausch"
                    >
                </div>

                {{-- Capacity Filter --}}
                <div>
                    <label for="capacity" class="block text-sm font-medium text-ink mb-1">Guests</label>
                    <select
                        id="capacity"
                        name="capacity"
                        x-model="capacity"
                        class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-rausch focus:border-rausch"
                    >
                        <option value="">Any</option>
                        <option value="1">1+</option>
                        <option value="2">2+</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                        <option value="5">5+</option>
                        <option value="6">6+</option>
                    </select>
                </div>

                {{-- Submit Button --}}
                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="flex-1 bg-rausch text-on-primary font-medium text-sm px-6 py-2 rounded-sm hover:bg-rausch-active transition-colors"
                    >
                        Search
                    </button>
                    <a
                        href="{{ url('/rooms') }}"
                        class="px-3 py-2 rounded-sm border border-hairline text-muted text-sm hover:bg-surface-strong transition-colors"
                    >
                        Clear
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- Results Count --}}
    <div class="mb-6">
        <p class="text-sm text-muted">
            {{ $rooms->total() }} {{ Str::plural('room', $rooms->total()) }} found
        </p>
    </div>

    {{-- Room Cards Grid --}}
    @if($rooms->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($rooms as $room)
                @include('components.room-card', ['room' => $room])
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-muted-soft mb-6" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <h3 class="text-lg font-semibold text-ink mb-1">No rooms found</h3>
            <p class="text-muted mb-6">Try adjusting your filters to find available rooms.</p>
            <a href="{{ url('/rooms') }}" class="inline-block bg-rausch text-on-primary font-medium text-sm px-8 py-2 rounded-sm hover:bg-rausch-active transition-colors">
                Clear Filters
            </a>
        </div>
    @endif

    {{-- Pagination --}}
    <div class="mt-8">
        @include('components.pagination', ['paginator' => $rooms])
    </div>
</div>
@endsection
