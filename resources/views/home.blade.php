@extends('layouts.base')

@section('content')

    <div class="relative w-full h-[90vh] pt-16 md:pt-32 overflow-hidden"
         x-data="{ playing: false, progress: 0, finished: false }"
         x-init="
        const video = $refs.introVideo;
        video.addEventListener('loadedmetadata', () => {
            video.currentTime = 2;
        });
        video.addEventListener('play', () => { playing = true });
        video.addEventListener('pause', () => { playing = false });
        video.addEventListener('ended', () => { finished = true });
        video.addEventListener('timeupdate', () => {
            progress = (video.currentTime / video.duration) * 100;
        });
     ">

    <!-- Background Image shown before/after video -->
{{--        <div x-show="!playing && finished" x-transition--}}
{{--             class="absolute inset-0 w-full h-full bg-cover bg-center z-0"--}}
{{--             style="background-image: url('{{ asset('storage/images/video_bg.png') }}');">--}}
{{--        </div>--}}

        <!-- Video background -->
        <video
            x-ref="introVideo"
            class="absolute inset-0 w-full h-full object-cover z-0"
            autoplay
            muted
            playsinline
            @canplay="$refs.introVideo.play()"
{{--            poster="{{ asset('storage/images/video_bg.png') }}"--}}
        >
            <source src="{{ asset('storage/videos/video_short.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Teal overlay -->
        <div class="absolute inset-0 bg-[#000000]/15 md:bg-[#000000]/15 z-10"></div>

        <!-- CTA -->
        <div class="absolute inset-0 z-20 pointer-events-none">
            <div class="relative w-full h-full">
                <div class="max-w-7xl mx-auto h-full flex items-center justify-start px-4 md:p-0">
                    <div class="pointer-events-auto md:bg-white backdrop-blur-sm md:backdrop-blur-md md:shadow-xl rounded-xl p-6 md:p-8 max-w-md md:mt-32">
                        <p class="text-xs uppercase tracking-wide text-white font-bold md:text-black mb-2">Noutate</p>
                        <h2 class="text-2xl md:text-3xl font-bold textColor mb-4 leading-tight">Descoperă noua colecție</h2>
                        <p class="text-sm text-white md:text-gray-600 mb-6">
                            Înfrumusețează-ți casa cu textile premium, perdele elegante și draperii moderne.
                            Colecția noastră este creată pentru a aduce rafinament, confort și stil fiecărui colț al locuinței tale.
                        </p>
                        <a href="#"
                           class="inline-block bg-black text-white text-sm font-semibold px-5 py-2 rounded-md transition">
                            CUMPĂRĂ ACUM
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="absolute bottom-0 left-0 w-full h-0.5 z-30 bg-white/30">
            <div class="h-full bg-black transition-all duration-75" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>

    @php
        // Map each parent category name to its corresponding icon URL.
        $categoryIcons = [
            'Perdele' => asset('storage/images/icons/perdele.png'),
            'Draperii' => asset('storage/images/icons/draperii.png'),
            'Covoare' => asset('storage/images/icons/covoare.png'),
            'Lenjerii de pat' => asset('storage/images/icons/lenjerii.png'),
            'Accesorii' => asset('storage/images/icons/accesorii.png'),
            'Galerii & Sine' => asset('storage/images/icons/sine.png'),


        ];

        $defaultIcon = asset('storage/images/icons/default.png');
    @endphp

    <div class="max-w-7xl mx-auto mt-8 md:mt-16 mb-8 px-4 md:px-0 relative overflow-hidden">
        <h2 class="text-3xl font-bold text-left textColor">
            Cele mai rafinate textile și perdele pentru casa ta
        </h2>
        <p class="mt-4 mb-8 md:mb-16 text-left text-gray-600">
            La <strong>TEXTURRA HOME SRL</strong> punem accent pe calitate, design și funcționalitate. Oferim o selecție atent aleasă de <strong>perdele, draperii și textile premium</strong> pentru casă, perfecte pentru a-ți transforma locuința într-un spațiu cald și elegant. Cu experiență în domeniu din 2017, venim în întâmpinarea clienților noștri cu produse moderne, la prețuri competitive și livrare rapidă.
        </p>

        <div class="max-w-7xl mx-auto px:0 md:px-4 py-2 pb-0 md:py-8 space-y-8">
            <div class="w-full overflow-hidden">
                <!-- Scrollable Container -->
                <div id="category-scroll" class="flex flex-nowrap gap-4 overflow-x-auto scroll-smooth pb-4 custom-scrollbar px-0">
                    @foreach ($topCategories as $category)
                        <div class="shrink-0 w-24 md:w-36 flex flex-col items-center text-center gap-4 transition duration-200 group">
                            <a href="{{ route('products.category', ['slug' => $category->slug]) }}">
                                <img
                                    src="{{ $categoryIcons[$category->name] ?? $defaultIcon }}"
                                    alt="{{ $category->name }}"
                                    class="w-24 md:w-36 p-6 object-cover mx-auto transition-transform duration-300 group-hover:scale-110 bg-gray-50 rounded-full shadow-md border border-gray-200"
                                />
                            </a>
                            <h2 class="text-sm font-bold uppercase text-gray-800">
                                <a href="{{ route('products.category', ['slug' => $category->slug]) }}" class="hover:underline">
                                    {{ $category->name }}
                                </a>
                            </h2>
                        </div>
                    @endforeach
                </div>

                <!-- Scroll Hint for Mobile -->
                <div class="flex justify-end mt-2 pr-4 md:hidden">
                    <img src="{{ asset('storage/images/swipe.svg') }}" alt="Swipe right" class="w-8 h-8 opacity-80 animate-pulse" id="category-scroll-hint">
                </div>
            </div>

            <style>
                .custom-scrollbar::-webkit-scrollbar {
                    display: none;
                }
                .custom-scrollbar {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                }
            </style>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const scrollContainer = document.getElementById("category-scroll");
                    const scrollHint = document.getElementById("category-scroll-hint");

                    if (scrollContainer && scrollHint) {
                        scrollContainer.addEventListener("scroll", function () {
                            if (this.scrollLeft > 10) {
                                scrollHint.style.opacity = "0";
                            } else {
                                scrollHint.style.opacity = "1";
                            }
                        });
                    }
                });
            </script>

        </div>
    </div>


    <!-- Swiper JS Initialization -->
    <script>
        var swiper = new Swiper('.swiper-container2', {
            // Enable grid layout:
            grid: {
                rows: 1,             // 2 rows
                fill: 'row',         // Fill items row by row
            },
            slidesPerView: 2,      // On mobile: 2 columns
            spaceBetween: 10,
            slidesPerGroup: 2,     // Move 2 slides at a time on mobile
            breakpoints: {
                768: {               // e.g., from tablet size upward
                    slidesPerView: 48,  // 4 columns on desktop
                    slidesPerGroup: 4, // Move 4 slides at a time
                    spaceBetween: 20,
                },
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    </script>


    <!-- Parallax Section -->
    <div class="relative bg-fixed bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('storage/images/bg.jpg') }}');min-height: 500px;">
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>

        <!-- Content Container -->
        <div class="relative z-10 flex flex-col items-center justify-center text-center text-white px-4 py-12 md:py-24">
            <h2 class="text-3xl md:text-5xl font-bold mb-2">
                Intră în universul TEXTURRA
            </h2>
            <p class="text-white text-lg md:text-2xl mb-10">
                Textile pentru casă create cu stil și pasiune.
            </p>

            <!-- Metrics Row -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8 max-w-6xl mx-auto">
                <!-- Ani de experiență -->
                <div>
                    <span class="block text-4xl md:text-5xl font-extrabold">11+</span>
                    <span class="block text-sm md:text-base uppercase tracking-wide">Ani de experiență</span>
                </div>

                <!-- Produse vândute -->
                <div>
                    <span class="block text-4xl md:text-5xl font-extrabold">500.000+</span>
                    <span class="block text-sm md:text-base uppercase tracking-wide">Produse vândute</span>
                </div>

                <!-- Consultanți -->
                <div>
                    <span class="block text-4xl md:text-5xl font-extrabold">10+</span>
                    <span class="block text-sm md:text-base uppercase tracking-wide">Consultanți specializați</span>
                </div>

                <!-- Parteneri comerciali -->
                <div>
                    <span class="block text-4xl md:text-5xl font-extrabold">120+</span>
                    <span class="block text-sm md:text-base uppercase tracking-wide">Parteneri comerciali</span>
                </div>

                <!-- Magazine fizice -->
                <div>
                    <span class="block text-4xl md:text-5xl font-extrabold">3</span>
                    <span class="block text-sm md:text-base uppercase tracking-wide">Magazine fizice</span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-0 py-8 md:mt-8">
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Block Example -->
            @php
                $infoBlocks = [
                    [
                        'icon' => 'transport.svg',
                        'title' => 'Transport gratuit',
                        'text' => 'De la 500 lei',
                        'alt' => 'Transport Gratuit'
                    ],
                    [
                        'icon' => 'livrare.svg',
                        'title' => 'Livrare rapidă',
                        'text' => '24h, 48h la distanțe considerabile',
                        'alt' => 'Livrare Rapidă'
                    ],
                    [
                        'icon' => 'consultanta.svg',
                        'title' => 'Consultanță pe produs',
                        'text' => 'Vrei un detaliu? Sună-ne!',
                        'alt' => 'Consultanță pe produs'
                    ],
                    [
                        'icon' => 'sliders.svg',
                        'title' => 'Personalizare produse',
                        'text' => 'Online și gratuit',
                        'alt' => 'Personalizare produse'
                    ]
                ];
            @endphp

            @foreach ($infoBlocks as $block)
                <div class="flex flex-col md:flex-row items-center bg-gray-100 rounded-lg p-4 shadow-md text-center md:text-left">
                    <img
                        src="{{ asset('storage/images/icons/' . $block['icon']) }}"
                        alt="{{ $block['alt'] }}"
                        class="w-10 h-10 md:w-12 md:h-12 mb-2 md:mb-0 md:mr-4"
                    />
                    <div>
                        <h3 class="text-sm md:text-base font-semibold text-gray-800">{{ $block['title'] }}</h3>
                        <p class="text-xs md:text-sm text-gray-600">{{ $block['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-4 md:py-12 px-6 md:px-0 bg-white">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
            Explorează Selecția Noastră de Textile pentru Casă
        </h2>
        <p class="text-sm md:text-base text-gray-600 mb-2 md:mb-8">
            Fie că îți dorești perdele elegante, draperii funcționale sau lenjerii de pat confortabile, colecția <strong>texturra.ro</strong> este creată pentru a aduce rafinament și stil în locuința ta. Punem accent pe calitate, design modern și prețuri corecte — totul pentru ca tu să transformi fiecare cameră într-un spațiu primitor și personalizat.
        </p>

        <div class="flex flex-wrap md:flex-nowrap">

            <!-- Main Content (4/5) -->
            <main class="w-full md:w-5/5 px-0 md:px-2 md:px-0">
                <div class="">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 mt-8">
                        @forelse ($products as $product)
                            @if ($loop->index === 1)
                                <!-- TEXT BLOCK instead of second product -->
                                <div class="p-6 rounded-2xl flex flex-col justify-top items-start border border-black">
                                    <h1 class="text-2xl font-extrabold mb-2">Colecția Diafan – Vară 2025</h1>
                                    <p class="text-sm text-gray-700 mb-4">Inspirată de fluiditatea aerului și lumina verii, această colecție aduce un omagiu eleganței discrete. Țesături vaporoase, transparențe delicate și accente naturale care transformă orice spațiu într-un sanctuar al rafinamentului. .</p>
{{--                                    <img src="{{ asset('storage/images/motive.png') }}" alt="Lookbook 1" class="rounded-2xl mb-4">--}}
                                    <x-simple-link href="/colectie/sons-of-zeus" class="text-xl uppercase font-bold underline text-center ">Vezi colecția completă</x-simple-link>
                                </div>
                            @elseif ($loop->index === 6)
                                <!-- VIDEO BLOCK instead of 3th product -->
                                <div class="overflow-hidden relative rounded-2xl border border-black">
                                    <video autoplay muted loop playsinline class="w-full h-auto object-cover rounded-2xl">
                                        <source src="{{ asset('storage/videos/teaser.mp4') }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>

                                    <!-- Overlay Text -->
                                    <div class="absolute inset-x-0 top-0 p-4 bg-linear-to-t from-transparent to-black/80 text-white">
                                        <h2 class="text-lg sm:text-xl font-bold">Lumina verii. Textură diafană.</h2>
                                        <p class="text-sm sm:text-base mt-1">Lasă razele să danseze prin perdelele noii colecții.
                                            Urmărește clipul nostru exclusiv – delicatețea prinde viață în mișcare.</p>
                                    </div>
                                </div>

                            @elseif ($loop->index === 8)
                                <!-- VIDEO BLOCK instead of 3th product -->
                                <div class="overflow-hidden relative rounded-2xl">
                                    <video autoplay muted loop playsinline class="w-full h-auto object-cover rounded-2xl">
                                        <source src="{{ asset('storage/videos/teaser3.MP4') }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>

                                    <!-- Overlay Text -->
                                    <div class="absolute inset-x-0 top-0 p-4 bg-linear-to-t from-transparent to-black/80 text-white">
                                        <h2 class="text-lg sm:text-xl font-bold">Vibrația verii în mișcare</h2>
                                        <p class="text-sm sm:text-base mt-1">Urmărește colecția prinde viață în clipul nostru exclusiv.</p>
                                    </div>
                                </div>
                            @else
                                <!-- PRODUCT CARD -->
                                <div wire:key="product-{{ $product->id }}" class="">
                                    <div class="bg-white overflow-hidden">
                                        <!-- Product Image -->
                                        <div class="overflow-hidden">
                                            <div class="w-full relative">
                                                <livewire:favorites-button :product-id="$product->id" wire:key="favorites-{{ $product->id }}" />
                                                <div id="swiper-{{ $product->id }}" class="swiper-container">
                                                    <div class="swiper-wrapper">
                                                        @foreach($product->detail_images ?? $product->images as $image)
                                                            <div class="swiper-slide shadow-lg">
                                                                <a href="{{ route('product.show', ['slug' => $product->slug]) }}">
                                                                    <img src="{{ asset($image) }}"
                                                                         alt="{{ $product->name }}"
                                                                         class="w-full h-auto object-cover p-0 rounded-2xl bg-white" />
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="swiper-pagination"></div>
                                                </div>
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function () {
                                                        new Swiper('#swiper-{{ $product->id }}', {
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
                                            <a href="{{ route('product.show', ['slug' => $product->slug]) }}"
                                               class="text-sm uppercase hover:underline leading-5 font-bold line-clamp-3 h-16">
                                                {{ strip_tags($product->name) }}
                                            </a>
                                            <div class="text-xs mt-2">
                                                @php
                                                    $attributes = $product->variations
                                                        ->pluck('attributeValues')
                                                        ->flatten()
                                                        ->unique('id')
                                                        ->filter(fn($attr) => $attr->attribute)
                                                        ->groupBy(fn($attr) => $attr->attribute->name);
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
                                                <p class="text-xs sm:text-lg font-extrabold text-gray-800">
                                                    {{ number_format($product->price(), 2) }} lei / m <br/>
                                                </p>
                                                <x-simple-link href="{{ route('product.show', ['slug' => $product->slug]) }}"
                                                               class="text-xs text-right simpleLink font-extrabold">
                                                    Vezi produsul
                                                </x-simple-link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <p class="col-span-full text-center text-gray-500">No products found in this category.</p>
                        @endforelse
                    </div>


                </div>
            </main>
        </div>
    </div>

    <!-- Parallax Section -->
    <div class="relative bg-fixed bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('storage/images/bg_map.png') }}');min-height: 500px;">
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>

        <!-- Content Container -->
        <div class="relative z-10 flex flex-col items-center justify-center text-center text-white py-12 md:py-24">
            <div class="max-w-7xl mx-auto w-full px-6 md:px-0">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-white mb-10">
                    Găsește-ne în magazinele fizice
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <!-- Location 1 -->
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center">
                        <div class="w-16 h-16 mb-4 flex items-center justify-center bg-black text-white rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c1.656 0 3-1.344 3-3s-1.344-3-3-3-3 1.344-3 3 1.344 3 3 3zm0 0c2.761 0 5 2.239 5 5 0 1.657-2.239 4-5 4s-5-2.343-5-4c0-2.761 2.239-5 5-5z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">George Coșbuc nr.13</h3>
                        <p class="text-sm text-gray-600 mb-4">Zona Halelor Centrale, Ploiești</p>
                        <a href="https://www.google.com/maps/search/?api=1&query=George+Cosbuc+13+Ploiesti" target="_blank"
                           class="inline-block px-4 py-2 bg-black text-white text-sm font-semibold rounded hover:bg-gray-800 transition">
                            Vezi pe Google Maps
                        </a>
                    </div>

                    <!-- Location 2 -->
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center">
                        <div class="w-16 h-16 mb-4 flex items-center justify-center bg-black text-white rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9 6 9-6-9-6-9 6z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15l9 6 9-6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Omnia Winmark, Etaj 2</h3>
                        <p class="text-sm text-gray-600 mb-4">Ploiești – Magazin în centrul comercial</p>
                        <a href="https://www.google.com/maps/search/?api=1&query=Omnia+Winmark+Ploiesti" target="_blank"
                           class="inline-block px-4 py-2 bg-black text-white text-sm font-semibold rounded hover:bg-gray-800 transition">
                            Vezi pe Google Maps
                        </a>
                    </div>

                    <!-- Location 3 -->
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center">
                        <div class="w-16 h-16 mb-4 flex items-center justify-center bg-black text-white rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Magazin Lems, Etaj 1</h3>
                        <p class="text-sm text-gray-600 mb-4">Blejoi – în incinta showroom-ului Lems</p>
                        <a href="https://www.google.com/maps/search/?api=1&query=Magazin+Lems+Blejoi" target="_blank"
                           class="inline-block px-4 py-2 bg-black text-white text-sm font-semibold rounded hover:bg-gray-800 transition">
                            Vezi pe Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <div class="max-w-7xl mx-auto px-6 md:px-0 py-12">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">
            Caută produse după culoare
        </h2>
        <div class="grid grid-cols-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-6">

            @foreach($colorGroups as $group)
                <a href=""
                   class="group block text-center transition transform hover:scale-105">
                    <div class="w-full aspect-square bg-gray-100 rounded-xl overflow-hidden shadow">
                        <img src="{{ asset($group->image_path) }}"
                             alt="{{ $group->name }}"
                             class="object-cover w-full h-full transition duration-300 group-hover:opacity-90" />
                    </div>
                    <span class="mt-2 block text-sm font-semibold text-gray-700 group-hover:underline">
                    {{ $group->name }}
                </span>
                </a>
            @endforeach

        </div>
    </div>

    <section class="w-full bg-white border-t border-black pt-12 pb-4 mb-12">
        <div class="max-w-7xl mx-auto px-6 md:px-0 md:py-16">

            <!-- CURTAIN BLOCK -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start mb-24">
                <!-- TEXT BLOCK -->
                <div class="col-span-1 flex flex-col">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4 leading-tight">Perdele, draperii <br/> la comandă</h1>
                    <p class="text-base md:text-lg mb-6">
                        Alege materialul preferat, dimensiunile exacte, tipul de rejansă și creează soluția perfectă pentru spațiul tău.
                        Produsele noastre sunt realizate manual, special pentru tine.
                    </p>
                    <a href="{{ route('products.category', ['slug' => 'perdele']) }}"
                       class="bg-black text-white font-semibold px-6 py-3 rounded-md hover:bg-gray-800 transition w-fit">
                        Configurează perdeaua ta
                    </a>
                </div>

                <!-- BEFORE/AFTER SLIDER -->
                <div class="col-span-2 relative w-full h-[400px] md:h-[500px] overflow-hidden border border-black rounded-lg">
                    <div class="relative w-full h-full before-after-slider">
                        <img src="{{ asset('storage/images/tex_3.png') }}" class="absolute top-0 left-0 w-full h-full object-cover z-10 after-img" />
                        <img src="{{ asset('storage/images/tex_2.png') }}" class="absolute top-0 left-0 w-full h-full object-cover z-20 before-img" />
                        <div class="handle z-30"></div>
                    </div>
                </div>
            </div>

            <!-- RUG BLOCK -->
            <div class="flex flex-col-reverse md:grid md:grid-cols-3 gap-12 items-start mt-0 md:mt-8">



            <!-- BEFORE/AFTER SLIDER -->
                <div class="col-span-2 relative w-full h-[400px] md:h-[500px] overflow-hidden border border-black rounded-lg">
                    <div class="relative w-full h-full before-after-slider">
                        <img src="{{ asset('storage/images/rug_after.png') }}" class="absolute top-0 left-0 w-full h-full object-cover z-10 after-img" />
                        <img src="{{ asset('storage/images/rug_before.png') }}" class="absolute top-0 left-0 w-full h-full object-cover z-20 before-img" />
                        <div class="handle z-30"></div>
                    </div>
                </div>

                <!-- TEXT BLOCK -->
                <div class="col-span-1 flex flex-col">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4 leading-tight">Covoare elegante <br/> pentru orice spațiu</h1>
                    <p class="text-base md:text-lg mb-6">
                        Completează atmosfera cu un covor care se potrivește perfect. Alege textura, dimensiunea și modelul potrivit pentru dormitor, living sau birou.
                    </p>
                    <a href="{{ route('products.category', ['slug' => 'covoare']) }}"
                       class="bg-black text-white font-semibold px-6 py-3 rounded-md hover:bg-gray-800 transition w-fit">
                        Vezi colecția de covoare
                    </a>
                </div>
            </div>

        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.before-after-slider').forEach(slider => {
                const beforeImg = slider.querySelector('.before-img');
                const handle = slider.querySelector('.handle');
                let isDragging = false;

                const updateSlider = (x) => {
                    const rect = slider.getBoundingClientRect();
                    const offsetX = Math.min(Math.max(0, x - rect.left), rect.width);
                    const percent = (offsetX / rect.width) * 100;
                    beforeImg.style.clipPath = `inset(0 ${100 - percent}% 0 0)`;
                    handle.style.left = `${percent}%`;
                };

                handle.addEventListener('mousedown', () => isDragging = true);
                document.addEventListener('mouseup', () => isDragging = false);
                document.addEventListener('mousemove', e => isDragging && updateSlider(e.clientX));

                handle.addEventListener('touchstart', () => isDragging = true);
                document.addEventListener('touchend', () => isDragging = false);
                document.addEventListener('touchmove', e => isDragging && updateSlider(e.touches[0].clientX));
            });
        });
    </script>




    <!-- Initialize Swiper -->
    <script>
        var swiper = new Swiper('.swiper-container', {
            loop: true,
            autoplay: {
                delay: 100000, // 10s
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            // optional: on slide change, reset & animate progress bar
        });

        function resetProgress() {
            const progressBar = document.getElementById('progressBar');
            progressBar.style.transition = 'none';
            progressBar.style.width = '0%';
        }

        function animateProgress() {
            const progressBar = document.getElementById('progressBar');
            setTimeout(() => {
                progressBar.style.transition = 'width 10s linear';
                progressBar.style.width = '100%';
            }, 50);
        }

        swiper.on('slideChangeTransitionStart', () => {
            resetProgress();
        });
        swiper.on('slideChangeTransitionEnd', () => {
            animateProgress();
        });

        // Initialize progress on first load
        document.addEventListener("DOMContentLoaded", () => {
            animateProgress();
        });

    </script>


@endsection
