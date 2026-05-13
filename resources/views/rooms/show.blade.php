@extends('layouts.app')

@section('title', $room->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6">
        <ol class="flex items-center gap-1 text-sm text-muted">
            <li><a href="/" class="hover:text-ink transition-colors">Beranda</a></li>
            <li><span class="mx-1">/</span></li>
            <li><a href="/rooms" class="hover:text-ink transition-colors">Kamar</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-ink font-medium">{{ $room->name }}</li>
        </ol>
    </nav>

    {{-- Hero Image --}}
    <div class="w-full aspect-[21/9] rounded-md overflow-hidden bg-surface-soft mb-8">
        @if($room->image_url)
            <img
                src="{{ $room->image_url }}"
                alt="{{ $room->name }}"
                class="w-full h-full object-cover"
            >
        @else
            <div class="w-full h-full flex items-center justify-center text-muted-soft">
                <svg class="w-24 h-24" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Two-column layout: Room Info (left) + Booking Sidebar (right) --}}
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Room Information (left column ~64%) --}}
        <div class="lg:w-[64%]">
            {{-- Room Type Badge --}}
            <p class="text-muted text-xs font-medium uppercase tracking-wide mb-1">{{ $room->type }}</p>

            {{-- Room Name --}}
            <h1 class="text-3xl font-bold text-ink mb-4">{{ $room->name }}</h1>

            {{-- Rating Row --}}
            <div class="flex items-center gap-2 mb-4">
                <div class="flex items-center gap-1">
                    <svg class="w-5 h-5 text-ink" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="font-semibold text-ink">{{ number_format($rating['average'], 1) }}</span>
                </div>
                <span class="text-muted">·</span>
                <a href="#reviews" class="text-ink underline text-sm hover:text-rausch transition-colors">{{ $rating['total_reviews'] }} ulasan</a>
            </div>

            {{-- Room Meta --}}
            <div class="flex flex-wrap items-center gap-6 text-muted text-sm mb-8 pb-8 border-b border-hairline-soft">
                {{-- Capacity --}}
                <div class="flex items-center gap-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    <span>Hingga {{ $room->capacity }} tamu</span>
                </div>

                {{-- Price --}}
                <div class="flex items-center gap-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><strong class="text-ink">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}</strong> / malam</span>
                </div>

                {{-- Bed Info --}}
                <div class="flex items-center gap-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M3 7.5h18M3 16.5h18" />
                    </svg>
                    <span>
                        @if($room->type === 'suite') 2 King Bed
                        @elseif($room->type === 'deluxe') 1 King Bed
                        @elseif($room->type === 'family') 2 Queen Bed
                        @else 1 Queen Bed
                        @endif
                    </span>
                </div>
            </div>

            {{-- Description --}}
            <div class="pb-8 border-b border-hairline-soft">
                <h2 class="text-xl font-semibold text-ink mb-4">Tentang kamar ini</h2>
                <p class="text-body leading-relaxed">{{ $room->description ?? 'Belum ada deskripsi untuk kamar ini.' }}</p>
            </div>

            {{-- Amenities --}}
            <div class="py-8 border-b border-hairline-soft">
                <h2 class="text-xl font-semibold text-ink mb-6">Fasilitas kamar</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($amenities as $amenity)
                        <div class="flex items-center gap-3 py-2">
                            <svg class="w-5 h-5 text-ink flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span class="text-body text-sm">{{ $amenity['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Ratings Breakdown --}}
            <div id="reviews" class="py-8 border-b border-hairline-soft">
                <div class="flex items-center gap-2 mb-6">
                    <svg class="w-6 h-6 text-ink" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <h2 class="text-xl font-semibold text-ink">{{ number_format($rating['average'], 1) }} · {{ $rating['total_reviews'] }} ulasan</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                    @php
                        $ratingCategories = [
                            'cleanliness' => 'Kebersihan',
                            'comfort' => 'Kenyamanan',
                            'location' => 'Lokasi',
                            'service' => 'Pelayanan',
                            'value' => 'Nilai',
                        ];
                    @endphp
                    @foreach($ratingCategories as $key => $label)
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-body text-sm">{{ $label }}</span>
                            <div class="flex items-center gap-3 flex-1 max-w-[180px]">
                                <div class="flex-1 h-1 bg-hairline-soft rounded-full overflow-hidden">
                                    <div class="h-full bg-ink rounded-full" style="width: {{ ($rating[$key] / 5) * 100 }}%"></div>
                                </div>
                                <span class="text-ink text-sm font-medium w-8 text-right">{{ number_format($rating[$key], 1) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Policies --}}
            <div class="py-8">
                <h2 class="text-xl font-semibold text-ink mb-6">Kebijakan kamar</h2>
                <dl class="space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-ink flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <dt class="font-medium text-ink text-sm">Check-in / Check-out</dt>
                            <dd class="text-muted text-sm mt-0.5">Check-in mulai {{ $policies['check_in'] }} · Check-out sebelum {{ $policies['check_out'] }}</dd>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-ink flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                        <div>
                            <dt class="font-medium text-ink text-sm">Kebijakan Pembatalan</dt>
                            <dd class="text-muted text-sm mt-0.5">{{ $policies['cancellation'] }}</dd>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-ink flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <div>
                            <dt class="font-medium text-ink text-sm">Anak-anak</dt>
                            <dd class="text-muted text-sm mt-0.5">{{ $policies['children'] }}</dd>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-ink flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <div>
                            <dt class="font-medium text-ink text-sm">Hewan Peliharaan</dt>
                            <dd class="text-muted text-sm mt-0.5">{{ $policies['pets'] }}</dd>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-ink flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <div>
                            <dt class="font-medium text-ink text-sm">Merokok</dt>
                            <dd class="text-muted text-sm mt-0.5">{{ $policies['smoking'] }}</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Booking Sidebar (right column ~36%) --}}
        <div class="lg:w-[36%]">
            <div class="lg:sticky lg:top-8">
                <div class="border border-hairline rounded-md p-6 shadow-lg">
                    {{-- Price Header --}}
                    <div class="mb-6">
                        <span class="text-2xl font-bold text-ink">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}</span>
                        <span class="text-muted text-base"> / malam</span>
                    </div>

                    @auth
                        {{-- Booking Form with Alpine.js --}}
                        <div x-data="{
                            checkIn: '',
                            checkOut: '',
                            loading: false,
                            available: null,
                            totalPrice: null,
                            error: '',
                            get nights() {
                                if (!this.checkIn || !this.checkOut) return 0;
                                const start = new Date(this.checkIn);
                                const end = new Date(this.checkOut);
                                const diff = (end - start) / (1000 * 60 * 60 * 24);
                                return diff > 0 ? diff : 0;
                            },
                            get datesValid() {
                                if (!this.checkIn || !this.checkOut) return false;
                                return new Date(this.checkOut) > new Date(this.checkIn);
                            },
                            get validationMessage() {
                                if (this.checkIn && this.checkOut && !this.datesValid) {
                                    return 'Check-out must be after check-in date';
                                }
                                return '';
                            },
                            async checkAvailability() {
                                if (!this.datesValid) return;
                                this.loading = true;
                                this.error = '';
                                this.available = null;
                                this.totalPrice = null;
                                try {
                                    const response = await fetch(`/api/rooms/{{ $room->id }}/availability?check_in=${this.checkIn}&check_out=${this.checkOut}`);
                                    if (!response.ok) {
                                        const data = await response.json();
                                        this.error = data.message || 'Unable to check availability. Please try again.';
                                        return;
                                    }
                                    const data = await response.json();
                                    this.available = data.available;
                                    if (data.available) {
                                        this.totalPrice = this.nights * {{ $room->price_per_night }};
                                    }
                                } catch (e) {
                                    this.error = 'Network error. Please check your connection and try again.';
                                } finally {
                                    this.loading = false;
                                }
                            }
                        }" x-cloak>
                            {{-- Date Inputs --}}
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <div>
                                    <label for="check_in" class="block text-xs font-medium text-ink uppercase tracking-wide mb-1">Check-in</label>
                                    <input
                                        type="date"
                                        id="check_in"
                                        x-model="checkIn"
                                        :min="new Date().toISOString().split('T')[0]"
                                        class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-rausch focus:border-rausch"
                                    >
                                </div>
                                <div>
                                    <label for="check_out" class="block text-xs font-medium text-ink uppercase tracking-wide mb-1">Check-out</label>
                                    <input
                                        type="date"
                                        id="check_out"
                                        x-model="checkOut"
                                        :min="checkIn || new Date().toISOString().split('T')[0]"
                                        class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-rausch focus:border-rausch"
                                    >
                                </div>
                            </div>

                            {{-- Validation Message --}}
                            <p x-show="validationMessage" x-text="validationMessage" class="text-error text-sm mb-4"></p>

                            {{-- Check Availability Button --}}
                            <button
                                type="button"
                                @click="checkAvailability()"
                                :disabled="!datesValid || loading"
                                class="w-full bg-ink text-canvas font-medium text-sm px-6 py-2 rounded-sm hover:bg-body transition-colors disabled:opacity-50 disabled:cursor-not-allowed mb-4"
                            >
                                <span x-show="!loading">Cek Ketersediaan</span>
                                <span x-show="loading" class="flex items-center justify-center gap-1">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memeriksa...
                                </span>
                            </button>

                            {{-- Error Message --}}
                            <div x-show="error" class="bg-red-50 border border-red-200 rounded-sm p-3 mb-4">
                                <p x-text="error" class="text-error text-sm"></p>
                            </div>

                            {{-- Availability Result: Available --}}
                            <div x-show="available === true" class="border-t border-hairline-soft pt-4">
                                {{-- Price Breakdown --}}
                                <div class="flex justify-between items-center text-sm text-body mb-2">
                                    <span>Rp {{ number_format($room->price_per_night, 0, ',', '.') }} × <span x-text="nights"></span> malam</span>
                                    <span class="font-semibold text-ink">Rp <span x-text="totalPrice ? totalPrice.toLocaleString('id-ID') : '0'"></span></span>
                                </div>

                                <div class="flex justify-between items-center text-base font-bold text-ink mb-6 pt-2 border-t border-hairline-soft">
                                    <span>Total</span>
                                    <span>Rp <span x-text="totalPrice ? totalPrice.toLocaleString('id-ID') : '0'"></span></span>
                                </div>

                                {{-- Book Now Form --}}
                                <form method="POST" action="/bookings" x-data="{ bookingSubmitting: false }" @submit="bookingSubmitting = true">
                                    @csrf
                                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                                    <input type="hidden" name="check_in" :value="checkIn">
                                    <input type="hidden" name="check_out" :value="checkOut">
                                    <button
                                        type="submit"
                                        :disabled="bookingSubmitting"
                                        class="w-full bg-rausch text-on-primary font-semibold text-base px-6 py-3 rounded-sm hover:bg-rausch-active transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <span x-show="!bookingSubmitting">Pesan Sekarang</span>
                                        <span x-show="bookingSubmitting" class="flex items-center justify-center gap-2">
                                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Memproses...
                                        </span>
                                    </button>
                                </form>
                            </div>

                            {{-- Availability Result: Unavailable --}}
                            <div x-show="available === false" class="border-t border-hairline-soft pt-4">
                                <div class="bg-surface-soft rounded-sm p-3 text-center">
                                    <svg class="w-8 h-8 mx-auto text-muted-soft mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                    <p class="text-ink font-medium mb-1">Tidak tersedia</p>
                                    <p class="text-muted text-sm">Kamar ini tidak tersedia untuk tanggal yang dipilih. Silakan coba tanggal lain.</p>
                                </div>
                                <button
                                    type="button"
                                    disabled
                                    class="w-full mt-4 bg-rausch-disabled text-muted font-semibold text-base px-6 py-3 rounded-sm cursor-not-allowed"
                                >
                                    Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    @endauth

                    @guest
                        {{-- Login Prompt for Unauthenticated Users --}}
                        <div class="text-center py-4">
                            <svg class="w-10 h-10 mx-auto text-muted-soft mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <p class="text-ink font-medium mb-1">Masuk untuk memesan kamar</p>
                            <p class="text-muted text-sm mb-6">Login untuk mengecek ketersediaan dan melakukan reservasi.</p>
                            <a
                                href="{{ route('login') }}"
                                class="inline-block w-full bg-rausch text-on-primary font-semibold text-base px-6 py-3 rounded-sm hover:bg-rausch-active transition-colors text-center"
                            >
                                Masuk
                            </a>
                            <p class="text-sm text-muted mt-4">
                                Belum punya akun? <a href="{{ route('register') }}" class="text-rausch hover:text-rausch-active font-medium">Daftar di sini</a>
                            </p>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
