<div class="inline-flex">
    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center transition-colors hover:text-[#B28D4E]" aria-label="Coș">
        <i class="fa-solid fa-cart-shopping fa-lg"></i>
        @if ($cartCount > 0)
            <span class="absolute -right-2.5 -top-2 inline-flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-[#B28D4E] px-1 text-[10px] font-semibold leading-none text-white">{{ $cartCount }}</span>
        @endif
    </a>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.addEventListener('cartUpdated', () => {
                Livewire.dispatch('cartUpdated');
            });
        });
    </script>
</div>
