@extends('layouts.app')

@section('title', '403 Forbidden')

@section('content')
<div class="max-w-xl mx-auto px-4 py-24 text-center">
    <h1 class="text-6xl font-bold text-rausch mb-4">403</h1>
    <h2 class="text-2xl font-semibold text-ink mb-4">Access Denied</h2>
    <p class="text-gray-600 mb-8">{{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}</p>
    <a href="{{ route('rooms.index') }}" class="inline-block bg-rausch text-white font-medium px-6 py-3 rounded-lg hover:bg-rausch/90 transition-colors">
        Back to Homepage
    </a>
</div>
@endsection
