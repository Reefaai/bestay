@extends('layouts.app')

@section('title', 'Manage Rooms')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-ink">Manage Rooms</h1>
            <p class="text-muted mt-1">Create, edit, and manage your room inventory</p>
        </div>
        <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center gap-1 bg-rausch text-on-primary font-medium text-sm px-8 py-2 rounded-sm hover:bg-rausch-active transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create Room
        </a>
    </div>

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

    {{-- Rooms Table --}}
    @if($rooms->count() > 0)
        <div class="overflow-x-auto border border-hairline rounded-md">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-soft border-b border-hairline">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-ink">Name</th>
                        <th class="px-6 py-4 font-semibold text-ink">Type</th>
                        <th class="px-6 py-4 font-semibold text-ink">Price/Night</th>
                        <th class="px-6 py-4 font-semibold text-ink">Capacity</th>
                        <th class="px-6 py-4 font-semibold text-ink">Status</th>
                        <th class="px-6 py-4 font-semibold text-ink text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    @foreach($rooms as $room)
                        <tr class="hover:bg-surface-soft transition-colors">
                            <td class="px-6 py-4 font-medium text-ink">{{ $room->name }}</td>
                            <td class="px-6 py-4 text-muted capitalize">{{ $room->type }}</td>
                            <td class="px-6 py-4 text-ink">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-muted">{{ $room->capacity }} {{ Str::plural('guest', $room->capacity) }}</td>
                            <td class="px-6 py-4">
                                @if($room->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-emerald-100 text-emerald-800 border-emerald-200">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.rooms.edit', $room) }}" class="inline-flex items-center px-4 py-1 text-sm font-medium text-ink border border-hairline rounded-sm hover:bg-surface-soft transition-colors">
                                        Edit
                                    </a>

                                    @if($room->is_active)
                                        <div x-data="{ showConfirm: false }">
                                            <button
                                                @click="showConfirm = true"
                                                class="inline-flex items-center px-4 py-1 text-sm font-medium text-error border border-error rounded-sm hover:bg-red-50 transition-colors"
                                            >
                                                Deactivate
                                            </button>

                                            {{-- Confirmation Dialog --}}
                                            <div
                                                x-show="showConfirm"
                                                x-cloak
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                                @keydown.escape.window="showConfirm = false"
                                            >
                                                <div class="absolute inset-0 bg-ink/50" @click="showConfirm = false"></div>

                                                <div
                                                    x-show="showConfirm"
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="relative bg-canvas rounded-md p-8 shadow-lg max-w-sm w-full"
                                                >
                                                    <h3 class="text-lg font-semibold text-ink mb-2">Deactivate Room</h3>
                                                    <p class="text-sm text-muted mb-6">
                                                        Are you sure you want to deactivate <strong>{{ $room->name }}</strong>? It will no longer appear in room listings.
                                                    </p>

                                                    <div class="flex gap-2 justify-end">
                                                        <button
                                                            @click="showConfirm = false"
                                                            class="px-6 py-2 text-sm font-medium text-muted border border-hairline rounded-sm hover:bg-surface-soft transition-colors"
                                                        >
                                                            Cancel
                                                        </button>

                                                        <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" x-data="{ submitting: false }" @submit="submitting = true">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                type="submit"
                                                                :disabled="submitting"
                                                                class="px-6 py-2 text-sm font-medium text-on-primary bg-error rounded-sm hover:bg-error-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                            >
                                                                <span x-show="!submitting">Yes, Deactivate</span>
                                                                <span x-show="submitting" class="flex items-center gap-1">
                                                                    <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                    </svg>
                                                                    Deactivating...
                                                                </span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-muted-soft mb-6" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 7.5h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
            </svg>
            <h3 class="text-lg font-semibold text-ink mb-1">No rooms yet</h3>
            <p class="text-muted mb-6">Get started by creating your first room.</p>
            <a href="{{ route('admin.rooms.create') }}" class="inline-block bg-rausch text-on-primary font-medium text-sm px-8 py-2 rounded-sm hover:bg-rausch-active transition-colors">
                Create Room
            </a>
        </div>
    @endif
</div>
@endsection
