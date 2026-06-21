@extends('layouts.base')

@section('content')
    <div class="max-w-7xl mx-auto py-0 md:py-12 md:py-12 px-0 md:pt-8">
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
                    <div class="swiper-pagination bottom-4! absolute left-0 w-full z-20"></div>
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
                             class="thumbnail w-16 h-16 object-contain rounded-2xl shadow-2xl cursor-pointer"
                             data-src="{{ $image }}"
                             onclick="updateMainImage(this)"
                        />
                    @endforeach
                </div>

                <div class="w-full mt-4 p-4 border border-black rounded-lg text-xs font-bold text-left text-black bg-gray-50">
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
            <div class="lg:w-3/5 px-4 md:px-2 pt-2 md:pt-0">
                <div class="w-full">
                    <h1 class="text-xl md:text-3xl font-bold text-gray-800">{{ $product->name }}</h1>
                </div>

                <div class="w-full text-2xl font-extrabold text-right hidden md:block">
                    {{ number_format($product->price(), 2) }} lei
                </div>
                <!-- Brand -->
                <p class="text-sm text-gray-500 md:mt-2 md:mt-0 mb-1 md:mb-2">Categorie: <span class="font-semibold text-black">{{ $product->category->name ?? 'N/A' }}</span></p>
                <p class="text-sm text-gray-500 mb-1 md:mb-2">Cod produs: <span class="font-semibold text-black">TXT-{{ $product->id }}</span></p>
                @php
                    // Color/material from the product_color + product_material pivots.
                    // Same shape: each value carries a ->value.
                    $attributes = collect([
                        'Material' => $product->materials->map(fn($m) => (object) ['value' => $m->name]),
                        'Culoare' => $product->colors->map(fn($c) => (object) ['value' => $c->name]),
                    ])->filter(fn($v) => $v->isNotEmpty());
                @endphp

                @if($attributes->isNotEmpty())

                    @foreach ($attributes as $label => $values)
                        <p class="text-sm text-gray-500 mb-1 md:mb-2">{{ $label }}: <span class="font-semibold text-black">{{ $values->pluck('value')->unique()->implode(', ') }}</span></p>
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

                <!-- Buton adaugare în coș -->
                <div class="flex flex-col gap-4 rounded-2xl mt-4 md:mt-0">

                    <!-- Dynamic Form -->
                    @if($product->type === 'custom')
                        @include('products.product-form-custom', ['product' => $product])
                    @else
                        @include('products.product-form-standard', ['product' => $product])
                    @endif

                </div>

                {{-- Același model, alte dimensiuni (produse-frați). Pur afișare/navigare —
                     nu atinge coșul, prețul sau configuratorul. --}}
                @php $siblings = $product->siblings()->get(); @endphp
                @if ($siblings->isNotEmpty())
                    <div class="mt-6 border-t pt-4">
                        <h3 class="text-base font-bold mb-3">Același model, alte dimensiuni</h3>
                        <div class="flex flex-col gap-2">
                            @foreach ($siblings as $sibling)
                                @php $sibImg = is_array($sibling->images) ? ($sibling->images[0] ?? null) : null; @endphp
                                <a href="{{ route('product.show', ['slug' => $sibling->slug]) }}"
                                   class="flex items-center gap-3 p-2 rounded-lg border border-gray-200 hover:border-black transition">
                                    @if ($sibImg)
                                        <img src="{{ $sibImg }}" alt="{{ strip_tags($sibling->name) }}"
                                             class="w-12 h-12 object-cover rounded flex-shrink-0">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium truncate">{{ strip_tags($sibling->name) }}</div>
                                        @if ($sibling->height)
                                            <div class="text-xs text-gray-500">Înălțime: {{ $sibling->height }} m</div>
                                        @endif
                                    </div>
                                    <div class="text-sm font-semibold whitespace-nowrap">
                                        {{ number_format($sibling->price(), 2) }} lei
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

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
            </div>
        </div>

        <div class="mt-4 md:mt-12 px-4 md:px-0">
            <h2 class="text-xl font-bold mb-4">Detalii produs</h2>
            <div class="border border-black rounded-lg overflow-hidden">
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
                                            $attributes = collect([
                                                'Material' => $product->materials->map(fn($m) => (object) ['value' => $m->name]),
                                                'Culoare' => $product->colors->map(fn($c) => (object) ['value' => $c->name]),
                                            ])->filter(fn($v) => $v->isNotEmpty());
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
                    <div x-show="open" class="px-4 py-4 text-black">
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
                    <div x-show="open" class="px-4 py-4 text-black">
                        <p class="text-xs">Detaliile transportului vor fi disponibile la checkout.</p>
                    </div>
                </div>

                <!-- Retur -->
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
                    <div x-show="open" class="px-4 py-4 text-black">
                        <p class="text-xs">Returul este acceptat în termen de 30 de zile de la achiziție.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secțiunea S-ar putea să îți placă -->
        <div class="mt-12 px-4 md:px-0">
            <h2 class="text-2xl font-bold mb-4">S-ar putea să îți placă</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach ($product->category->products->take(5) as $relatedProduct)
                    @if ($relatedProduct->id !== $product->id)
                        <div wire:key="product-{{ $relatedProduct->id }}" class="">
                            <div class="bg-white overflow-hidden">
                                <!-- Product Image -->
                                <div class="overflow-hidden">
                                    <div class="w-full relative">
                                        <livewire:favorites-button :product-id="$relatedProduct->id" wire:key="favorites-{{ $relatedProduct->id }}" />
                                        <div id="swiper-{{ $relatedProduct->id }}" class="swiper-container">
                                            <div class="swiper-wrapper">
                                                @foreach($relatedProduct->detail_images ?? $relatedProduct->images as $image)
                                                    <div class="swiper-slide shadow-lg">
                                                        <a href="{{ route('product.show', ['slug' => $relatedProduct->slug]) }}">
                                                            <img src="{{ asset($image) }}"
                                                                 alt="{{ $relatedProduct->name }}"
                                                                 class="w-full h-auto object-cover p-0 rounded-2xl bg-white" />
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="swiper-pagination"></div>
                                        </div>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function () {
                                                new Swiper('#swiper-{{ $relatedProduct->id }}', {
                                                    slidesPerView: 1,
                                                    spaceBetween: 10,
                                                    pagination: {
                                                        el: '.swiper-pagination',
                                                        clickable: true,
                                                    },
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>

                                <!-- Product Details -->
                                <div class="p-2">
                                    <a href="{{ route('product.show', ['slug' => $relatedProduct->slug]) }}"
                                       class="text-sm uppercase hover:underline leading-5 font-bold line-clamp-3 h-16">
                                        {{ strip_tags($relatedProduct->name) }}
                                    </a>
                                    <div class="text-xs mt-2">
                                        @php
                                            $attributes = collect([
                                                'Material' => $product->materials->map(fn($m) => (object) ['value' => $m->name]),
                                                'Culoare' => $product->colors->map(fn($c) => (object) ['value' => $c->name]),
                                            ])->filter(fn($v) => $v->isNotEmpty());
                                        @endphp

                                            <!-- Display Material and Color -->
                                        <div class="text-xs mt-4 space-y-1">
                                            @if ($attributes->has('Material'))
                                                <div>
                                                    <span class="font-semibold text-gray-700">Material:</span>
                                                    {{ $attributes['Material']->pluck('value')->implode(', ') }}
                                                </div>
                                            @endif

                                            @if (!empty($product->colors_with_css))
                                                <div class="flex flex-wrap items-center text-xs text-gray-700">
                                                    <span class="font-semibold mr-2">Culoare:</span>
                                                    @foreach ($product->colors_with_css as $color)
                                                        <span class="flex items-center mr-4 mb-2 pt-2">
                                                                        <span class="w-5 h-5 rounded-full border border-gray-300 mr-1"
                                                                              style="background-color: {{ $color['css'] }}"></span>
                                                                        <span>{{ $color['name'] }}</span>
                                                                    </span>
                                                    @endforeach
                                                </div>

                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between mt-4">
                                        <p class="text-xs sm:text-sm font-extrabold text-gray-800">
                                            {{ number_format($relatedProduct->price(), 2) }} lei / m <br/>
                                        </p>
                                        <x-simple-link href="{{ route('product.show', ['slug' => $relatedProduct->slug]) }}"
                                                       class="text-xs text-right simpleLink font-extrabold">
                                            Vezi produsul
                                        </x-simple-link>
                                    </div>
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
                         class="max-h-screen w-auto overflow-hidden transition-all duration-300">
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
                    wrapper.classList.remove('max-h-screen', 'w-auto', 'overflow-hidden');
                    wrapper.classList.add('w-full', 'h-full', 'overflow-scroll');

                    // Zoom in
                    zoomImage.style.transform = 'scale(2)';
                    zoomImage.style.cursor = 'zoom-out';
                } else {
                    // Reset wrapper
                    wrapper.classList.remove('w-full', 'h-full', 'overflow-scroll');
                    wrapper.classList.add('max-h-screen', 'w-auto', 'overflow-hidden');

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
            // document.querySelectorAll(".thumbnail").forEach(img => {
            //     img.classList.remove("border-4", "border-black");
            // });
            //
            // // Add border to the selected thumbnail
            // selectedImage.classList.add("border-4", "border-black");
        }
    </script>





@endsection
