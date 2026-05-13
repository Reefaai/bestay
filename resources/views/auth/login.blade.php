@extends('layouts.app')

@section('title', 'Log in')

@section('content')
<div class="flex items-start md:items-center justify-center min-h-[calc(100vh-160px)] px-4 py-8">
    <div class="w-full max-w-[400px]">
        <h1 class="text-2xl font-semibold text-ink mb-6">Log in</h1>

        <form method="POST" action="{{ route('login') }}" x-data="{ submitting: false }" @submit="setTimeout(() => submitting = true, 50)">
            @csrf

            {{-- Email Field --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-ink mb-1">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="w-full h-[56px] px-4 border border-hairline rounded-sm text-ink placeholder-muted-soft focus:outline-none focus:border-ink focus:ring-0 focus:border-2 transition-colors"
                    placeholder="you@example.com"
                >
                @error('email')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Field --}}
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-ink mb-1">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full h-[56px] px-4 border border-hairline rounded-sm text-ink placeholder-muted-soft focus:outline-none focus:border-ink focus:ring-0 focus:border-2 transition-colors"
                    placeholder="Enter your password"
                >
                @error('password')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button
                type="submit"
                :disabled="submitting"
                class="w-full h-[48px] bg-rausch text-on-primary font-medium rounded-sm hover:bg-rausch-active transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span x-show="!submitting">Log in</span>
                <span x-show="submitting" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Logging in...
                </span>
            </button>
        </form>

        {{-- Register Link --}}
        <p class="mt-6 text-center text-body text-sm">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-rausch font-medium hover:underline">Sign up</a>
        </p>
    </div>
</div>
@endsection
