@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-ink">User Management</h1>
        <p class="text-sm text-muted mt-0.5">View all registered users and their booking activity</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-canvas border border-hairline rounded-md p-4 mb-5">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-muted mb-1">Search</label>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Name or email…"
                class="w-full border border-hairline rounded-sm px-3 py-2 text-sm text-ink bg-canvas focus:outline-none focus:ring-2 focus:ring-rausch/20 focus:border-rausch transition-colors"
            >
        </div>
        <div>
            <label class="block text-xs font-medium text-muted mb-1">Role</label>
            <select name="role" class="border border-hairline rounded-sm px-3 py-2 text-sm text-ink bg-canvas focus:outline-none focus:ring-2 focus:ring-rausch/20 focus:border-rausch transition-colors">
                <option value="">All Roles</option>
                <option value="user"  {{ $role === 'user'  ? 'selected' : '' }}>User</option>
                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-on-primary bg-rausch rounded-sm hover:bg-rausch-active transition-colors">
                Filter
            </button>
            @if($search || $role)
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-medium text-muted border border-hairline rounded-sm hover:bg-surface-soft transition-colors">
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Table --}}
@if($users->count() > 0)
    <div class="bg-canvas border border-hairline rounded-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-soft border-b border-hairline">
                    <tr>
                        <th class="text-left px-5 py-3.5 font-medium text-muted">User</th>
                        <th class="text-left px-5 py-3.5 font-medium text-muted">Role</th>
                        <th class="text-left px-5 py-3.5 font-medium text-muted">Bookings</th>
                        <th class="text-left px-5 py-3.5 font-medium text-muted">Joined</th>
                        <th class="text-right px-5 py-3.5 font-medium text-muted">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @foreach($users as $user)
                        <tr class="hover:bg-surface-soft transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-rausch/10 flex items-center justify-center text-rausch font-semibold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-ink">{{ $user->name }}</p>
                                        <p class="text-xs text-muted">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-rausch/10 text-rausch border-rausch/20">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700">
                                        User
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-body">{{ $user->bookings_count }}</td>
                            <td class="px-5 py-3.5 text-muted text-xs">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-medium text-rausch hover:text-rausch-active transition-colors">
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
        @include('components.pagination', ['paginator' => $users])
    </div>
@else
    <div class="bg-canvas border border-hairline rounded-md text-center py-16">
        <svg class="w-12 h-12 mx-auto text-muted-soft mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
        </svg>
        <h3 class="text-base font-semibold text-ink mb-1">No users found</h3>
        <p class="text-sm text-muted">Try adjusting your search or filters.</p>
    </div>
@endif
@endsection
