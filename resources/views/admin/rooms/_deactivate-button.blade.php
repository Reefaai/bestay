<div x-data="{ showConfirm: false }">
    <button
        @click="showConfirm = true"
        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-error border border-error rounded-sm hover:bg-red-50 transition-colors"
    >
        Deactivate
    </button>

    {{-- Confirmation Dialog --}}
    <div
        x-show="showConfirm"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @keydown.escape.window="showConfirm = false"
    >
        <div class="absolute inset-0 bg-ink/50" @click="showConfirm = false"></div>

        <div
            x-show="showConfirm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-canvas rounded-md p-8 shadow-lg max-w-sm w-full"
        >
            <h3 class="text-lg font-semibold text-ink mb-2">Deactivate Room</h3>
            <p class="text-sm text-muted mb-6">
                Are you sure you want to deactivate <strong>{{ $room->name }}</strong>? It will no longer appear in room listings.
            </p>

            <div class="flex gap-2 justify-end">
                <button
                    @click="showConfirm = false"
                    class="px-6 py-2 text-sm font-medium text-muted border border-hairline rounded-sm hover:bg-surface-soft transition-colors"
                >
                    Cancel
                </button>

                <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="px-6 py-2 text-sm font-medium text-on-primary bg-error rounded-sm hover:bg-error-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!submitting">Yes, Deactivate</span>
                        <span x-show="submitting" class="flex items-center gap-1">
                            <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Deactivating...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
