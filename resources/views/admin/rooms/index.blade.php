@extends('layouts.admin')

@section('title', 'Manage Rooms')

@section('content')
<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-xl font-bold text-ink">Manage Rooms</h1>
            <p class="text-sm text-muted mt-0.5">Create, edit, and manage your room inventory</p>
        </div>
        <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center justify-center gap-1 bg-rausch text-on-primary font-medium text-sm px-8 py-2 rounded-sm hover:bg-rausch-active transition-colors">
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

    {{-- Rooms List --}}
    @if($rooms->count() > 0)

        {{-- Desktop Table (hidden on mobile) --}}
        <div class="hidden lg:block border border-hairline rounded-md">
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
                                        @include('admin.rooms._deactivate-button', ['room' => $room])
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile/Tablet Card Layout (hidden on desktop) --}}
        <div class="lg:hidden space-y-3">
            @foreach($rooms as $room)
                <div class="border border-hairline rounded-md p-4 hover:bg-surface-soft transition-colors">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-medium text-ink truncate">{{ $room->name }}</h3>
                            <p class="text-sm text-muted capitalize mt-0.5">{{ $room->type }} · {{ $room->capacity }} {{ Str::plural('guest', $room->capacity) }}</p>
                        </div>
                        @if($room->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-emerald-100 text-emerald-800 border-emerald-200 shrink-0">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200 shrink-0">Inactive</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-ink">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}<span class="text-muted font-normal">/night</span></p>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-ink border border-hairline rounded-sm hover:bg-surface-soft transition-colors">
                                Edit
                            </a>

                            @if($room->is_active)
                                @include('admin.rooms._deactivate-button', ['room' => $room])
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
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
