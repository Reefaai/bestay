{{-- Footer component --}}
<footer class="bg-canvas border-t border-hairline mt-auto">
    <div class="max-w-7xl mx-auto px-4 py-12">
        {{-- 3-column link layout (1-col mobile, 3-col desktop) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- About Section --}}
            <div>
                <h3 class="text-ink font-semibold text-sm mb-4">About Bestay</h3>
                <p class="text-muted text-sm leading-relaxed mb-4">
                    Bestay is a modern hotel booking platform offering comfortable stays at great prices. Find your perfect room and book with confidence.
                </p>
                <a href="/" class="text-rausch font-bold text-lg tracking-tight">Bestay</a>
            </div>

            {{-- Support Links --}}
            <div>
                <h3 class="text-ink font-semibold text-sm mb-4">Support</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="/" class="text-muted hover:text-rausch text-sm transition-colors">Help Center</a>
                    </li>
                    <li>
                        <a href="/" class="text-muted hover:text-rausch text-sm transition-colors">Safety Information</a>
                    </li>
                    <li>
                        <a href="/" class="text-muted hover:text-rausch text-sm transition-colors">Cancellation Options</a>
                    </li>
                    <li>
                        <a href="/" class="text-muted hover:text-rausch text-sm transition-colors">Contact Us</a>
                    </li>
                </ul>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-ink font-semibold text-sm mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="/" class="text-muted hover:text-rausch text-sm transition-colors">Browse Rooms</a>
                    </li>
                    <li>
                        <a href="/login" class="text-muted hover:text-rausch text-sm transition-colors">Login</a>
                    </li>
                    <li>
                        <a href="/register" class="text-muted hover:text-rausch text-sm transition-colors">Register</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Copyright Line --}}
        <div class="border-t border-hairline mt-8 pt-6">
            <p class="text-muted-soft text-sm text-center">
                &copy; {{ date('Y') }} Bestay. All rights reserved.
            </p>
        </div>
    </div>
</footer>
