{{-- Navigation component --}}
<nav
    class="bg-canvas border-b border-hairline sticky top-0 z-50"
    x-data="{
        mobileMenuOpen: false,
        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
            try { localStorage.setItem('theme', this.theme); } catch (e) {}
        }
    }"
>
    <div class="max-w-7xl mx-auto px-4" style="height: 80px;">
        <div class="flex items-center justify-between h-full">
            {{-- Logo / Brand --}}
            <a href="/" class="text-rausch font-bold text-xl tracking-tight">
                Bestay
            </a>

            {{-- Desktop Navigation (hidden below md breakpoint ~744px) --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('rooms.index') }}" class="text-ink hover:text-rausch text-sm font-medium transition-colors">
                    Rooms
                </a>

                @auth
                    <a href="/dashboard" class="text-ink hover:text-rausch text-sm font-medium transition-colors">
                        Dashboard
                    </a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-ink hover:text-rausch text-sm font-medium transition-colors">
                            Admin
                        </a>
                    @endif

                    {{-- User Avatar Dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button
                            @click="open = !open"
                            @click.outside="open = false"
                            class="flex items-center gap-2 rounded-full border border-hairline px-1 py-1 hover:shadow-sm transition-shadow cursor-pointer"
                        >
                            <div class="w-8 h-8 rounded-full bg-rausch/10 flex items-center justify-center text-rausch font-semibold text-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-canvas border border-hairline rounded-md shadow-lg py-1 z-50"
                        >
                            <div class="px-4 py-2 border-b border-hairline">
                                <p class="text-sm font-medium text-ink truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-muted truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="/dashboard" class="block px-4 py-2 text-sm text-ink hover:bg-surface-soft transition-colors">
                                My Bookings
                            </a>

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-ink hover:bg-surface-soft transition-colors">
                                    Admin Panel
                                </a>
                            @endif

                            <div class="border-t border-hairline mt-1 pt-1">
                                <form method="POST" action="/logout">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-ink hover:bg-surface-soft transition-colors cursor-pointer">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="/login" class="text-ink hover:text-rausch text-sm font-medium transition-colors">
                        Login
                    </a>
                    <a href="/register" class="text-ink hover:text-rausch text-sm font-medium transition-colors">
                        Register
                    </a>
                @endauth

                {{-- Theme Toggle (desktop) --}}
                <button
                    @click="toggleTheme()"
                    type="button"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-hairline text-ink hover:bg-surface-soft transition-colors cursor-pointer"
                    :aria-label="theme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode'"
                    :title="theme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode'"
                >
                    {{-- Sun icon (shown in dark mode, click → light) --}}
                    <svg x-show="theme === 'dark'" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    {{-- Moon icon (shown in light mode, click → dark) --}}
                    <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>
            </div>

            {{-- Mobile controls --}}
            <div class="md:hidden flex items-center gap-2">
                {{-- Theme Toggle (mobile) --}}
                <button
                    @click="toggleTheme()"
                    type="button"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-hairline text-ink hover:bg-surface-soft transition-colors"
                    :aria-label="theme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode'"
                >
                    <svg x-show="theme === 'dark'" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>

                {{-- Mobile Hamburger Button --}}
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="inline-flex items-center justify-center p-2 rounded-sm text-ink hover:text-rausch hover:bg-surface-soft transition-colors focus:outline-none"
                    :aria-expanded="mobileMenuOpen.toString()"
                    aria-label="Toggle navigation menu"
                >
                    <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu Panel --}}
    <div
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="md:hidden border-t border-hairline-soft bg-canvas"
    >
        <div class="px-4 py-6 space-y-1">
            <a href="{{ route('rooms.index') }}" class="block px-3 py-2 rounded-sm text-ink hover:text-rausch hover:bg-surface-soft text-sm font-medium transition-colors">
                Rooms
            </a>

            @auth
                <div class="flex items-center gap-3 px-3 py-3 border-b border-hairline-soft mb-2">
                    <div class="w-8 h-8 rounded-full bg-rausch/10 flex items-center justify-center text-rausch font-semibold text-sm flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-ink truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-muted truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <a href="/dashboard" class="block px-3 py-2 rounded-sm text-ink hover:text-rausch hover:bg-surface-soft text-sm font-medium transition-colors">
                    My Bookings
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-sm text-ink hover:text-rausch hover:bg-surface-soft text-sm font-medium transition-colors">
                        Admin Panel
                    </a>
                @endif

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 rounded-sm text-ink hover:text-rausch hover:bg-surface-soft text-sm font-medium transition-colors cursor-pointer">
                        Logout
                    </button>
                </form>
            @else
                <a href="/login" class="block px-3 py-2 rounded-sm text-ink hover:text-rausch hover:bg-surface-soft text-sm font-medium transition-colors">
                    Login
                </a>
                <a href="/register" class="block px-3 py-2 rounded-sm text-ink hover:text-rausch hover:bg-surface-soft text-sm font-medium transition-colors">
                    Register
                </a>
            @endauth
        </div>
    </div>
</nav>
