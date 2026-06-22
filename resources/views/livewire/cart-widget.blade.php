<div>
    <div class="fixed bottom-6 right-8 space-y-4 z-50" id="ctaBlock">
        <div class="relative">
            <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-gray-900">
                <x-cart class="block h-9 w-auto fill-current text-black" />
            </a>
            <span class="absolute -top-3 -right-3 bg-red-600 text-white rounded-full px-2 py-1 text-xs">
                {{ $cartCount }}
            </span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.addEventListener('cartUpdated', () => {
            Livewire.dispatch('cartUpdated');
        });
    });
</script>
