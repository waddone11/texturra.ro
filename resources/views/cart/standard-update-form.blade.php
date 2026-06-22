<form action="{{ route('cart.update', $item->id) }}" method="POST" x-data="{ quantity: {{ $item->quantity }} }" class="flex flex-wrap items-center gap-2">
    @csrf
    @method('PATCH')

    <input type="hidden" name="quantity" :value="quantity">

    <div class="flex items-center overflow-hidden rounded-md border border-[#171411]/25">
        <button type="button"
                class="flex h-9 w-10 items-center justify-center border-r border-[#171411]/15 text-[#171411] transition-colors hover:bg-[#f1ece4]"
                @click="quantity = Math.max(1, quantity - 1)">−</button>
        <input type="number" x-model="quantity" min="1"
               class="h-9 w-12 border-0 p-0 text-center text-sm font-medium focus:outline-none focus:ring-0">
        <button type="button"
                class="flex h-9 w-10 items-center justify-center border-l border-[#171411]/15 text-[#171411] transition-colors hover:bg-[#f1ece4]"
                @click="quantity++">+</button>
    </div>

    <button type="submit"
            class="inline-flex h-9 items-center rounded-md border border-[#171411]/25 px-4 text-[11px] font-semibold uppercase tracking-[0.08em] text-[#171411] transition-colors hover:border-[#B58A43] hover:text-[#8c6529]">
        Actualizează
    </button>
</form>
