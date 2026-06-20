@extends('layouts.base')

@section('content')
    <div class="max-w-7xl mx-auto py-0 md:py-12 md:py-12 px-0 md:p-8">
        <div class="flex flex-col lg:flex-row gap-4 md:gap-8 py-0 md:py-8">
            <!-- Galerie imagini -->
            <div class="lg:w-2/5 block md:hidden relative">
                <div id="swiper-{{ $product->id }}" class="swiper-container overflow-hidden relative">
                    <div class="swiper-wrapper">
                        @foreach($product->images as $image)
                            <div class="swiper-slide relative">
                                <img
                                    src="{{ asset($image) }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-108 object-cover p-0 bg-white"
                                />

                                <button onclick="openZoomGallery()" class="absolute top-4 h-10 w-10 left-4 bg-white text-black p-2 rounded-full z-20">
                                    <i class="fa fa-search-plus"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="swiper-pagination !bottom-4 absolute left-0 w-full z-20"></div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        new Swiper('#swiper-{{ $product->id }}', {
                            slidesPerView: 1,
                            spaceBetween: 10,
                            pagination: {
                                el: '#swiper-{{ $product->id }} .swiper-pagination',
                                clickable: true,
                            },
                        });
                    });
                </script>
            </div>

            <div class="lg:w-2/5 hidden md:block">
                <!-- Main Image -->
                <div class="mb-4 relative">

                    <button onclick="openZoomGallery()" class="absolute top-4 h-10 w-10 left-4 bg-white text-black p-2 rounded-full z-10">
                        <i class="fa fa-search-plus"></i>
                    </button>

                    <img id="mainImage"
                         src="{{ $product->images[0] ?? '/placeholder-image.png' }}"
                         alt="{{ $product->name }}"
                         class="w-full h-auto rounded-2xl"
                    />
                </div>
                <!-- Thumbnail Images -->
                <div class="flex flex-wrap gap-2 px-2 mt-12 md:mt-0 pt-4 md:pt-0 md:px-0 flex items-center">
                    @foreach ($product->images as $key => $image)
                        <img src="{{ $image }}"
                             alt="{{ $product->name }}"
                             class="thumbnail w-1/5 md:w-1/8 max-w-[50px] h-16 object-cover rounded-xl shadow-2xl border border-black hover:scale-105 transition-transform duration-200 cursor-pointer"
                             data-src="{{ $image }}"
                             onclick="updateMainImage(this)"
                        />
                    @endforeach
                </div>

                <div class="w-full mt-4 p-4 border border-black rounded-lg text-xs font-bold text-left text-black">
                    <div class="w-full">
                        <h4 class="text-lg font-semibold mb-2">INFO PRODUS</h4>
                        <p class="mb-4 text-gray-700">
                            Este o perdea ușoară și semi-transparentă și permite luminii naturale să pătrundă delicat în încăpere.
                        </p>

                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <img src="https://perdeledraperiimoderne.ro/public/images/temperatura-spalare.png" alt="Spalare 30°C" class="w-6 h-6 mt-1">
                                <span class="pt-2"><strong>1. Se spală la 30°C</strong></span>
                            </li>
                            <li class="flex items-start gap-3">
                                <img src="https://perdeledraperiimoderne.ro/public/images/centrifugare.png" alt="Centrifugare" class="w-6 h-6 mt-1">
                                <span class="pt-2"><strong>2. Nu se folosesc înălbitori chimici și stoarcere la maxim 600 de turații;</strong></span>
                            </li>
                            <li class="flex items-start gap-3">
                                <img src="https://perdeledraperiimoderne.ro/public/images/calcare.png" alt="Calcare" class="w-6 h-6 mt-1">
                                <span class="pt-2"><strong>3. Se calcă la temperatură normală cu abur;</strong></span>
                            </li>
                            <li class="flex items-start gap-3">
                                <img src="https://perdeledraperiimoderne.ro/public/images/spalare.png" alt="Prespalare" class="w-6 h-6 mt-1">
                                <span class="pt-2"><strong>4. Înainte de prima utilizare, vă recomandăm prespălarea produselor.</strong></span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

            <!-- Detalii produs -->
            <div class="lg:w-3/5 px-2 pt-2 md:pt-0">
                <div class="w-full">
                    <h1 class="text-3xl font-bold text-gray-800">{{ $product->name }}</h1>
                </div>

                <div class="w-full text-2xl font-extrabold text-right hidden md:block">
                    {{ number_format($product->price(), 2) }} lei
                </div>
                <!-- Brand -->
                <p class="text-sm text-gray-500 mt-2 md:mt-0 mb-2">Categorie: <span class="font-semibold text-black">{{ $product->category->name ?? 'N/A' }}</span></p>
                <p class="text-sm text-gray-500 mb-2">Cod produs: <span class="font-semibold text-black">TXT-{{ $product->id }}</span></p>
                @php
                    $attributes = $product->variations
                        ->pluck('attributeValues')
                        ->flatten()
                        ->unique('id')
                        ->filter(fn($attr) => $attr->attribute) // ensure attribute exists
                        ->groupBy(fn($attr) => $attr->attribute->name);
                @endphp

                @if($attributes->isNotEmpty())

                    @foreach ($attributes as $label => $values)
                        <p class="text-sm text-gray-500 mb-2">{{ $label }}: <span class="font-semibold text-black">{{ $values->pluck('value')->unique()->implode(', ') }}</span></p>
                    @endforeach
                @endif


                @php
                    $originalPrice = $product->price ?? 0;
                    $discountedPrice = $product->price();
                    $discountPercentage = $originalPrice > 0 ? round((($originalPrice - $discountedPrice) / $originalPrice) * 100) : 0;
                @endphp
                @if($originalPrice > $discountedPrice)
                    <div class="w-full h-6 py-1 bg-black px-2 text-sm font-extrabold text-white flex items-center space-x-2 mt-4 mb-4 hidden md:block">
                        <span>Discount:</span>
                        <span class="text-red-600">-{{ $discountPercentage }}%</span>
                        <span class="line-through opacity-60"> PRET VECHI {{ number_format($originalPrice, 2) }} lei</span>
                        <span class="text-yellow-400">Pret nou: {{ number_format($discountedPrice, 2) }} lei</span>
                    </div>
                @endif
                <!-- Descriere -->
{{--                <div class="text-black mb-6 text-sm/relaxed hidden md:block">--}}
{{--                    {!! html_entity_decode($product->description) !!}--}}
{{--                </div>--}}

                <!-- Buton adaugare în coș -->
                <div class="flex flex-col gap-4 rounded-2xl">
                    <!-- Left Section: Form -->
                    <div class="w-full mx-auto bg-white border border-black rounded-2xl p-12 py-6 shadow-xl" x-data="{
                            length: 1.0,
                            height: 2.8,
                            maxLength: 30,
                            maxHeight: 3,
                            rejansaPrice: 0,
                            rejansaLabel: '',
                            quantity: 1,
                            prices: {
                                'Manoperă rejanșă': 9,
                                'Manoperă capse': 30,
                                'Rejansă galerie': 12,
                                'Rejansă 10 cm': 11,
                                'Rejansă tiv lat': 8,
                            },
                            setRejansa(label, price) {
                                this.rejansaLabel = label;
                                this.rejansaPrice = price;
                            },
                            get total() {
                                return (this.length * this.rejansaPrice).toFixed(2);
                            }
                        }">
                        <!-- Dimensiuni -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="font-semibold text-sm mb-1 block">Adaugă lungimea dorită</label>
                                <div class="flex items-center border border-gray-300 rounded-md overflow-hidden">
                                    <button type="button" class="w-10 h-10 flex justify-center items-center" @click="length = Math.max(0.5, length - 0.5)">−</button>
                                    <input type="number" x-model="length" step="0.5" min="0.5" max="30" class="w-full text-center font-medium border-0 border-l border-r border-gray-200">
                                    <button type="button" class="w-10 h-10 flex justify-center items-center" @click="length = Math.min(maxLength, length + 0.5)">+</button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">maxim 30 metri liniari</p>
                            </div>
                            <div>
                                <label class="font-semibold text-sm mb-1 block">Înălțime</label>
                                <div class="flex items-center border border-gray-300 rounded-md overflow-hidden">
                                    <button type="button" class="w-10 h-10 flex justify-center items-center" @click="height = Math.max(0.5, height - 0.1)">−</button>
                                    <input type="number" x-model="height" step="0.1" min="0.5" max="3" class="w-full text-center font-medium border-0  border-l border-r border-gray-200">
                                    <button type="button" class="w-10 h-10 flex justify-center items-center" @click="height = Math.min(maxHeight, height + 0.1)">+</button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">maxim 3 metri</p>
                            </div>
                        </div>

                        <!-- Tip rejansă -->
                        <div class="space-y-2 mb-6">
                            <template x-for="(price, label) in prices" :key="label">
                                <label class="flex items-center justify-between px-4 py-3 border rounded-lg hover:border-black transition cursor-pointer"
                                       :class="rejansaLabel === label ? 'border-black bg-gray-50' : 'border-gray-300'">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="rejansa" :value="label" class="form-radio text-black" @change="setRejansa(label, price)" :checked="rejansaLabel === label">
                                        <span class="font-medium text-sm" x-text="label"></span>
                                    </div>
                                    <span class="text-sm text-gray-600" x-text="`(${price} RON / metru)`"></span>
                                </label>
                            </template>
                        </div>

                        <!-- Bucăți -->
                        <div class="flex gap-6 mb-6">
                            <label class="flex flex-col items-center p-4 border rounded-lg cursor-pointer w-full hover:border-black transition"
                                   :class="quantity === 1 ? 'border-black bg-gray-50' : 'border-gray-300'">
                                <input type="radio" name="bucati" value="1" class="hidden" @change="quantity = 1" :checked="quantity === 1">
                                <img src="/storage/images/1piece.png" alt="1 bucata" class="w-10 mb-2">
                                <span class="text-sm font-medium">1 bucată</span>
                            </label>
                            <label class="flex flex-col items-center p-4 border rounded-lg cursor-pointer w-full hover:border-black transition"
                                   :class="quantity === 2 ? 'border-black bg-gray-50' : 'border-gray-300'">
                                <input type="radio" name="bucati" value="2" class="hidden" @change="quantity = 2" :checked="quantity === 2">
                                <img src="/storage/images/2pieces.png" alt="2 bucăți" class="w-10 mb-2">
                                <span class="text-sm font-medium">2 bucăți</span>
                            </label>
                        </div>

                        <!-- Total și buton -->
                        <div class="flex flex-col gap-3">
                            <div class="text-center font-bold text-lg">Total estimat: <span x-text="total + ' RON'"></span></div>
                            <button type="submit" class="w-full bg-black text-white text-center py-3 rounded-lg font-semibold hover:bg-gray-900 transition">
                                ADAUGĂ ÎN COȘ
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Size Guide Modal -->
                <div id="sizeGuideModal"
                     class="fixed inset-y-0 right-0 w-full md:w-1/3 lg:w-1/4 bg-white shadow-lg transform translate-x-full transition-transform duration-300 overflow-y-auto z-50">

                    <!-- Header -->
                    <div class="flex justify-between items-center p-4 border-b">
                        <h2 class="text-lg font-bold">Tabel Mărimi</h2>
                        <button onclick="closeSizeGuide()" class="text-gray-600 hover:text-black">
                            ✕
                        </button>
                    </div>

                    <!-- Content -->
                    <div id="sizeGuideContent" class="p-4 text-sm">
                        <p>Încărcare...</p>
                    </div>
                </div>

                <!-- Backdrop -->
                <div id="sizeGuideBackdrop" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 transition-opacity" onclick="closeSizeGuide()"></div>
                <!-- Secțiunea acordiune -->
{{--                <div class="mt-12">--}}
{{--                    <h2 class="text-xl font-bold mb-4">Detalii produs</h2>--}}
{{--                    <div class="border border-gray-300 rounded-lg overflow-hidden">--}}
{{--                        <!-- Specificații -->--}}
{{--                        <div x-data="{ open: true }" class="border-b">--}}
{{--                            <button--}}
{{--                                @click="open = !open"--}}
{{--                                :class="{ 'bg-black text-white': open }"--}}
{{--                                class="w-full px-4 py-3 text-left flex justify-between items-center text-black"--}}
{{--                            >--}}
{{--                                <span class="font-bold text-sm">Specificații</span>--}}
{{--                                <span x-show="!open">+</span>--}}
{{--                                <span x-show="open">-</span>--}}
{{--                            </button>--}}
{{--                            <div x-show="open" class="px-0 py-2 text-black">--}}
{{--                                <div class="mt-2">--}}
{{--                                    <div class="rounded-lg overflow-hidden">--}}
{{--                                        @forelse ($characteristicsWithLabels as $characteristic)--}}
{{--                                            <div class="border-b px-4 py-2 flex justify-between items-center text-xs">--}}
{{--                                                <span class="text-gray-600 font-semibold">{{ $characteristic['label'] }}</span>--}}
{{--                                                <span class="text-gray-800">{{ $characteristic['value'] }}</span>--}}
{{--                                            </div>--}}
{{--                                        @empty--}}
{{--                                            <div class="text-gray-500 px-4 py-2">--}}
{{--                                                @php--}}
{{--                                                    $attributes = $product->variations--}}
{{--                                                        ->pluck('attributeValues')--}}
{{--                                                        ->flatten()--}}
{{--                                                        ->unique('id')--}}
{{--                                                        ->filter(fn($attr) => $attr->attribute) // ensure attribute exists--}}
{{--                                                        ->groupBy(fn($attr) => $attr->attribute->name);--}}
{{--                                                @endphp--}}

{{--                                                @if($attributes->isNotEmpty())--}}
{{--                                                    <table class="w-full text-sm border border-gray-300 mb-4">--}}

{{--                                                        <tbody>--}}
{{--                                                        @foreach ($attributes as $label => $values)--}}
{{--                                                            <tr class="border-t text-xs text-gray-800">--}}
{{--                                                                <td class="px-4 py-2 font-semibold w-1/3">{{ $label }}</td>--}}
{{--                                                                <td class="px-4 py-2">--}}
{{--                                                                    {{ $values->pluck('value')->unique()->implode(', ') }}--}}
{{--                                                                </td>--}}
{{--                                                            </tr>--}}
{{--                                                        @endforeach--}}
{{--                                                        </tbody>--}}
{{--                                                    </table>--}}
{{--                                                @endif--}}

{{--                                                {!! html_entity_decode($product->description) !!}--}}
{{--                                            </div>--}}
{{--                                        @endforelse--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <!-- Garanție -->--}}
{{--                        <div x-data="{ open: false }" class="border-b">--}}
{{--                            <button--}}
{{--                                @click="open = !open"--}}
{{--                                :class="{ 'bg-black text-white': open }"--}}
{{--                                class="w-full px-4 py-3 text-left flex justify-between items-center text-black"--}}
{{--                            >--}}
{{--                                <span class="font-bold text-sm">Garanție</span>--}}
{{--                                <span x-show="!open">+</span>--}}
{{--                                <span x-show="open">-</span>--}}
{{--                            </button>--}}
{{--                            <div x-show="open" class="px-4 py-2 text-black">--}}
{{--                                <p class="text-xs">{{ $product->warranty ?? 'Nu există informații despre garanție.' }}</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <!-- Transport -->--}}
{{--                        <div x-data="{ open: false }" class="border-b">--}}
{{--                            <button--}}
{{--                                @click="open = !open"--}}
{{--                                :class="{ 'bg-black text-white': open }"--}}
{{--                                class="w-full px-4 py-3 text-left flex justify-between items-center text-black"--}}
{{--                            >--}}
{{--                                <span class="font-bold text-sm">Transport</span>--}}
{{--                                <span x-show="!open">+</span>--}}
{{--                                <span x-show="open">-</span>--}}
{{--                            </button>--}}
{{--                            <div x-show="open" class="px-4 py-2 text-black">--}}
{{--                                <p class="text-xs">Detaliile transportului vor fi disponibile la checkout.</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <!-- Returnări -->--}}
{{--                        <div x-data="{ open: false }">--}}
{{--                            <button--}}
{{--                                @click="open = !open"--}}
{{--                                :class="{ 'bg-black text-white': open }"--}}
{{--                                class="w-full px-4 py-3 text-left flex justify-between items-center text-black"--}}
{{--                            >--}}
{{--                                <span class="font-bold text-sm">Retur</span>--}}
{{--                                <span x-show="!open">+</span>--}}
{{--                                <span x-show="open">-</span>--}}
{{--                            </button>--}}
{{--                            <div x-show="open" class="px-4 py-2 text-black">--}}
{{--                                <p class="text-xs">Returul este acceptat în termen de 30 de zile de la achiziție.</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>

        <div class="mt-12">
            <h2 class="text-xl font-bold mb-4">Detalii produs</h2>
            <div class="border border-gray-300 rounded-lg overflow-hidden">
                <!-- Specificații -->
                <div x-data="{ open: true }" class="border-b">
                    <button
                        @click="open = !open"
                        :class="{ 'bg-black text-white': open }"
                        class="w-full px-4 py-3 text-left flex justify-between items-center text-black"
                    >
                        <span class="font-bold text-sm">Specificații</span>
                        <span x-show="!open">+</span>
                        <span x-show="open">-</span>
                    </button>
                    <div x-show="open" class="px-0 py-2 text-black">
                        <div class="mt-2">
                            <div class="rounded-lg overflow-hidden">
                                @forelse ($characteristicsWithLabels as $characteristic)
                                    <div class="border-b px-4 py-2 flex justify-between items-center text-xs">
                                        <span class="text-gray-600 font-semibold">{{ $characteristic['label'] }}</span>
                                        <span class="text-gray-800">{{ $characteristic['value'] }}</span>
                                    </div>
                                @empty
                                    <div class="text-gray-500 px-4 py-2">
                                        @php
                                            $attributes = $product->variations
                                                ->pluck('attributeValues')
                                                ->flatten()
                                                ->unique('id')
                                                ->filter(fn($attr) => $attr->attribute) // ensure attribute exists
                                                ->groupBy(fn($attr) => $attr->attribute->name);
                                        @endphp

                                        @if($attributes->isNotEmpty())
                                            <table class="w-full text-sm border border-gray-300 mb-4">

                                                <tbody>
                                                @foreach ($attributes as $label => $values)
                                                    <tr class="border-t text-xs text-gray-800">
                                                        <td class="px-4 py-2 font-semibold w-1/3">{{ $label }}</td>
                                                        <td class="px-4 py-2">
                                                            {{ $values->pluck('value')->unique()->implode(', ') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endif

                                        {!! html_entity_decode($product->description) !!}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Garanție -->
                <div x-data="{ open: false }" class="border-b">
                    <button
                        @click="open = !open"
                        :class="{ 'bg-black text-white': open }"
                        class="w-full px-4 py-3 text-left flex justify-between items-center text-black"
                    >
                        <span class="font-bold text-sm">Garanție</span>
                        <span x-show="!open">+</span>
                        <span x-show="open">-</span>
                    </button>
                    <div x-show="open" class="px-4 py-2 text-black">
                        <p class="text-xs">{{ $product->warranty ?? 'Nu există informații despre garanție.' }}</p>
                    </div>
                </div>

                <!-- Transport -->
                <div x-data="{ open: false }" class="border-b">
                    <button
                        @click="open = !open"
                        :class="{ 'bg-black text-white': open }"
                        class="w-full px-4 py-3 text-left flex justify-between items-center text-black"
                    >
                        <span class="font-bold text-sm">Transport</span>
                        <span x-show="!open">+</span>
                        <span x-show="open">-</span>
                    </button>
                    <div x-show="open" class="px-4 py-2 text-black">
                        <p class="text-xs">Detaliile transportului vor fi disponibile la checkout.</p>
                    </div>
                </div>

                <!-- Returnări -->
                <div x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        :class="{ 'bg-black text-white': open }"
                        class="w-full px-4 py-3 text-left flex justify-between items-center text-black"
                    >
                        <span class="font-bold text-sm">Retur</span>
                        <span x-show="!open">+</span>
                        <span x-show="open">-</span>
                    </button>
                    <div x-show="open" class="px-4 py-2 text-black">
                        <p class="text-xs">Returul este acceptat în termen de 30 de zile de la achiziție.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secțiunea S-ar putea să îți placă -->
        <div class="mt-12 px-2 md:px-0">
            <h2 class="text-2xl font-bold mb-4">S-ar putea să îți placă</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach ($product->category->products->take(4) as $relatedProduct)
                    @if ($relatedProduct->id !== $product->id)
                        <div class="bg-white shadow-md rounded-lg overflow-hidden">
                            <a href="{{ route('product.show', ['slug' => $relatedProduct->slug]) }}">
                                <img
                                    src="{{ $relatedProduct->images[0] ?? '/storage/images/placeholder-images.webp' }}"
                                    alt="{{ $relatedProduct->name }}"
                                    class="w-full h-64 object-cover bg-gray-50"
                                />
                                <p class="relative w-24 -top-2 py-0.5 shadow-2xl bg-black text-xs font-bold text-white text-center">
                                    {{ number_format($relatedProduct->price(), 2) }} lei
                                </p>
                            </a>
                            <div class="p-1 md:p-2 pb-4">
                                <a href="{{ route('product.show', ['slug' => $relatedProduct->slug]) }}" class="text-xs hover:underline leading-3 heightLink font-bold">
                                    {{ Str::limit(strip_tags($relatedProduct->name), 100) }}
                                </a>
                                {{-- Additional Details --}}
                                @if($relatedProduct->stock() < 1)
                                    <div class="text-xs md:mt-2">
                                        <p>Brand: <span class="font-semibold">{{ $relatedProduct->brand_name ?? 'N/A' }}</span></p>
                                        <p class="font-semibold text-red-600">Indisponibil</p>
                                    </div>
                                @endif

                                <!-- Price and Button -->
                                <div class="flex items-center justify-between mt-1 md:mt-4">
                                    <x-simple-link
                                        href="{{ route('product.show', ['slug' => $relatedProduct->slug]) }}"
                                        class="text-xs text-right simpleLink">
                                        vezi produs
                                    </x-simple-link>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div id="zoomGalleryPanel"
             class="fixed inset-0 bg-white z-50 transform translate-x-full transition-transform duration-300 overflow-hidden">
            <div class="flex h-full">
                <!-- Thumbnails -->
                <div class="w-16 md:w-20 p-2 overflow-y-auto border-r border-gray-200 bg-white">
                    <div class="flex flex-col gap-2">
                        @foreach($product->images as $img)
                            <img src="{{ asset($img) }}"
                                 data-large="{{ asset($img) }}"
                                 onclick="changeZoomGalleryImage(this)"
                                 class="w-full h-16 object-cover rounded border cursor-pointer hover:ring-2 zoom-thumb"/>
                        @endforeach
                    </div>
                </div>

                <!-- Main Viewer -->
                <div class="flex-1 flex flex-col items-center justify-center relative">
                    <!-- Make this z-50 and ensure it's visible -->
                    <button onclick="closeZoomGallery()"
                            class="absolute top-4 right-4 text-md text-black z-50 bg-white rounded-full h-10 w-10 px-2 py-0.5 shadow-lg hover:bg-black hover:text-white transition">
                        ✕
                    </button>
                    <div id="zoomImageWrapper"
                         class="max-h-[100vh] w-auto overflow-hidden transition-all duration-300">
                        <img id="zoomGalleryImage"
                             src="{{ asset($product->images[0]) }}"
                             alt="Zoomed {{ $product->name }}"
                             class="object-contain transition-transform duration-300 ease-in-out"
                             style="transform: scale(1); cursor: zoom-in;"/>
                    </div>
                </div>
            </div>
        </div>

        <div id="zoomGalleryBackdrop" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 transition-opacity"
             onclick="closeZoomGallery()"></div>
    </div>

    <script>
        let zoomedIn = false;

        function openZoomGallery() {
            document.getElementById('zoomGalleryPanel').classList.remove('translate-x-full');
            document.getElementById('zoomGalleryBackdrop').classList.remove('hidden');
            zoomedIn = false;
            document.getElementById('zoomGalleryImage').style.transform = 'scale(1)';
            document.getElementById('zoomGalleryImage').style.cursor = 'zoom-in';
        }

        function closeZoomGallery() {
            document.getElementById('zoomGalleryPanel').classList.add('translate-x-full');
            document.getElementById('zoomGalleryBackdrop').classList.add('hidden');
        }

        function changeZoomGalleryImage(thumb) {
            const image = document.getElementById('zoomGalleryImage');
            const newSrc = thumb.getAttribute('data-large');
            image.src = newSrc;
            image.style.transform = 'scale(1)';
            zoomedIn = false;
            image.style.cursor = 'zoom-in';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const zoomImage = document.getElementById('zoomGalleryImage');
            const wrapper = document.getElementById('zoomImageWrapper');

            zoomImage.addEventListener('click', () => {
                zoomedIn = !zoomedIn;

                if (zoomedIn) {
                    // Expand wrapper and enable scrolling
                    wrapper.classList.remove('max-h-[100vh]', 'w-auto', 'overflow-hidden');
                    wrapper.classList.add('w-full', 'h-full', 'overflow-scroll');

                    // Zoom in
                    zoomImage.style.transform = 'scale(2)';
                    zoomImage.style.cursor = 'zoom-out';
                } else {
                    // Reset wrapper
                    wrapper.classList.remove('w-full', 'h-full', 'overflow-scroll');
                    wrapper.classList.add('max-h-[100vh]', 'w-auto', 'overflow-hidden');

                    // Zoom out
                    zoomImage.style.transform = 'scale(1)';
                    zoomImage.style.cursor = 'zoom-in';
                }
            });
        });
    </script>

    <script>
        function updateMainImage(selectedImage) {
            // Update the main image
            document.getElementById("mainImage").src = selectedImage.getAttribute("data-src");

            // Remove border from all thumbnails
            document.querySelectorAll(".thumbnail").forEach(img => {
                img.classList.remove("border-4", "border-black");
            });

            // Add border to the selected thumbnail
            selectedImage.classList.add("border-4", "border-black");
        }
    </script>





@endsection
