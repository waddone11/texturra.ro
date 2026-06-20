<form action="{{ route('cart.update', $item->id) }}" method="POST" x-data="{ quantity: {{ $item->quantity }} }" class="flex items-center">
    @csrf
    @method('PATCH')

    <input type="hidden" name="quantity" :value="quantity">

    <div class="flex items-center border border-black rounded-md overflow-hidden w-[150px] max-w-xs">
        <button type="button"
                class="w-12 h-8 flex justify-center items-center border-r border-black"
                @click="quantity = Math.max(1, quantity - 1)">
            −
        </button>
        <input type="number"
               x-model="quantity"
               min="1"
               class="w-full text-center font-medium border-0 focus:outline-none p-0">
        <button type="button"
                class="w-12 h-8 flex justify-center items-center border-l border-black"
                @click="quantity++">
            +
        </button>
    </div>

    <button type="submit"
            class="w-[150px] bg-black text-white text-center py-2 rounded-lg font-semibold hover:bg-gray-900 transition ml-4 text-xs px-1 border border-black">
        Actualizează
    </button>
</form>
