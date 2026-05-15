@extends('layouts.admin')

@section('title', 'Edit Room')

@section('content')
<div>
    <div class="mb-6">
        <a href="{{ route('admin.rooms.index') }}" class="inline-flex items-center gap-1 text-sm text-muted hover:text-ink transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Back to Rooms
        </a>
        <h1 class="text-xl font-bold text-ink">Edit Room</h1>
        <p class="text-sm text-muted mt-0.5">Update details for <strong>{{ $room->name }}</strong></p>
    </div>

    {{-- Flash Messages --}}
    @if(session('error'))
        <div class="mb-6 rounded-sm border border-red-200 bg-red-50 px-6 py-4">
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Edit Room Form --}}
    <form method="POST" action="{{ route('admin.rooms.update', $room) }}" x-data="{ submitting: false }" @submit="submitting = true" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-ink mb-1">Room Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $room->name) }}"
                required
                class="w-full px-4 py-2 border border-hairline rounded-sm text-ink placeholder-muted-soft focus:outline-none focus:ring-2 focus:ring-rausch focus:border-transparent transition-colors @error('name') border-error @enderror"
                placeholder="e.g. Ocean View Suite"
            >
            @error('name')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Type --}}
        <div>
            <label for="type" class="block text-sm font-medium text-ink mb-1">Room Type</label>
            <select
                id="type"
                name="type"
                required
                class="w-full px-4 py-2 border border-hairline rounded-sm text-ink focus:outline-none focus:ring-2 focus:ring-rausch focus:border-transparent transition-colors @error('type') border-error @enderror"
            >
                <option value="standard" {{ old('type', $room->type) === 'standard' ? 'selected' : '' }}>Standard</option>
                <option value="deluxe" {{ old('type', $room->type) === 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                <option value="suite" {{ old('type', $room->type) === 'suite' ? 'selected' : '' }}>Suite</option>
                <option value="family" {{ old('type', $room->type) === 'family' ? 'selected' : '' }}>Family</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="block text-sm font-medium text-ink mb-1">Description</label>
            <textarea
                id="description"
                name="description"
                rows="4"
                class="w-full px-4 py-2 border border-hairline rounded-sm text-ink placeholder-muted-soft focus:outline-none focus:ring-2 focus:ring-rausch focus:border-transparent transition-colors @error('description') border-error @enderror"
                placeholder="Describe the room features, amenities, and atmosphere..."
            >{{ old('description', $room->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Price and Capacity Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            {{-- Price per Night --}}
            <div>
                <label for="price_per_night" class="block text-sm font-medium text-ink mb-1">Harga per Malam (Rp)</label>
                <input
                    type="number"
                    id="price_per_night"
                    name="price_per_night"
                    value="{{ old('price_per_night', $room->price_per_night) }}"
                    required
                    min="0"
                    step="0.01"
                    class="w-full px-4 py-2 border border-hairline rounded-sm text-ink placeholder-muted-soft focus:outline-none focus:ring-2 focus:ring-rausch focus:border-transparent transition-colors @error('price_per_night') border-error @enderror"
                    placeholder="0.00"
                >
                @error('price_per_night')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Capacity --}}
            <div>
                <label for="capacity" class="block text-sm font-medium text-ink mb-1">Capacity (guests)</label>
                <input
                    type="number"
                    id="capacity"
                    name="capacity"
                    value="{{ old('capacity', $room->capacity) }}"
                    required
                    min="1"
                    max="100"
                    class="w-full px-4 py-2 border border-hairline rounded-sm text-ink placeholder-muted-soft focus:outline-none focus:ring-2 focus:ring-rausch focus:border-transparent transition-colors @error('capacity') border-error @enderror"
                    placeholder="2"
                >
                @error('capacity')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Image URL --}}
        <div>
            <label for="image_url" class="block text-sm font-medium text-ink mb-1">Image URL</label>
            <input
                type="url"
                id="image_url"
                name="image_url"
                value="{{ old('image_url', $room->image_url) }}"
                class="w-full px-4 py-2 border border-hairline rounded-sm text-ink placeholder-muted-soft focus:outline-none focus:ring-2 focus:ring-rausch focus:border-transparent transition-colors @error('image_url') border-error @enderror"
                placeholder="https://example.com/room-image.jpg"
            >
            @error('image_url')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Active Status --}}
        <div class="flex items-center gap-2">
            <input
                type="hidden"
                name="is_active"
                value="0"
            >
            <input
                type="checkbox"
                id="is_active"
                name="is_active"
                value="1"
                {{ old('is_active', $room->is_active) ? 'checked' : '' }}
                class="w-4 h-4 text-rausch border-hairline rounded focus:ring-rausch"
            >
            <label for="is_active" class="text-sm font-medium text-ink">Room is active (visible in listings)</label>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-4 pt-4 border-t border-hairline-soft">
            <button
                type="submit"
                :disabled="submitting"
                class="bg-rausch text-on-primary font-medium text-sm px-8 py-2 rounded-sm hover:bg-rausch-active transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span x-show="!submitting">Update Room</span>
                <span x-show="submitting" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating...
                </span>
            </button>
            <a href="{{ route('admin.rooms.index') }}" class="text-sm font-medium text-muted hover:text-ink transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
