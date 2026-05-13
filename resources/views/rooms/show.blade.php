@extends('layouts.app')

@section('title', $room->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6 md:mb-8 animate-fade-in-up" style="animation-delay: 0ms">
        <ol class="flex items-center gap-1 text-sm text-muted">
            <li><a href="/" class="hover:text-ink transition-colors duration-200 inline-flex items-center min-h-[44px] md:min-h-0">Beranda</a></li>
            <li><span class="mx-1">›</span></li>
            <li><a href="/rooms" class="hover:text-ink transition-colors duration-200 inline-flex items-center min-h-[44px] md:min-h-0">Kamar</a></li>
            <li><span class="mx-1">›</span></li>
            <li class="text-ink font-medium">{{ $room->name }}</li>
        </ol>
    </nav>

    {{-- Hero Image --}}
    <div class="w-full aspect-[4/3] md:aspect-[16/9] rounded-xl overflow-hidden mb-6 md:mb-8 animate-fade-in-up" style="animation-delay: 100ms">
        @if($room->image_url)
            <img
                src="{{ $room->image_url }}"
                alt="{{ $room->name }}"
                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
            >
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300">
                <svg class="w-24 h-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Two-column layout: Room Info (left) + Booking Sidebar (right) --}}
    <div class="flex flex-col md:flex-row gap-6 md:gap-8 pb-[80px] md:pb-0">
        {{-- Room Information (left column ~64%) --}}
        <div class="md:w-[64%]">
            {{-- Room Header (Badge + Name + Rating + Meta) --}}
            <div class="animate-fade-in-up" style="animation-delay: 200ms">
            {{-- Room Type Badge --}}
            @php
                $badgeColors = [
                    'suite' => 'bg-yellow-100 text-yellow-800',
                    'deluxe' => 'bg-blue-100 text-blue-800',
                    'family' => 'bg-green-100 text-green-800',
                    'standard' => 'bg-gray-100 text-gray-800',
                ];
                $badgeColor = $badgeColors[$room->type] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium uppercase tracking-wide mb-2 {{ $badgeColor }}">{{ $room->type }}</span>

            {{-- Room Name --}}
            <h1 class="text-[22px] md:text-[28px] font-bold text-ink mb-4">{{ $room->name }}</h1>

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
            <div class="flex flex-wrap items-center gap-6 text-muted text-sm pb-6 md:pb-8 border-b border-hairline-soft">
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
            </div>{{-- End Room Header --}}

            {{-- Room Highlights --}}
            @if(!empty($highlights))
            <div class="pt-6 md:pt-8 pb-6 md:pb-8 border-b border-hairline-soft animate-fade-in-up" style="animation-delay: 300ms">
                <h2 class="text-[20px] font-semibold text-ink mb-6">Keunggulan kamar</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($highlights as $highlight)
                    <div class="flex items-start gap-4 p-4 rounded-lg bg-surface-soft">
                        {{-- Highlight Icon --}}
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-canvas flex items-center justify-center text-ink">
                            @switch($highlight['icon'])
                                @case('bed')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>
                                @break
                                @case('size')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                                    </svg>
                                @break
                                @case('view')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                @break
                                @case('bath')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                    </svg>
                                @break
                                @case('living')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
                                    </svg>
                                @break
                                @case('kids')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                                    </svg>
                                @break
                                @case('kitchen')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1.001A3.75 3.75 0 0012 18z" />
                                    </svg>
                                @break
                                @default
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                                    </svg>
                            @endswitch
                        </div>
                        {{-- Highlight Content --}}
                        <div class="min-w-0">
                            <p class="font-bold text-ink text-sm">{{ Str::limit($highlight['title'], 30) }}</p>
                            <p class="text-muted text-sm mt-0.5">{{ Str::limit($highlight['description'], 80) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Description --}}
            <div class="pt-6 md:pt-8 pb-6 md:pb-8 border-b border-hairline-soft animate-fade-in-up" style="animation-delay: 400ms">
                <h2 class="text-[20px] font-semibold text-ink mb-6">Tentang kamar ini</h2>
                <p class="text-body leading-[1.6]">{{ $room->description ?? 'Belum ada deskripsi untuk kamar ini.' }}</p>
            </div>

            {{-- Amenities --}}
            <div class="py-6 md:py-8 border-b border-hairline-soft animate-fade-in-up" style="animation-delay: 500ms" x-data="{ expanded: false }">
                <h2 class="text-[20px] font-semibold text-ink mb-6">Fasilitas kamar</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($amenities as $index => $amenity)
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-surface-soft"
                            @if(count($amenities) > 6 && $index >= 6)
                                x-show="expanded"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2"
                            @endif
                        >
                            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-canvas flex items-center justify-center text-ink">
                                @switch($amenity['icon'])
                                    @case('wifi')
                                        {{-- WiFi icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
                                        </svg>
                                    @break
                                    @case('tv')
                                        {{-- TV icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                                        </svg>
                                    @break
                                    @case('ac')
                                        {{-- Snowflake / AC icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m0-18l-3 3m3-3l3 3m-3 15l-3-3m3 3l3-3M3 12h18M3 12l3-3m-3 3l3 3m15-3l-3-3m3 3l-3 3" />
                                        </svg>
                                    @break
                                    @case('bath')
                                        {{-- Bathtub / Bathroom icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5h18m-18 0v3a3 3 0 003 3h12a3 3 0 003-3v-3m-18 0V6.75A2.25 2.25 0 015.25 4.5h.5a.75.75 0 01.75.75v8.25" />
                                        </svg>
                                    @break
                                    @case('water')
                                        {{-- Hot water / droplet icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                        </svg>
                                    @break
                                    @case('coffee')
                                        {{-- Coffee/Tea maker icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                    @break
                                    @case('breakfast')
                                        {{-- Breakfast / food icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75l-1.5.75a3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0L3 16.5m15-3.379a48.474 48.474 0 00-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 013 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 016 13.12M12.265 3.11a.375.375 0 11-.53 0L12 2.845l.265.265z" />
                                        </svg>
                                    @break
                                    @case('minibar')
                                        {{-- Minibar / glass icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5V18M15 7.5V18M3 16.811V8.69c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 010 1.954l-7.108 4.061A1.125 1.125 0 013 16.811z" />
                                        </svg>
                                    @break
                                    @case('safe')
                                        {{-- Safe / lock icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    @break
                                    @case('desk')
                                        {{-- Desk / workspace icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                        </svg>
                                    @break
                                    @case('living')
                                        {{-- Living room / sofa icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
                                        </svg>
                                    @break
                                    @case('butler')
                                        {{-- Butler / bell service icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                        </svg>
                                    @break
                                    @case('view')
                                        {{-- View / panorama icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    @break
                                    @case('kids')
                                        {{-- Kids / play area icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                                        </svg>
                                    @break
                                    @case('bed')
                                        {{-- Bed icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                    @break
                                    @case('kitchen')
                                        {{-- Kitchen / flame icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1.001A3.75 3.75 0 0012 18z" />
                                        </svg>
                                    @break
                                    @default
                                        {{-- Default checkmark icon --}}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                @endswitch
                            </div>
                            <span class="text-body text-sm">{{ $amenity['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                @if(count($amenities) > 6)
                    <button
                        @click="expanded = !expanded"
                        :aria-expanded="expanded.toString()"
                        class="mt-4 px-4 py-2 min-h-[44px] text-sm font-medium text-ink border border-ink rounded-lg hover:bg-ink hover:text-white transition-colors duration-200"
                    >
                        <span x-text="expanded ? 'Sembunyikan' : 'Tampilkan semua fasilitas'"></span>
                    </button>
                @endif
            </div>

            {{-- Map Section --}}
            @if(isset($mapData))
            <div class="py-6 md:py-8 border-b border-hairline-soft animate-fade-in-up" style="animation-delay: 600ms">
                <h2 class="text-[20px] font-semibold text-ink mb-6">Lokasi</h2>
                <p class="text-body text-sm mb-4">{{ Str::limit($mapData['area'], 100) }}</p>
                <div id="map-container" class="relative h-[200px] min-[744px]:h-[300px] rounded-lg overflow-hidden">
                    {{-- Interactive map (shown by default, hidden on tile error) --}}
                    <div id="leaflet-map" class="w-full h-full"></div>
                    {{-- Fallback placeholder (hidden by default, shown on tile error) --}}
                    <div id="map-fallback" class="hidden absolute inset-0 w-full h-full bg-gray-200 flex items-center justify-center rounded-lg">
                        <div class="text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <p class="text-gray-500 text-sm font-medium">Peta tidak tersedia</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Ratings Breakdown --}}
            <div id="reviews" class="py-6 md:py-8 border-b border-hairline-soft animate-fade-in-up" style="animation-delay: 700ms">
                <div class="flex items-center gap-2 mb-6">
                    <svg class="w-6 h-6 text-ink" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <h2 class="text-[20px] font-semibold text-ink">{{ number_format($rating['average'], 1) }} · {{ $rating['total_reviews'] }} ulasan</h2>
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
            <div class="py-6 md:py-8 animate-fade-in-up" style="animation-delay: 800ms">
                <h2 class="text-[20px] font-semibold text-ink mb-6">Kebijakan kamar</h2>
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
        <div id="booking-section" class="md:w-[36%] animate-fade-in-up" style="animation-delay: 300ms">
            <div id="booking-sidebar-wrapper" class="min-[1128px]:sticky min-[1128px]:top-[24px]">
                <div class="border border-hairline rounded-xl p-6 shadow-[0_6px_16px_rgba(0,0,0,0.12)]">
                    {{-- Price Header --}}
                    <div class="mb-6">
                        <span class="text-[28px] font-bold text-ink">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}</span>
                        <span class="text-muted text-base"> per malam</span>
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
                                return Math.round(diff);
                            },
                            get datesValid() {
                                if (!this.checkIn || !this.checkOut) return false;
                                const start = new Date(this.checkIn);
                                const end = new Date(this.checkOut);
                                if (isNaN(start.getTime()) || isNaN(end.getTime())) return false;
                                return end > start;
                            },
                            get datesInvalid() {
                                if (!this.checkIn || !this.checkOut) return false;
                                return !this.datesValid;
                            },
                            get isDisabled() {
                                return this.available === false || this.datesInvalid;
                            },
                            get validationMessage() {
                                if (this.checkIn && this.checkOut && !this.datesValid) {
                                    return 'Check-out harus setelah tanggal check-in';
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
                            <div x-show="available === true" class="border-t border-gray-200 pt-4">
                                {{-- Price Breakdown --}}
                                <div class="flex justify-between items-center text-sm text-body mb-3">
                                    <span>Rp {{ number_format($room->price_per_night, 0, ',', '.') }} × <span x-text="nights"></span> malam</span>
                                    <span class="font-semibold text-ink">Rp <span x-text="totalPrice ? totalPrice.toLocaleString('id-ID') : '0'"></span></span>
                                </div>

                                {{-- Horizontal Divider --}}
                                <hr class="border-gray-200 my-3">

                                <div class="flex justify-between items-center text-base font-bold text-ink mb-6">
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
                                        class="w-full bg-rausch text-on-primary font-semibold text-base px-6 py-3 rounded-lg hover:bg-rausch-active transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
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

                                {{-- Trust Indicator --}}
                                <div class="mt-3 flex items-center justify-center gap-1.5 text-sm text-muted">
                                    <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Konfirmasi instan</span>
                                </div>
                            </div>

                            {{-- Availability Result: Unavailable --}}
                            <div x-show="available === false" class="border-t border-gray-200 pt-4 opacity-60">
                                <div class="bg-surface-soft rounded-lg p-3 text-center">
                                    <svg class="w-8 h-8 mx-auto text-muted-soft mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                    <p class="text-ink font-medium mb-1">Tidak tersedia</p>
                                    <p class="text-muted text-sm">Kamar ini tidak tersedia untuk tanggal yang dipilih. Silakan coba tanggal lain.</p>
                                </div>
                                <button
                                    type="button"
                                    disabled
                                    class="w-full mt-4 bg-rausch text-on-primary font-semibold text-base px-6 py-3 rounded-lg opacity-50 cursor-not-allowed"
                                >
                                    Pesan Sekarang
                                </button>
                            </div>

                            {{-- Invalid Dates State --}}
                            <div x-show="datesInvalid" class="border-t border-gray-200 pt-4 opacity-60">
                                <div class="bg-red-50 rounded-lg p-3 text-center">
                                    <svg class="w-8 h-8 mx-auto text-red-400 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    <p class="text-ink font-medium mb-1">Tanggal tidak valid</p>
                                    <p class="text-muted text-sm">Tanggal check-out harus setelah tanggal check-in.</p>
                                </div>
                                <button
                                    type="button"
                                    disabled
                                    class="w-full mt-4 bg-rausch text-on-primary font-semibold text-base px-6 py-3 rounded-lg opacity-50 cursor-not-allowed"
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

{{-- Mobile Fixed Bottom Bar (visible only on <768px) --}}
<div class="fixed bottom-0 left-0 right-0 z-50 bg-canvas border-t border-hairline-soft shadow-[0_-2px_8px_rgba(0,0,0,0.08)] md:hidden" style="max-height: 72px;">
    <div class="flex items-center justify-between px-4 py-3 max-w-7xl mx-auto">
        <div>
            <span class="text-lg font-bold text-ink">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}</span>
            <span class="text-muted text-sm"> / malam</span>
        </div>
        <button
            type="button"
            onclick="document.getElementById('booking-section').scrollIntoView({ behavior: 'smooth', block: 'start' })"
            class="min-w-[44px] min-h-[44px] bg-rausch text-on-primary font-semibold text-sm px-6 py-3 rounded-lg hover:bg-rausch-active transition-colors"
        >
            Pesan
        </button>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map-container {
        border-radius: 8px;
    }

    /* Mobile touch targets: ensure minimum 44x44px for interactive elements on <768px */
    @media (max-width: 767px) {
        button,
        a[href],
        input[type="date"],
        [role="button"] {
            min-height: 44px;
        }

        button,
        [role="button"] {
            min-width: 44px;
        }
    }
</style>
@endpush

@push('scripts')
{{-- Sticky sidebar footer boundary detection --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var sidebar = document.getElementById('booking-sidebar-wrapper');
        var footer = document.querySelector('footer');
        if (!sidebar || !footer) return;

        var mediaQuery = window.matchMedia('(min-width: 1128px)');

        function handleStickyBoundary() {
            if (!mediaQuery.matches) {
                sidebar.style.position = '';
                sidebar.style.top = '';
                sidebar.style.alignSelf = '';
                return;
            }

            var footerRect = footer.getBoundingClientRect();
            var sidebarRect = sidebar.getBoundingClientRect();
            var sidebarBottom = sidebarRect.top + sidebarRect.height;

            if (sidebarBottom >= footerRect.top) {
                // Sidebar bottom has reached the footer — stop sticking
                sidebar.style.position = 'relative';
                sidebar.style.top = 'auto';
                sidebar.style.alignSelf = 'flex-end';
            } else {
                // Reset to sticky
                sidebar.style.position = '';
                sidebar.style.top = '';
                sidebar.style.alignSelf = '';
            }
        }

        window.addEventListener('scroll', handleStickyBoundary, { passive: true });
        window.addEventListener('resize', handleStickyBoundary, { passive: true });
        handleStickyBoundary();
    });
</script>
@if(isset($mapData))
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mapData = @json($mapData);
        var mapEl = document.getElementById('leaflet-map');
        var fallbackEl = document.getElementById('map-fallback');

        try {
            var map = L.map(mapEl, {
                center: [mapData.lat, mapData.lng],
                zoom: mapData.zoom || 15
            });

            var tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            });

            tileLayer.on('tileerror', function () {
                // On tile load failure, show fallback
                mapEl.style.display = 'none';
                fallbackEl.classList.remove('hidden');
                fallbackEl.classList.add('flex');
                // Remove interactive features
                map.remove();
            });

            tileLayer.addTo(map);

            var marker = L.marker([mapData.lat, mapData.lng]).addTo(map);
            marker.bindPopup(mapData.name);
        } catch (e) {
            // If Leaflet fails to initialize, show fallback
            mapEl.style.display = 'none';
            fallbackEl.classList.remove('hidden');
            fallbackEl.classList.add('flex');
        }
    });
</script>
@endif
@endpush
