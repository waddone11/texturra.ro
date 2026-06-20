<div x-data="{ open: false }" class="w-full bg-blue-50 border-l-4 border-black p-2 md:p-4 rounded-lg shadow-sm">
    <div
        class="flex items-center justify-between gap-3 cursor-pointer"
        @click="open = !open"
    >
        <!-- Left Side: Icon + Title -->
        <div class="flex items-center gap-3">
            <div class="text-black text-3xl font-bold">
                <i class="fa-solid fa-info-circle"></i>
            </div>
            <h3 class="font-bold text-xs md:text-sm text-blue-800 mb-0">
                Cum comandăm Perdea și/sau Draperie?
            </h3>
        </div>

        <!-- Right Side: Click Aici -->
        <div class="text-xs md:text-sm text-blue-800 underline">
            Click aici
        </div>
    </div>

    <div x-show="open" x-transition class="mt-4 pl-9 pr-2 text-sm text-blue-700 space-y-3">
        <ul class="list-disc space-y-1">
            <li>
                <strong>Perdea</strong>: Lungimea sinei/galeriei × 2 + <strong>20cm</strong><br/>
                <span class="text-xs italic">Exemplu: Dacă sina are 1.5m → 1.5 × 2 + 0.20 = <strong>3.20 ml</strong></span>
            </li>
            <li>
                <strong>Draperie</strong>: Lungimea sinei/galeriei × 1.5 + <strong>20cm</strong><br/>
                <span class="text-xs italic">Exemplu: Dacă sina are 1.5m → 1.5 × 1.5 + 0.20 = <strong>2.45 ml</strong></span>
            </li>
        </ul>
        <p class="text-xs md:text-sm">
            <strong>+20 cm</strong> sunt adăugați automat pentru cusătura marginilor laterale.
        </p>
    </div>
</div>
@php
    $alpineData = [
        'length' => 1.0,
        'height' => $product->height ? floatval($product->height) : 2.8,
        'maxLength' => 30,
        'maxHeight' => $product->height ? floatval($product->height) : 3.0,
        'materialPrice' => floatval($product->price),
        'pieces' => 1,
        'manufactoringTypes' => $manufactoringTypes,
        'manufactoringLabel' => $manufactoringTypes[0]->name ?? '',
        'manufactoringPrice' => floatval($manufactoringTypes[0]->price ?? 0),
        'total' => 0,
    ];
@endphp
<!-- FORM BLOCK -->
<div class="w-full mx-auto bg-white rounded-2xl py-6 pt-0"
     x-data="productFormComponent(window.productFormProps)"
>
    <form method="POST" action="{{ route('cart.add.custom') }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="length" :value="length">
        <input type="hidden" name="height" :value="height">
        <input type="hidden" name="manufactoring_type_id" :value="manufactoringId">
        <input type="hidden" name="pieces" :value="pieces">

        <!-- DIMENSIUNI -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="font-semibold text-sm mb-1 block">Lățime (m)</label>
                <div class="flex items-center border border-black rounded-md overflow-hidden">
                    <button type="button" class="w-10 h-10 flex justify-center items-center border-r border-black"
                            @click="length = Math.max(1, +(length - 0.1).toFixed(2))">−</button>
                    <input type="number" x-model="length" step="0.1" min="1" max="30"
                           class="w-full text-center font-medium border-0 focus:outline-none">
                    <button type="button" class="w-10 h-10 flex justify-center items-center border-l border-black"
                            @click="length = Math.min(maxLength, +(length + 0.1).toFixed(2))">+</button>
                </div>
                <p class="text-xs text-gray-500 mt-1">minim 1 metru, maxim 30 metri</p>
            </div>

            <div>
                <label class="font-semibold text-sm mb-1 block">Înălțime (m)</label>
                <div class="flex items-center border border-black rounded-md overflow-hidden">
                    <button type="button" class="w-10 h-10 flex justify-center items-center border-r border-black"
                            @click="height = Math.max(0.5, +(height - 0.1).toFixed(2))">−</button>
                    <input type="number" x-model="height" step="0.1" min="0.5" :max="maxHeight"
                           class="w-full text-center font-medium border-0 focus:outline-none">
                    <button type="button" class="w-10 h-10 flex justify-center items-center border-l border-black"
                            @click="height = Math.min(maxHeight, +(height + 0.1).toFixed(2))">+</button>
                </div>
                <p class="text-xs text-gray-500 mt-1">maxim {{ $product->height ?? 3 }} metri</p>
            </div>
        </div>

        <!-- MANOPERĂ -->
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-2 mb-6">
            <template x-for="type in manufactoringTypes" :key="type.id">
                <label class="flex items-center justify-between px-1 md:px-4 py-1 md:py-3 border rounded-lg transition cursor-pointer"
                       :class="manufactoringLabel === type.name
                    ? 'border-black bg-black text-white'
                    : 'border-black bg-white text-black'">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between w-full">
                        <div class="flex items-center gap-3">
                            <div class="w-5 h-5 flex items-center justify-center rounded-full border transition"
                                 :class="manufactoringLabel === type.name ? 'bg-white border-white' : 'bg-black border-black'">
                                <div class="w-2.5 h-2.5 rounded-full"
                                     :class="manufactoringLabel === type.name ? 'bg-transparent' : 'bg-black'"></div>
                            </div>
                            <input type="radio"
                                   name="manufactoring_type_id"
                                   :value="type.id"
                                   class="hidden"
                                   @change="setManopera(type.name, type.price, type.id)"
                                   :checked="manufactoringLabel === type.name">

                            <span class="font-bold text-xs md:text-sm" x-text="type.name"></span>
                        </div>
                        <span class="mt-1 md:mt-0 textSuperSmall text-xs md:text-sm font-bold pl-8"
                              x-text="`${type.price} lei/m`"></span>
                    </div>
                </label>
            </template>
        </div>


        <!-- BUCĂȚI -->

        <div class="flex gap-2 mb-6">

            <label class="flex flex-col items-center p-4 border rounded-lg cursor-pointer w-full transition"
                   :class="pieces === 1 ? 'border-black bg-black text-white' : 'border-black bg-white text-black'">
                <input type="radio" name="bucati" value="1" class="hidden" @change="pieces = 1" :checked="pieces === 1">
                <img
                    src="/storage/images/icons/1piece.png"
                    :src="pieces === 1
                                            ? '/storage/images/icons/1piece_white.png'
                                            : '/storage/images/icons/1piece.png'"
                     alt="1 bucată" class="w-10 mb-2 transition">
                <span class="text-sm font-bold">1 bucată</span>
            </label>

            <label class="flex flex-col items-center p-4 border rounded-lg cursor-pointer w-full transition"
                   :class="pieces === 2 ? 'border-black bg-black text-white' : 'border-black bg-white text-black'">
                <input type="radio" name="bucati" value="2" class="hidden" @change="pieces = 2" :checked="pieces === 2">
                <img
                    src="/storage/images/icons/2pieces.png"
                    :src="pieces === 2
                                            ? '/storage/images/icons/2pieces_white.png'
                                            : '/storage/images/icons/2pieces.png'"
                     alt="2 bucăți" class="w-10 mb-2 transition">
                <span class="text-sm font-bold">2 bucăți</span>
            </label>
        </div>
        <div>
            <p class="text-xs text-blue-700">
                * Selectează dacă dorești ca perdeaua să fie tăiată într-o singură bucată sau în două egale.<br/>
                Alegerea NU influențează prețul.
            </p>
        </div>

        <!-- TOTAL -->
        <div class="flex flex-col gap-3">
            <div class="text-sm text-gray-700 text-center">
                <p>
                    <strong>Total estimat:</strong>
                    <span class="text-lg font-bold" x-text="total + ' lei'"></span>
                </p>
                <p class="text-xs mt-1 italic">
                    Lungime (
                    <span x-text="length.toFixed(2)"></span> m) × Preț material (
                    <span x-text="materialPrice"></span> lei) + Preț manoperă (
                    <span x-text="manufactoringPrice"></span> lei/m)
                </p>
            </div>

            <button type="submit"
                    class="w-full bg-black text-white text-center py-3 rounded-lg font-semibold hover:bg-gray-900 transition">
                ADAUGĂ ÎN COȘ
            </button>
        </div>
    </form>
</div>

<script>
    window.productFormProps = {
        maxHeight: {{ $product->height ? floatval($product->height) : 3.0 }},
        manufactoringTypes: @json($manufactoringTypes),
        materialPrice: {{ floatval($product->price) }}
    };
</script>
<script>
    function productFormComponent({ maxHeight, manufactoringTypes, materialPrice }) {
        return Alpine.reactive({
            length: 1.0,
            height: 2.8,
            maxLength: 30,
            maxHeight: parseFloat(maxHeight),
            materialPrice: parseFloat(materialPrice),
            pieces: 1,
            manufactoringTypes,
            manufactoringLabel: manufactoringTypes[0]?.name || '',
            manufactoringPrice: parseFloat(manufactoringTypes[0]?.price || 0),
            manufactoringId: manufactoringTypes[0]?.id || null,

            setManopera(label, price) {
                this.manufactoringLabel = label;
                this.manufactoringPrice = parseFloat(price);
                this.manufactoringId = id;
            },

            get total() {
                return ((this.length * this.materialPrice) + (this.length * this.manufactoringPrice)).toFixed(2);
            }
        });
    }

</script>



