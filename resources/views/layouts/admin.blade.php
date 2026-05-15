<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') — Bestay Admin</title>

    {{-- Theme init: prevent flash of wrong theme --}}
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = stored || (prefersDark ? 'dark' : 'light');
                if (theme === 'dark') document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-surface-soft text-ink font-sans antialiased min-h-screen">

<div
    x-data="{
        sidebarOpen: false,
        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
            try { localStorage.setItem('theme', this.theme); } catch (e) {}
        }
    }"
    class="flex h-screen overflow-hidden"
>
    {{-- ── Sidebar ── --}}
    {{-- Mobile overlay --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        @click="sidebarOpen = false"
        class="fixed inset-0 z-20 bg-ink/50 lg:hidden"
    ></div>

    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-64 bg-canvas border-r border-hairline flex flex-col transition-transform duration-200 lg:translate-x-0 lg:static lg:z-auto"
    >
        {{-- Brand --}}
        <div class="flex items-center gap-2 px-6 h-16 border-b border-hairline flex-shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="text-rausch font-bold text-xl tracking-tight">
                Bestay
            </a>
            <span class="text-xs font-medium text-muted bg-surface-soft border border-hairline px-2 py-0.5 rounded-full">Admin</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard',       'label' => 'Dashboard',    'icon' => 'home'],
                    ['route' => 'admin.bookings.index',  'label' => 'Bookings',     'icon' => 'calendar'],
                    ['route' => 'admin.payments.index',  'label' => 'Payments',     'icon' => 'credit-card'],
                    ['route' => 'admin.rooms.index',     'label' => 'Rooms',        'icon' => 'building'],
                    ['route' => 'admin.users.index',     'label' => 'Users',        'icon' => 'users'],
                    ['route' => 'admin.bookings.conflicts', 'label' => 'Conflicts', 'icon' => 'warning'],
                ];
            @endphp

            @foreach($navItems as $item)
                @php $active = request()->routeIs($item['route'] . '*'); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-sm text-sm font-medium transition-colors
                        {{ $active
                            ? 'bg-rausch/10 text-rausch'
                            : 'text-body hover:bg-surface-soft hover:text-ink' }}"
                >
                    {{-- Icons --}}
                    @if($item['icon'] === 'home')
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    @elseif($item['icon'] === 'calendar')
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    @elseif($item['icon'] === 'credit-card')
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                    @elseif($item['icon'] === 'building')
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    @elseif($item['icon'] === 'users')
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    @elseif($item['icon'] === 'warning')
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    @endif
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Sidebar footer --}}
        <div class="px-3 py-4 border-t border-hairline flex-shrink-0 space-y-1">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-sm text-sm font-medium text-body hover:bg-surface-soft hover:text-ink transition-colors">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
                View Site
            </a>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-sm text-sm font-medium text-body hover:bg-surface-soft hover:text-ink transition-colors cursor-pointer">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main area ── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Top bar --}}
        <header class="bg-canvas border-b border-hairline h-16 flex items-center justify-between px-4 lg:px-6 flex-shrink-0">
            {{-- Mobile hamburger --}}
            <button
                @click="sidebarOpen = !sidebarOpen"
                class="lg:hidden inline-flex items-center justify-center p-2 rounded-sm text-ink hover:bg-surface-soft transition-colors"
                aria-label="Toggle sidebar"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            {{-- Page title (desktop) --}}
            <h1 class="hidden lg:block text-base font-semibold text-ink">@yield('title', 'Dashboard')</h1>

            {{-- Right side --}}
            <div class="flex items-center gap-3 ml-auto">
                {{-- Theme toggle --}}
                <button
                    @click="toggleTheme()"
                    type="button"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-hairline text-ink hover:bg-surface-soft transition-colors"
                    :aria-label="theme === 'dark' ? 'Light mode' : 'Dark mode'"
                >
                    <svg x-show="theme === 'dark'" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg x-show="theme === 'light'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>

                {{-- User info --}}
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-rausch/10 flex items-center justify-center text-rausch font-semibold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-ink">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
