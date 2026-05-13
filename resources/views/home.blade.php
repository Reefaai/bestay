@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
{{-- Hero Section --}}
<section class="relative overflow-hidden bg-canvas">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="flex flex-col lg:flex-row lg:items-center lg:gap-12">
            {{-- Hero Text --}}
            <div class="flex-1 max-w-xl">
                <h1 class="text-4xl lg:text-5xl font-bold text-ink leading-tight tracking-tight">
                    Temukan Kenyamanan
                    <span class="text-rausch">Seperti di Rumah</span>
                </h1>
                <p class="mt-6 text-body text-lg leading-relaxed">
                    Bestay menghadirkan pengalaman menginap yang hangat dan personal. Dari kamar standar yang nyaman hingga suite mewah, kami memastikan setiap momen istirahat Anda sempurna.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center bg-rausch text-on-primary font-medium px-8 py-3.5 rounded-lg hover:bg-rausch-active transition-colors">
                        Jelajahi Kamar
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center border border-ink text-ink font-medium px-8 py-3.5 rounded-lg hover:bg-surface-soft transition-colors">
                            Daftar Sekarang
                        </a>
                    @endguest
                </div>
            </div>

            {{-- Hero Visual --}}
            <div class="hidden lg:block flex-1 mt-12 lg:mt-0">
                <div class="relative rounded-2xl overflow-hidden shadow-lg">
                    <img
                        src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop&q=80"
                        alt="Bestay Hotel Room"
                        class="w-full h-96 object-cover"
                    >
                    {{-- Floating badge --}}
                    <div class="absolute top-6 left-6 bg-canvas/95 backdrop-blur-sm rounded-full px-4 py-2 shadow-sm">
                        <span class="text-sm font-semibold text-ink">✨ Guest Favorite</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats / Trust Section --}}
<section class="bg-surface-soft border-y border-hairline-soft">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <p class="text-3xl font-bold text-rausch">500+</p>
                <p class="text-sm text-muted mt-1">Tamu Puas</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-rausch">50+</p>
                <p class="text-sm text-muted mt-1">Kamar Tersedia</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-rausch">4.8</p>
                <p class="text-sm text-muted mt-1">Rating Rata-rata</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-rausch">24/7</p>
                <p class="text-sm text-muted mt-1">Layanan Tamu</p>
            </div>
        </div>
    </div>
</section>

{{-- Why Bestay Section --}}
<section class="bg-canvas">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
        <div class="text-center mb-12">
            <h2 class="text-2xl lg:text-3xl font-bold text-ink">Mengapa Memilih Bestay?</h2>
            <p class="text-muted mt-2 max-w-2xl mx-auto">Kami berkomitmen memberikan pengalaman menginap terbaik dengan fasilitas modern dan pelayanan yang ramah.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Feature 1 --}}
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto mb-6 bg-rausch/10 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-rausch" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-ink mb-2">Kamar Nyaman</h3>
                <p class="text-muted text-sm leading-relaxed">Setiap kamar dirancang dengan perhatian detail untuk kenyamanan maksimal. Kasur premium, linen berkualitas, dan suasana yang menenangkan.</p>
            </div>

            {{-- Feature 2 --}}
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto mb-6 bg-rausch/10 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-rausch" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-ink mb-2">Booking Aman</h3>
                <p class="text-muted text-sm leading-relaxed">Proses reservasi yang mudah dan aman. Konfirmasi instan, kebijakan pembatalan fleksibel, dan transparansi harga tanpa biaya tersembunyi.</p>
            </div>

            {{-- Feature 3 --}}
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto mb-6 bg-rausch/10 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-rausch" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-ink mb-2">Pelayanan Bintang</h3>
                <p class="text-muted text-sm leading-relaxed">Tim kami siap melayani 24/7. Dari check-in hingga check-out, kami memastikan pengalaman Anda berjalan lancar dan menyenangkan.</p>
            </div>
        </div>
    </div>
</section>

{{-- Room Types Preview --}}
<section class="bg-surface-soft border-y border-hairline-soft">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
        <div class="text-center mb-12">
            <h2 class="text-2xl lg:text-3xl font-bold text-ink">Pilihan Kamar Kami</h2>
            <p class="text-muted mt-2">Berbagai tipe kamar untuk memenuhi kebutuhan Anda</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Standard --}}
            <a href="{{ route('rooms.index', ['type' => 'standard']) }}" class="group block bg-canvas rounded-xl overflow-hidden border border-hairline hover:shadow-md transition-shadow">
                <div class="h-40 bg-surface-strong overflow-hidden">
                    <img
                        src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&h=300&fit=crop&q=80"
                        alt="Standard Room"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    >
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-ink group-hover:text-rausch transition-colors">Standard</h3>
                    <p class="text-muted text-sm mt-1">Nyaman dan terjangkau untuk perjalanan singkat</p>
                </div>
            </a>

            {{-- Deluxe --}}
            <a href="{{ route('rooms.index', ['type' => 'deluxe']) }}" class="group block bg-canvas rounded-xl overflow-hidden border border-hairline hover:shadow-md transition-shadow">
                <div class="h-40 bg-surface-strong overflow-hidden">
                    <img
                        src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=400&h=300&fit=crop&q=80"
                        alt="Deluxe Room"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    >
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-ink group-hover:text-rausch transition-colors">Deluxe</h3>
                    <p class="text-muted text-sm mt-1">Ruang lebih luas dengan fasilitas premium</p>
                </div>
            </a>

            {{-- Suite --}}
            <a href="{{ route('rooms.index', ['type' => 'suite']) }}" class="group block bg-canvas rounded-xl overflow-hidden border border-hairline hover:shadow-md transition-shadow">
                <div class="h-40 bg-surface-strong overflow-hidden">
                    <img
                        src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400&h=300&fit=crop&q=80"
                        alt="Suite Room"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    >
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-ink group-hover:text-rausch transition-colors">Suite</h3>
                    <p class="text-muted text-sm mt-1">Kemewahan dan privasi untuk pengalaman eksklusif</p>
                </div>
            </a>

            {{-- Family --}}
            <a href="{{ route('rooms.index', ['type' => 'family']) }}" class="group block bg-canvas rounded-xl overflow-hidden border border-hairline hover:shadow-md transition-shadow">
                <div class="h-40 bg-surface-strong overflow-hidden">
                    <img
                        src="https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=400&h=300&fit=crop&q=80"
                        alt="Family Room"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    >
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-ink group-hover:text-rausch transition-colors">Family</h3>
                    <p class="text-muted text-sm mt-1">Luas dan lengkap untuk liburan keluarga</p>
                </div>
            </a>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 text-rausch font-medium hover:underline">
                Lihat Semua Kamar
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="bg-canvas">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
        <div class="text-center mb-12">
            <h2 class="text-2xl lg:text-3xl font-bold text-ink">Cara Booking di Bestay</h2>
            <p class="text-muted mt-2">Tiga langkah mudah untuk menginap di Bestay</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Step 1 --}}
            <div class="relative text-center">
                <div class="w-12 h-12 mx-auto mb-6 bg-rausch text-on-primary rounded-full flex items-center justify-center font-bold text-lg">
                    1
                </div>
                <h3 class="text-lg font-semibold text-ink mb-2">Pilih Kamar</h3>
                <p class="text-muted text-sm leading-relaxed">Jelajahi berbagai pilihan kamar dan temukan yang sesuai dengan kebutuhan dan budget Anda.</p>
            </div>

            {{-- Step 2 --}}
            <div class="relative text-center">
                <div class="w-12 h-12 mx-auto mb-6 bg-rausch text-on-primary rounded-full flex items-center justify-center font-bold text-lg">
                    2
                </div>
                <h3 class="text-lg font-semibold text-ink mb-2">Tentukan Tanggal</h3>
                <p class="text-muted text-sm leading-relaxed">Pilih tanggal check-in dan check-out. Sistem kami akan memastikan ketersediaan kamar secara real-time.</p>
            </div>

            {{-- Step 3 --}}
            <div class="relative text-center">
                <div class="w-12 h-12 mx-auto mb-6 bg-rausch text-on-primary rounded-full flex items-center justify-center font-bold text-lg">
                    3
                </div>
                <h3 class="text-lg font-semibold text-ink mb-2">Konfirmasi & Nikmati</h3>
                <p class="text-muted text-sm leading-relaxed">Booking langsung terkonfirmasi. Tinggal datang dan nikmati pengalaman menginap yang tak terlupakan.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="bg-[#1a1a1c] dark:bg-[#0a0a0b]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20 text-center">
        <h2 class="text-2xl lg:text-3xl font-bold text-white">Siap Untuk Menginap?</h2>
        <p class="text-white/70 mt-3 max-w-xl mx-auto">Temukan kamar impian Anda dan pesan sekarang. Pengalaman menginap terbaik menanti Anda di Bestay.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center bg-rausch text-white font-medium px-8 py-3.5 rounded-lg hover:bg-rausch-active transition-colors">
                Cari Kamar Sekarang
            </a>
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center border border-white/30 text-white font-medium px-8 py-3.5 rounded-lg hover:bg-white/10 transition-colors">
                    Buat Akun Gratis
                </a>
            @endguest
        </div>
    </div>
</section>
@endsection
