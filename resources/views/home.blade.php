@extends('layouts.app')

@section('title', 'Welcome')

@push('styles')
<style>
/* Hero parallax bg */
.hero-bg {
    background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&h=900&fit=crop&q=85');
    background-size: cover;
    background-position: center;
}

/* Floating animation */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50%       { transform: translateY(-10px); }
}
.animate-float { animation: float 4s ease-in-out infinite; }

/* Slide up on scroll */
.reveal {
    opacity: 0;
    transform: translateY(32px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.reveal.visible {
    opacity: 1;
    transform: translateY(0);
}

/* Stagger children */
.reveal-stagger > * {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.5s ease, transform 0.5s ease;
}
.reveal-stagger.visible > *:nth-child(1) { opacity:1; transform:translateY(0); transition-delay: 0.05s; }
.reveal-stagger.visible > *:nth-child(2) { opacity:1; transform:translateY(0); transition-delay: 0.15s; }
.reveal-stagger.visible > *:nth-child(3) { opacity:1; transform:translateY(0); transition-delay: 0.25s; }
.reveal-stagger.visible > *:nth-child(4) { opacity:1; transform:translateY(0); transition-delay: 0.35s; }
.reveal-stagger.visible > *:nth-child(5) { opacity:1; transform:translateY(0); transition-delay: 0.45s; }
.reveal-stagger.visible > *:nth-child(6) { opacity:1; transform:translateY(0); transition-delay: 0.55s; }

/* Counter animation */
.counter { transition: all 0.1s; }

/* Gradient text */
.gradient-text {
    background: linear-gradient(135deg, #ff385c 0%, #ff6b35 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Room card hover */
.room-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.room-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
}

/* Shimmer on stat cards */
@keyframes shimmer {
    0%   { background-position: -200% center; }
    100% { background-position: 200% center; }
}
.stat-shimmer {
    background: linear-gradient(90deg, transparent 0%, rgba(255,56,92,0.08) 50%, transparent 100%);
    background-size: 200% auto;
    animation: shimmer 3s linear infinite;
}
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════
     HERO SECTION
═══════════════════════════════════════════════════════ --}}
<section class="relative flex flex-col hero-bg" style="height: calc(100vh - 80px); min-height: 600px;">
    {{-- Dark overlay --}}
    <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-black/50 to-black/30"></div>

    {{-- Decorative blobs --}}
    <div class="absolute top-20 right-10 w-72 h-72 bg-rausch/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-20 left-10 w-96 h-96 bg-rausch/10 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Main content — grows to fill space --}}
    <div class="relative z-10 flex-1 flex items-center max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-12 pb-4">
        <div class="max-w-3xl">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-1.5 mb-6 animate-fade-in-up">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-white/90 text-sm font-medium">Kamar tersedia sekarang</span>
            </div>

            {{-- Headline --}}
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-white leading-[1.1] tracking-tight animate-fade-in-up" style="animation-delay:0.1s">
                Menginap dengan
                <span class="block gradient-text">Penuh Gaya</span>
            </h1>

            <p class="mt-6 text-white/75 text-lg sm:text-xl leading-relaxed max-w-xl animate-fade-in-up" style="animation-delay:0.2s">
                Temukan kamar impian Anda — dari standard yang nyaman hingga suite eksklusif. Booking mudah, harga transparan, pengalaman tak terlupakan.
            </p>

            {{-- CTA Buttons --}}
            <div class="mt-10 flex flex-wrap gap-4 animate-fade-in-up" style="animation-delay:0.3s">
                <a href="{{ route('rooms.index') }}"
                   class="group inline-flex items-center gap-2 bg-rausch text-white font-semibold px-8 py-4 rounded-xl hover:bg-rausch-active transition-all duration-200 shadow-lg shadow-rausch/30 hover:shadow-rausch/50 hover:scale-105">
                    Jelajahi Kamar
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
                @guest
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/30 text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/20 transition-all duration-200">
                    Daftar Gratis
                </a>
                @endguest
            </div>

            {{-- Quick stats row --}}
            <div class="mt-14 flex flex-wrap gap-8 animate-fade-in-up" style="animation-delay:0.4s">
                <div>
                    <p class="text-3xl font-bold text-white">500+</p>
                    <p class="text-white/60 text-sm mt-0.5">Tamu Puas</p>
                </div>
                <div class="w-px bg-white/20 self-stretch"></div>
                <div>
                    <p class="text-3xl font-bold text-white">4.8★</p>
                    <p class="text-white/60 text-sm mt-0.5">Rating</p>
                </div>
                <div class="w-px bg-white/20 self-stretch"></div>
                <div>
                    <p class="text-3xl font-bold text-white">24/7</p>
                    <p class="text-white/60 text-sm mt-0.5">Layanan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator — absolute bottom center, simple chevron --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-1 opacity-60 hover:opacity-100 transition-opacity">
        <span class="text-white text-[10px] font-medium tracking-[0.2em] uppercase">Scroll</span>
        <svg class="w-4 h-4 text-white animate-bounce" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
        </svg>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     FEATURED ROOMS
═══════════════════════════════════════════════════════ --}}
<section class="bg-canvas py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-12 reveal">
            <div>
                <p class="text-rausch text-sm font-semibold uppercase tracking-widest mb-2">Pilihan Terbaik</p>
                <h2 class="text-3xl lg:text-4xl font-bold text-ink">Kamar Unggulan Kami</h2>
                <p class="text-muted mt-2 max-w-lg">Dipilih khusus untuk pengalaman menginap yang tak terlupakan</p>
            </div>
            <a href="{{ route('rooms.index') }}"
               class="group inline-flex items-center gap-2 text-rausch font-semibold hover:gap-3 transition-all flex-shrink-0">
                Lihat Semua
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

        {{-- Room cards grid --}}
        @if($featuredRooms->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 reveal-stagger">
            @foreach($featuredRooms as $room)
            <a href="{{ route('rooms.show', $room) }}"
               class="room-card group block bg-canvas rounded-2xl overflow-hidden border border-hairline hover:border-rausch/30">
                {{-- Image --}}
                <div class="relative aspect-[4/3] overflow-hidden bg-surface-soft">
                    @if($room->image_url)
                        <img src="{{ $room->image_url }}"
                             alt="{{ $room->name }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                             loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-surface-strong">
                            <svg class="w-16 h-16 text-muted-soft" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Type badge --}}
                    <div class="absolute top-3 left-3">
                        <span class="bg-black/50 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide">
                            {{ $room->type }}
                        </span>
                    </div>

                    {{-- Favorite badge --}}
                    <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4 text-rausch" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-5">
                    <h3 class="font-bold text-ink text-lg leading-tight group-hover:text-rausch transition-colors">
                        {{ $room->name }}
                    </h3>

                    <div class="flex items-center gap-4 mt-2 text-sm text-muted">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                            {{ $room->capacity }} tamu
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            4.8
                        </span>
                    </div>

                    @if($room->description)
                    <p class="text-muted text-sm mt-2 line-clamp-2 leading-relaxed">{{ $room->description }}</p>
                    @endif

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-hairline">
                        <div>
                            <span class="text-ink font-bold text-xl">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}</span>
                            <span class="text-muted text-sm"> / malam</span>
                        </div>
                        <span class="text-rausch text-sm font-semibold group-hover:underline">Lihat →</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-16 reveal">
            <p class="text-muted">Belum ada kamar tersedia. Cek kembali nanti.</p>
        </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     STATS SECTION
═══════════════════════════════════════════════════════ --}}
<section class="bg-surface-soft border-y border-hairline py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 reveal-stagger">
            @foreach([
                ['value' => '500', 'suffix' => '+', 'label' => 'Tamu Puas', 'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
                ['value' => '50', 'suffix' => '+', 'label' => 'Kamar Tersedia', 'icon' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21'],
                ['value' => '4.8', 'suffix' => '★', 'label' => 'Rating Rata-rata', 'icon' => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z'],
                ['value' => '24', 'suffix' => '/7', 'label' => 'Layanan Tamu', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
            ] as $stat)
            <div class="relative bg-canvas rounded-2xl p-6 border border-hairline overflow-hidden group hover:border-rausch/30 transition-colors">
                <div class="stat-shimmer absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="w-10 h-10 bg-rausch/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-rausch" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                    <p class="text-3xl lg:text-4xl font-bold text-ink">
                        {{ $stat['value'] }}<span class="text-rausch">{{ $stat['suffix'] }}</span>
                    </p>
                    <p class="text-muted text-sm mt-1">{{ $stat['label'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     ROOM TYPE CATEGORIES
═══════════════════════════════════════════════════════ --}}
<section class="bg-canvas py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14 reveal">
            <p class="text-rausch text-sm font-semibold uppercase tracking-widest mb-2">Kategori</p>
            <h2 class="text-3xl lg:text-4xl font-bold text-ink">Tipe Kamar</h2>
            <p class="text-muted mt-3 max-w-xl mx-auto">Pilih tipe kamar yang sesuai dengan kebutuhan dan kenyamanan Anda</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 reveal-stagger">
            @foreach([
                ['type' => 'standard', 'label' => 'Standard', 'desc' => 'Nyaman & terjangkau', 'img' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=500&h=400&fit=crop&q=80', 'color' => 'from-blue-600/80'],
                ['type' => 'deluxe',   'label' => 'Deluxe',   'desc' => 'Fasilitas premium',   'img' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=500&h=400&fit=crop&q=80', 'color' => 'from-purple-600/80'],
                ['type' => 'suite',    'label' => 'Suite',    'desc' => 'Kemewahan eksklusif', 'img' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=500&h=400&fit=crop&q=80', 'color' => 'from-amber-600/80'],
                ['type' => 'family',   'label' => 'Family',   'desc' => 'Luas untuk keluarga', 'img' => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=500&h=400&fit=crop&q=80', 'color' => 'from-emerald-600/80'],
            ] as $cat)
            <a href="{{ route('rooms.index', ['type' => $cat['type']]) }}"
               class="group relative rounded-2xl overflow-hidden aspect-[3/4] block">
                <img src="{{ $cat['img'] }}" alt="{{ $cat['label'] }}"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t {{ $cat['color'] }} to-transparent"></div>
                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors"></div>
                <div class="absolute bottom-0 left-0 right-0 p-5">
                    <h3 class="text-white font-bold text-xl">{{ $cat['label'] }}</h3>
                    <p class="text-white/80 text-sm mt-0.5">{{ $cat['desc'] }}</p>
                    <span class="inline-flex items-center gap-1 mt-3 text-white text-xs font-semibold bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full group-hover:bg-white/30 transition-colors">
                        Lihat kamar
                        <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     WHY BESTAY — FEATURES
═══════════════════════════════════════════════════════ --}}
<section class="bg-surface-soft border-y border-hairline py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            {{-- Left: image collage --}}
            <div class="relative reveal">
                <div class="grid grid-cols-2 gap-4">
                    <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&h=500&fit=crop&q=80"
                         alt="Room" class="rounded-2xl w-full h-64 object-cover shadow-lg">
                    <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400&h=300&fit=crop&q=80"
                         alt="Suite" class="rounded-2xl w-full h-40 object-cover shadow-lg mt-8">
                    <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=400&h=300&fit=crop&q=80"
                         alt="Deluxe" class="rounded-2xl w-full h-40 object-cover shadow-lg -mt-8">
                    <img src="https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=400&h=500&fit=crop&q=80"
                         alt="Family" class="rounded-2xl w-full h-64 object-cover shadow-lg">
                </div>
                {{-- Floating card --}}
                <div class="absolute -bottom-6 -right-4 bg-canvas rounded-2xl shadow-xl p-4 border border-hairline animate-float">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-ink font-semibold text-sm">Booking Dikonfirmasi</p>
                            <p class="text-muted text-xs">Kamar Deluxe · 3 malam</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: features list --}}
            <div class="reveal">
                <p class="text-rausch text-sm font-semibold uppercase tracking-widest mb-3">Mengapa Bestay?</p>
                <h2 class="text-3xl lg:text-4xl font-bold text-ink leading-tight">
                    Pengalaman Menginap<br>yang Berbeda
                </h2>
                <p class="text-muted mt-4 leading-relaxed">
                    Kami percaya setiap tamu berhak mendapatkan yang terbaik. Dari fasilitas hingga pelayanan, semua dirancang untuk kenyamanan Anda.
                </p>

                <div class="mt-8 space-y-5">
                    @foreach([
                        ['icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25', 'title' => 'Kamar Berkualitas', 'desc' => 'Setiap kamar dirancang dengan detail untuk kenyamanan maksimal. Kasur premium, linen berkualitas, suasana menenangkan.'],
                        ['icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z', 'title' => 'Booking 100% Aman', 'desc' => 'Proses reservasi mudah dan aman. Konfirmasi instan, kebijakan pembatalan fleksibel, harga transparan.'],
                        ['icon' => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z', 'title' => 'Pelayanan Bintang 5', 'desc' => 'Tim kami siap melayani 24/7. Dari check-in hingga check-out, pengalaman Anda adalah prioritas kami.'],
                        ['icon' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z', 'title' => 'Harga Terbaik', 'desc' => 'Tidak ada biaya tersembunyi. Harga yang Anda lihat adalah harga yang Anda bayar.'],
                    ] as $i => $feat)
                    <div class="flex gap-4 group">
                        <div class="w-11 h-11 bg-rausch/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-rausch group-hover:scale-110 transition-all duration-200">
                            <svg class="w-5 h-5 text-rausch group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feat['icon'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-ink">{{ $feat['title'] }}</h3>
                            <p class="text-muted text-sm mt-0.5 leading-relaxed">{{ $feat['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     HOW IT WORKS
═══════════════════════════════════════════════════════ --}}
<section class="bg-canvas py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14 reveal">
            <p class="text-rausch text-sm font-semibold uppercase tracking-widest mb-2">Mudah & Cepat</p>
            <h2 class="text-3xl lg:text-4xl font-bold text-ink">Cara Booking di Bestay</h2>
            <p class="text-muted mt-3">Tiga langkah mudah untuk menginap impian Anda</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative reveal-stagger">
            {{-- Connector line (desktop) --}}
            <div class="hidden md:block absolute top-10 left-1/3 right-1/3 h-px bg-gradient-to-r from-rausch/30 via-rausch to-rausch/30 z-0"></div>

            @foreach([
                ['num' => '01', 'title' => 'Pilih Kamar', 'desc' => 'Jelajahi berbagai pilihan kamar. Filter berdasarkan tipe, harga, dan kapasitas sesuai kebutuhan Anda.', 'icon' => 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z'],
                ['num' => '02', 'title' => 'Tentukan Tanggal', 'desc' => 'Pilih tanggal check-in dan check-out. Sistem kami memastikan ketersediaan kamar secara real-time.', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
                ['num' => '03', 'title' => 'Konfirmasi & Nikmati', 'desc' => 'Booking langsung terkonfirmasi setelah pembayaran. Tinggal datang dan nikmati pengalaman menginap terbaik.', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ] as $step)
            <div class="relative z-10 text-center group">
                <div class="w-20 h-20 mx-auto mb-6 bg-canvas border-2 border-rausch/20 group-hover:border-rausch rounded-2xl flex flex-col items-center justify-center shadow-sm group-hover:shadow-md group-hover:scale-105 transition-all duration-300">
                    <span class="text-rausch text-xs font-bold">{{ $step['num'] }}</span>
                    <svg class="w-7 h-7 text-rausch mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $step['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-ink mb-2">{{ $step['title'] }}</h3>
                <p class="text-muted text-sm leading-relaxed max-w-xs mx-auto">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12 reveal">
            <a href="{{ route('rooms.index') }}"
               class="group inline-flex items-center gap-2 bg-rausch text-white font-semibold px-8 py-4 rounded-xl hover:bg-rausch-active transition-all duration-200 shadow-lg shadow-rausch/20 hover:shadow-rausch/40 hover:scale-105">
                Mulai Booking Sekarang
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     CTA SECTION
═══════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-[#0f0f10]">
    {{-- Background image --}}
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1600&h=600&fit=crop&q=80"
             alt="" class="w-full h-full object-cover opacity-20">
        <div class="absolute inset-0 bg-gradient-to-r from-[#0f0f10] via-[#0f0f10]/80 to-[#0f0f10]/60"></div>
    </div>

    {{-- Decorative blobs --}}
    <div class="absolute top-0 right-0 w-96 h-96 bg-rausch/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-rausch/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="max-w-2xl reveal">
            <p class="text-rausch text-sm font-semibold uppercase tracking-widest mb-4">Jangan Tunda Lagi</p>
            <h2 class="text-4xl lg:text-5xl font-bold text-white leading-tight">
                Kamar Impian Anda<br>
                <span class="gradient-text">Menanti di Sini</span>
            </h2>
            <p class="text-white/60 mt-5 text-lg leading-relaxed">
                Bergabunglah dengan ratusan tamu yang sudah merasakan kenyamanan menginap di Bestay. Booking sekarang dan dapatkan pengalaman terbaik.
            </p>
            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('rooms.index') }}"
                   class="group inline-flex items-center gap-2 bg-rausch text-white font-semibold px-8 py-4 rounded-xl hover:bg-rausch-active transition-all duration-200 shadow-lg shadow-rausch/30 hover:scale-105">
                    Cari Kamar Sekarang
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
                @guest
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 border border-white/20 text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/10 transition-all duration-200">
                    Buat Akun Gratis
                </a>
                @endguest
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
// Intersection Observer for scroll reveal
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.reveal, .reveal-stagger').forEach(el => observer.observe(el));
</script>
@endpush
