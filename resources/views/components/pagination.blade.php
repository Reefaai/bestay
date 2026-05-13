{{-- Pagination component --}}
{{-- Receives: $paginator (Laravel paginator instance) --}}
@if($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-center gap-1 py-6">
        {{-- Previous Page Link --}}
        @if($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-sm text-muted-soft cursor-not-allowed" aria-disabled="true">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-sm text-ink hover:bg-surface-soft transition-colors" rel="prev" aria-label="Previous page">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
            @if($page == $paginator->currentPage())
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-sm bg-rausch text-on-primary text-sm font-medium" aria-current="page">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 rounded-sm text-ink hover:bg-surface-soft text-sm font-medium transition-colors">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-sm text-ink hover:bg-surface-soft transition-colors" rel="next" aria-label="Next page">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        @else
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-sm text-muted-soft cursor-not-allowed" aria-disabled="true">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </span>
        @endif
    </nav>
@endif
