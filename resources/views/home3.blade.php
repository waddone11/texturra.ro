@extends('layouts.base')

@section('content')

    <div class="relative w-full h-[90vh] pt-16 md:pt-32 overflow-hidden" x-data="{ playing: false, progress: 0 }"
         x-init="
        const video = $refs.introVideo;
        video.addEventListener('loadedmetadata', () => {
            video.currentTime = 2;
        });
        video.addEventListener('play', () => { playing = true });
        video.addEventListener('pause', () => { playing = false });
        video.addEventListener('timeupdate', () => {
            progress = (video.currentTime / video.duration) * 100;
        });
    ">
        <!-- Video background -->
        <video
            x-ref="introVideo"
            class="absolute inset-0 w-full h-full object-cover z-0"
            autoplay
            muted
            playsinline
            @canplay="$refs.introVideo.play()"
        >
            <source src="{{ asset('storage/videos/video_desk.MP4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Teal overlay -->
        <div class="absolute inset-0 bg-[#000000]/15 md:bg-[#000000]/45 z-10"></div>

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

        ];

        $defaultIcon = asset('storage/images/icons/default.png');
    @endphp

    <div class="max-w-7xl mx-auto mt-8 md:mt-16 mb-8 px-6 md:px-0 relative overflow-hidden">
        <h2 class="text-3xl font-bold text-left textColor">
            Cele mai rafinate textile și perdele pentru casa ta
        </h2>
        <p class="mt-4 mb-8 md:mb-16 text-left text-gray-600">
            La <strong>TEXTURRA HOME SRL</strong> punem accent pe calitate, design și funcționalitate. Oferim o selecție atent aleasă de <strong>perdele, draperii și textile premium</strong> pentru casă, perfecte pentru a-ți transforma locuința într-un spațiu cald și elegant. Cu experiență în domeniu din 2017, venim în întâmpinarea clienților noștri cu produse moderne, la prețuri competitive și livrare rapidă.
        </p>

        <!-- Swiper Container -->
        <div class="swiper-container2 max-w-7xl mx-auto pb-4 relative">
            <div class="swiper-wrapper min-h-[320px]">
                @foreach ($topCategories as $category)
                    <div class="swiper-slide">
                        <div class="p-4 transition duration-200 min-h-[280px] flex flex-col items-center text-center gap-4 aspect-square bg-gray-100 rounded-xl shadow-xl">

                            <!-- Image -->
                            <a href="{{ route('products.category', ['slug' => $category->slug]) }}">
                                <img
                                    src="{{ asset($category->image ?? 'storage/images/icons/default.svg') }}"
                                    alt="{{ $category->name }}"
                                    class="w-32 h-32 object-contain mx-auto"
                                />
                            </a>

                            <!-- Category name -->
                            <h2 class="text-md font-bold uppercase text-gray-800">
                                <a href="{{ route('products.category', ['slug' => $category->slug]) }}" class="hover:underline">
                                    {{ $category->name }}
                                </a>
                            </h2>

                            <!-- Subcategories -->
                            @if ($category->children->isNotEmpty())
                                <ul class="flex flex-wrap justify-center gap-2 min-h-24">
                                    @foreach ($category->children->take(3) as $child)
                                        <li>
                                            <a href="{{ route('products.category', ['slug' => $child->slug]) }}"
                                               class="inline-block bg-black text-white text-xs font-semibold px-3 py-1 rounded-full hover:bg-gray-800 transition">
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                    @endforeach

                                    @if ($category->children->count() > 3)
                                        <li>
                                            <span class="inline-block bg-gray-400 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                                <a href="{{ route('products.category', ['slug' => $category->slug]) }}" class="hover:underline">
                                                    + altele...
                                                </a>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="relative w-full h-12 mt-4">

            <div class="swiper-pagination mt-4"></div>
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
                    slidesPerView: 4,  // 4 columns on desktop
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
                    <span class="block text-4xl md:text-5xl font-extrabold">5+</span>
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
                    <span class="block text-4xl md:text-5xl font-extrabold">2</span>
                    <span class="block text-sm md:text-base uppercase tracking-wide">Magazine fizice</span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 md:px-0 py-8 mt-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">

            <!-- Transport Gratuit -->
            <div class="flex items-center bg-gray-100 rounded-lg p-4 shadow-lg">
                <img
                    src="{{ asset('storage/images/icons/transport.svg') }}"
                    alt="Transport Gratuit"
                    class="w-12 h-12 mr-4"
                />
                <div>
                    <h3 class="text-sm md:text-base font-semibold text-gray-800">Transport gratuit</h3>
                    <p class="text-xs md:text-sm text-gray-600">De la 500 lei</p>
                </div>
            </div>

            <!-- Livrare Rapidă -->
            <div class="flex items-center bg-gray-100 rounded-lg p-4 shadow-sm">
                <img
                    src="{{ asset('storage/images/icons/livrare.svg') }}"
                    alt="Livrare Rapidă"
                    class="w-12 h-12 mr-4"
                />
                <div>
                    <h3 class="text-sm md:text-base font-semibold text-gray-800">Livrare rapidă</h3>
                    <p class="text-xs md:text-sm text-gray-600">24h, 48h la distanțe considerabile</p>
                </div>
            </div>

            <!-- Consultanță pe produs -->
            <div class="flex items-center bg-gray-100 rounded-lg p-4 shadow-sm">
                <img
                    src="{{ asset('storage/images/icons/consultanta.svg') }}"
                    alt="Consultanță pe produs"
                    class="w-12 h-12 mr-4"
                />
                <div>
                    <h3 class="text-sm md:text-base font-semibold text-gray-800">Consultanță pe produs</h3>
                    <p class="text-xs md:text-sm text-gray-600">Vrei un detaliu? Sună-ne!</p>
                </div>
            </div>

            <!-- Personalizare Ambalaje -->
            <div class="flex items-center bg-gray-100 rounded-lg p-4 shadow-sm">
                <img
                    src="{{ asset('storage/images/icons/sliders.svg') }}"
                    alt="Personalizare produse"
                    class="w-12 h-12 mr-4"
                />
                <div>
                    <h3 class="text-sm md:text-base font-semibold text-gray-800">Personalizare produse</h3>
                    <p class="text-xs md:text-sm text-gray-600">Online și gratuit</p>
                </div>
            </div>

        </div>
    </div>

    <div class="max-w-7xl mx-auto py-12 px-6 md:px-0 bg-white">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
            Explorează Selecția Noastră de Textile pentru Casă
        </h2>
        <p class="text-sm md:text-base text-gray-600 mb-8">
            Fie că îți dorești perdele elegante, draperii funcționale sau lenjerii de pat confortabile, colecția <strong>texturra.ro</strong> este creată pentru a aduce rafinament și stil în locuința ta. Punem accent pe calitate, design modern și prețuri corecte — totul pentru ca tu să transformi fiecare cameră într-un spațiu primitor și personalizat.
        </p>

        <div class="flex flex-wrap md:flex-nowrap">

            <!-- Main Content (4/5) -->
            <main class="w-full md:w-5/5 px-2 md:px-0">
                <div class="">

                    <!-- Product Listing -->
                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-8 mt-8">
                        @forelse ($products as $product)
                            <div wire:key="product-{{ $product->id }}">
                                <div class="bg-white overflow-hidden">
                                    <!-- Product Image -->
                                    <div class="w-full relative">
                                        <livewire:favorites-button :product-id="$product->id" wire:key="favorites-{{ $product->id }}" />
                                        <a href="{{ route('product.show', ['slug' => $product->slug ?? $product->slug]) }}" class="hover:underline">
                                            <img
                                                src="{{ asset($product->images[0] ?? 'storage/images/placeholder_product.webp') }}"
                                                alt="{{ $product->name }}"
                                                class="w-full object-cover p-0 rounded-xl bg-gray-100 bg-f4f4f5 shadow-sm border border-gray-300"
                                            />
                                        </a>

                                        @php
                                            $originalPrice = $product->price ?? 0;
                                            $discountedPrice = $product->price();
                                            $discountPercentage = $originalPrice > 0 ? round((($originalPrice - $discountedPrice) / $originalPrice) * 100) : 0;
                                        @endphp

                                        @if($originalPrice > $discountedPrice)
                                            <div class="w-full h-6 py-1 bg-black px-2 textSuperSmall font-extrabold text-white flex items-center space-x-2">
                                                <span>Discount:</span>
                                                <span class="text-red-600">-{{ $discountPercentage }}%</span>
                                                <span class="line-through opacity-60">
                                                    {{ number_format($originalPrice, 2) }} lei</span>
                                                <span class="text-white">Acum: {{ number_format($discountedPrice, 2) }} lei</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Details -->
                                    <div class="p-2 md:py-4 md:px-2">
                                        <a href="{{ route('product.show', ['slug' => $product->slug]) }}" class="text-xs md:text-sm hover:underline leading-4 heightLink font-bold">
                                            {{ Str::limit(strip_tags($product->name), 100) }}
                                        </a>
                                        <!-- Additional Details -->
                                        <div class="text-xs mt-2">
                                            <p class="relative -left-4 {{ $product->stock() > 0 ? 'bg-green-500' : 'bg-red-600' }}  w-4/5 md:w-1/2 text-white pl-4 rounded-r-xl">Stoc:
                                                <span class="font-semibold {{ $product->stock() > 0 ? 'text-white' : 'text-white' }}">
                                                    {{ $product->stock()  > 0 ? 'Disponibil' : 'Indisponibil' }}
                                                </span>
                                            </p>
                                        </div>

                                        <!-- Price and Button -->
                                        <div class="flex items-center justify-between mt-4">
                                            <p class="text-xs sm:text-lg font-extrabold text-gray-800">
                                                {{ number_format($product->price(), 2) }} {{ $product->currency }} lei / m <br/>
                                            </p>
                                            <x-simple-link
                                                href="{{ route('product.show', ['slug' => $product->slug]) }}"
                                                class="text-sm md:text-xs text-right  font-extrabold">
                                                Detalii produs
                                            </x-simple-link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="col-span-full text-center text-gray-500">No products found in this category.</p>
                        @endforelse
                    </div>


                </div>
            </main>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 md:px-0 py-12">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">
            Caută produse după culoare
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-6">

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

    <section class="w-full bg-white border-t border-black pt-12 pb-4">
        <div class="max-w-7xl mx-auto py-16">

            <!-- CURTAIN BLOCK (with before-after slider) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start mb-24">
                <!-- TEXT BLOCK -->
                <div class="col-span-1 flex flex-col">
                    <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">Perdele, draperii <br/> la comandă</h1>
                    <p class="text-base md:text-lg mb-6">
                        Alege materialul preferat, dimensiunile exacte, tipul de rejansă și creează soluția perfectă pentru spațiul tău.
                        Produsele noastre sunt realizate manual, special pentru tine.
                    </p>
                    <a href="{{ route('products.category', ['slug' => 'perdele']) }}"
                       class="bg-black text-white font-semibold px-6 py-3 rounded-md hover:bg-gray-800 transition w-fit">
                        Configurează perdeaua ta
                    </a>
                </div>

                <!-- SLIDER BLOCK -->
                <div class="col-span-2 relative w-full h-[400px] md:h-[500px] overflow-hidden border border-black rounded-lg">
                    <div class="relative w-full h-full before-after-slider">
                        <img src="{{ asset('storage/images/tex_3.png') }}" class="absolute top-0 left-0 w-full h-full object-cover z-10 after-img" />
                        <img src="{{ asset('storage/images/tex_2.png') }}" class="absolute top-0 left-0 w-full h-full object-cover z-20 before-img" />
                        <div class="handle z-30"></div>
                    </div>
                </div>
            </div>

            <!-- RUG BLOCK -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <!-- IMAGE LEFT -->
                <div class="relative w-full h-[400px] md:h-[500px] overflow-hidden border border-black rounded-lg">
                    <img src="{{ asset('storage/images/rug_after.png') }}" alt="Covor elegant"
                         class="w-full h-full object-cover rounded-lg">
                </div>

                <!-- TEXT RIGHT -->
                <div class="flex flex-col">
                    <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">Covoare elegante <br/> pentru orice spațiu</h1>
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
        new Swiper('.mySwiper', {
            loop: false,
            allowTouchMove: false,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });

        document.addEventListener('DOMContentLoaded', () => {
            const swiperInstance = document.querySelector('.mySwiper').swiper;

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

                const startDrag = () => {
                    isDragging = true;
                    if (swiperInstance) swiperInstance.allowTouchMove = false;
                };

                const stopDrag = () => {
                    isDragging = false;
                    if (swiperInstance) swiperInstance.allowTouchMove = true;
                };

                handle.addEventListener('mousedown', startDrag);
                document.addEventListener('mouseup', stopDrag);
                document.addEventListener('mousemove', e => isDragging && updateSlider(e.clientX));

                handle.addEventListener('touchstart', startDrag);
                document.addEventListener('touchend', stopDrag);
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
