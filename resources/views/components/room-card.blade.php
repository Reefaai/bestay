{{-- Room card component --}}
{{-- Receives: $room (object with id, name, type, description, capacity, price_per_night, image_url, is_active) --}}
<a href="/rooms/{{ $room->id }}" class="group block rounded-md overflow-hidden bg-canvas border border-hairline-soft hover:shadow-lg transition-shadow duration-200">
    {{-- Room Image --}}
    <div class="aspect-[4/3] overflow-hidden bg-surface-soft">
        @if($room->image_url)
            <img
                src="{{ $room->image_url }}"
                alt="{{ $room->name }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            >
        @else
            <div class="w-full h-full flex items-center justify-center text-muted-soft">
                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Room Details --}}
    <div class="p-4">
        {{-- Room Type --}}
        <p class="text-muted text-xs font-medium uppercase tracking-wide mb-0.5">{{ $room->type }}</p>

        {{-- Room Name --}}
        <h3 class="text-ink font-semibold text-base leading-tight mb-1">{{ $room->name }}</h3>

        {{-- Capacity --}}
        <div class="flex items-center gap-1 text-muted text-sm mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            <span>{{ $room->capacity }} {{ $room->capacity === 1 ? 'guest' : 'guests' }}</span>
        </div>

        {{-- Price --}}
        <div class="flex items-baseline gap-0.5">
            <span class="text-ink font-bold text-lg">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}</span>
            <span class="text-muted text-sm">/ malam</span>
        </div>
    </div>
</a>
