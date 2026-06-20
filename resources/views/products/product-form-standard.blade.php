<!-- FORM BLOCK - STANDARD PRODUCT -->
<div class="w-full mx-auto bg-white rounded-2xl py-6 pt-4"
     x-data="{ quantity: 1 }">
    <form method="POST" action="{{ route('cart.add.standard') }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="quantity" :value="quantity">

        <!-- Cantitate -->
        <div class="mb-6">
            <label class="font-semibold text-sm mb-1 block">Cantitate</label>
            <div class="flex items-center border border-black rounded-md overflow-hidden w-full max-w-xs">
                <button type="button"
                        class="w-10 h-10 flex justify-center items-center border-r border-black"
                        @click="quantity = Math.max(1, quantity - 1)">
                    −
                </button>
                <input type="number"
                       x-model="quantity"
                       min="1"
                       class="w-full text-center font-medium border-0 focus:outline-none">
                <button type="button"
                        class="w-10 h-10 flex justify-center items-center border-l border-black"
                        @click="quantity++">
                    +
                </button>
            </div>
        </div>

        <!-- Buton submit -->
        <button type="submit"
                class="w-full bg-black text-white text-center py-3 rounded-lg font-semibold hover:bg-gray-900 transition">
            ADAUGĂ ÎN COȘ
        </button>
    </form>
</div>
