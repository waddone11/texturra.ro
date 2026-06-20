@extends('layouts.base')

@section('content')

        <section>
            <div class="relative flex items-center justify-center h-3_4-screen sm:min-h-screen overflow-hidden">
                <!-- Logo and Text -->
                <div class="relative z-10 flex flex-col items-center">
                    <!-- Logo Image -->
                    <img src="{{ asset('images/logo_alb.svg') }}" alt="Logo" class="w-3/5 sm:w-1/5 -mt-32 sm:mt-0">

                    <!-- Logo Text -->
                    <h1 class="logoHome mt-4 text-gray-900 dark:text-gray-100 text-shadow font-extrabold pixelate-animation text-3xl sm:text-9xl">
                        Shark Tatt<span class="our-red">o</span>o
                    </h1>
                </div>
            </div>
        </section>

        <section>
            <div class="relative flex items-center justify-center overflow-hidden backdrop-blur backdrop-brightness-0 h-auto md:h-40">
                <div class="container z-10 grid grid-cols-2 md:grid-cols-4 gap-4 text-center text-white">
                    <!-- Block 1: Call the Shop -->
                    <div class="h-28 flex flex-col justify-center">
                        <h3 class="text-sm md:text-lg font-bold">{{ __('home.call_shop') }}</h3>
                        <p class="text-xs md:text-base">{{config('app.tel')}}</p>
                    </div>
                    <!-- Block 2: Our Hours -->
                    <div class="h-28 flex flex-col justify-center">
                        <h3 class="text-sm md:text-lg font-bold">{{ __('home.our_hours') }}</h3>
                        <p class="text-xs md:text-base">{{ __('home.hours_weekdays') }}</p>
                        <p class="text-xs md:text-base">{{ __('home.hours_sunday') }}</p>
                    </div>
                    <!-- Block 3: Visit Us -->
                    <div class="h-28 flex flex-col justify-center">
                        <h3 class="text-sm md:text-lg font-bold">{{ __('home.visit_us') }}</h3>
                        <p class="text-xs md:text-base">{{ __('home.address_line1') }}</p>
                        <p class="text-xs md:text-base">{{ __('home.address_line2') }}</p>
                    </div>
                    <!-- Block 4: Email Us -->
                    <div class="h-28 flex flex-col justify-center">
                        <h3 class="text-sm md:text-lg font-bold">{{ __('home.email_us') }}</h3>
                        <p class="text-xs md:text-base">{{ __('home.email') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-black">
            <div class="container mx-auto flex flex-col sm:flex-row">

                <div class="w-full sm:w-1/2 h-[350px] sm:h-auto min-h-[350px] sm:min-h-0 flex-shrink-0">
                    <img src="{{ asset('images/machine_mb.png') }}" alt="Tattoo Shop" class="object-cover">
                </div>

                <!-- Right Half with About Us Content -->
                <div class="w-full sm:w-1/2 flex flex-col justify-center custom_about_mb p-6 text-white">
                    <h2 class="text-3xl font-bold mb-4 uppercase">{{ __('home.about_us') }}</h2>
                    <p id="about-us-text">{!! __('home.about_us_text') !!}</p>

                    <!-- Expandable Text for About Us -->
                    <p id="about-us-full-text" class="hidden">
                        {{ __('home.about_us_text_extended') }}
                        <br/>
                        <a href="{{ route('about') }}">
                            <x-primary-button class="mt-4 bg-white text-black">
                                {{ __('home.read_entire') }}
                            </x-primary-button>
                        </a>
                    </p>

                    <x-primary-button onclick="toggleReadMore()" id="read-more-btn" class="mt-4 bg-white text-black">
                        {{ __('home.read_entire') }}
                    </x-primary-button>
                </div>
            </div>
        </section>

        <!-- JavaScript for Accordion Toggle -->
        <script>
            function toggleReadMore() {
                const fullText = document.getElementById('about-us-full-text');
                const readMoreBtn = document.getElementById('read-more-btn');

                if (fullText.classList.contains('hidden')) {
                    fullText.classList.remove('hidden');
                    readMoreBtn.textContent = "{{ __('home.read_less') }}";
                } else {
                    fullText.classList.add('hidden');
                    readMoreBtn.textContent = "{{ __('home.read_more') }}";
                }
            }
        </script>

        <!-- Our Work Section -->
        <section class="bg-white-900 pb-12" style="overflow-x: auto; max-width: 100%;">
            <div class="container mx-auto text-right mb-8">
                <h2 class="text-4xl font-bold our-red mb-0 md:mb-4 uppercase mt-8 md:mt-0">{{ __('home.our_work') }}</h2>
                <p class="text-black text-xs text-white">{{ __('home.our_work_description') }}</p>
            </div>

            <!-- Mobile Horizontal Scrollable Gallery -->
            <div class="container mx-auto px-6">
                <div class="flex gap-4 overflow-x-scroll md:hidden pb-8 relative" style="scroll-snap-type: x mandatory;">
                    @foreach($portfolios as $portfolio)
                        <a href="{{ route('portfolio.show', $portfolio->id) }}" class="relative flex-shrink-0 w-[40%] scroll-snap-align: start group">
                            <img src="{{ asset('storage/' . $portfolio->main_image) }}" alt="{{ $portfolio->description }}" class="w-full h-full object-cover rounded-2xl">

                            <!-- Artist Name Badge -->
                            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded">
                                {{ $portfolio->employee->name }}
                            </span>

                            <!-- Overlay on Hover -->
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center rounded-2xl">
                                <span class="text-white font-bold">{{ __('home.view_work') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Desktop Grid Layout -->
                <div class="container mx-auto hidden md:grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    @foreach($portfolios as $portfolio)
                        <a href="{{ route('portfolio.show', $portfolio->id) }}" class="relative group">
                            <img src="{{ asset('storage/' . $portfolio->main_image) }}" alt="{{ $portfolio->description }}" class="w-full h-full object-cover rounded-2xl">

                            <!-- Artist Name Badge -->
                            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded">
                                {{ $portfolio->employee->name }}
                            </span>

                            <!-- Overlay on Hover -->
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center rounded-2xl">
                                <span class="text-white font-bold">{{ __('home.view_work') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Book Now and View Portfolios Buttons -->
                <div class="text-left w-full md:w-1/2 mt-0 md:mt-8 text-white">
                    <strong class="text-xs md:text-sm">{!! __('book.info') !!}</strong><br/>

                    <x-primary-link class="mr-4 mt-4 bg-red-500 text-white" href="{{ route('book-appointment') }}">
                        {{ __('home.book_now') }}
                    </x-primary-link>
                    <x-primary-link class="md:ml-4 mt-4 bg-red-500 text-white" href="{{ route('portfolios') }}">
                        {{ __('home.view_portfolios') }}
                    </x-primary-link>
                </div>
            </div>
        </section>

        <section class="md:px-2">
            <div class="container bg-white mx-auto my-8 md:min-h-[500px] p-6 md:rounded-2xl">
                <div class="flex items-center mb-8">
                    <img src="{{ asset('images/nicoleta.jpg') }}" alt="Nicoleta - Manager" class="w-24 h-24 rounded-full mr-4">
                    <div>
                        <h3 class="font-bold text-lg">{{ __('home.manager_name') }}</h3>
                        <p>{{ __('home.manager_phone') }}</p>
                    </div>
                </div>

                <livewire:calendar-component :events="$events" view="timeGridWeek" />

                <div class="mt-8 text-center text-xs md:text-sm">
                    <p class="font-bold mb-4">{{ __('home.dont_forget') }}</p>
                    <x-tooltip-cta
                        title="{{ __('home.connect_with_us') }}"
                        :links="[
                            ['label' => __('home.call_us'), 'url' => 'tel:' . config('app.tel')],
                            ['label' => __('home.whatsapp_chat'), 'url' => 'https://wa.me/+491744552303'],
                            ['label' => __('home.messenger_chat'), 'url' => 'https://m.me/1693515170877100'],
                            ['label' => __('home.tiktok_chat'), 'url' => 'https://www.tiktok.com/@shark_tattoo_studio'],
                            ['label' => __('home.instagram_chat'), 'url' => 'https://www.instagram.com/shark.tattoo.marius'],
                        ]"
                    />
                </div>
            </div>
        </section>

        <section class="relative container mx-auto my-8 min-h-[400px] p-6 pb-12 rounded-2xl">
            <h2 class="text-3xl font-bold text-center mb-8 text-white">Meet Our Team</h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                @foreach($employees as $employee)
                    <div class=" text-left">
                        <!-- Employee Image -->
                        <div class="w-full h-64 mx-auto bg-cover bg-top mb-4"
                             style="background-image: url('{{ asset($employee->profile_image ? 'storage/' . $employee->profile_image : 'storage/images/no_user.webp') }}');">
                        </div>

                        <!-- Employee Name and Role -->
                        <h4 class="text-xl font-semibold text-white">{{ $employee->name }}</h4>
                        <p class="text-sm text-white">{{ $employee->specialty }}</p>

                        <!-- Social Icons -->
                        <div class="mt-4 flex justify-left">
                            <!-- Left-aligned button -->
                            <x-simple-link href="{{ route('team.show', $employee->slug) }}" class="bg-white text-black border border-black">
                                {{ __('location.my_work') }}
                            </x-simple-link>

                            <!-- Right-aligned button -->
                            <x-primary-link href="{{ route('book-appointment') }}" class="ml-4 border border-red-500 text-red-500">
                                {{ __('home.book_now') }}
                            </x-primary-link>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16 md:w-1/2 text-left">
                <p class="text-white text-xs md:text-md">
                    <strong>{!! __('book.info') !!}</strong><br>
                </p>
                <br/>
                <x-tooltip-cta
                    title="{{ __('home.connect_with_us') }}"
                    :links="[
                            ['label' => __('home.call_us'), 'url' => 'tel:' . config('app.tel')],
                            ['label' => __('home.whatsapp_chat'), 'url' => 'https://wa.me/+491744552303'],
                            ['label' => __('home.messenger_chat'), 'url' => 'https://m.me/1693515170877100'],
                            ['label' => __('home.tiktok_chat'), 'url' => 'https://www.tiktok.com/@shark_tattoo_studio'],
                            ['label' => __('home.instagram_chat'), 'url' => 'https://www.instagram.com/shark.tattoo.marius'],
                        ]"
                />
            </div>
        </section>


        <!-- Services and Map Section -->
        @foreach($locations as $location)
            <div class="container-full bg-white py-2">
                <section class="container bg-white mx-auto mt-12 mb-12 px-6">
                    <h2 class="text-3xl font-semibold text-gray-800 mb-6">Shark tattoo - {{$location->name}}</h2>
                    <div class="flex flex-col lg:flex-row gap-12 items-stretch"  >
                        <!-- Services Offered at Location (Left Side) -->
                        <div class="lg:w-1/4 flex flex-col h-full">
                            <h2 class="text-3xl font-semibold text-gray-800 mb-6">{{ __('location.services_offered') }}</h2>
                            <div class="overflow-x-auto">
                                @php
                                    // Group services by category
                                    $groupedServices = $location->services->groupBy('category');
                                @endphp

                                <table class="min-w-full table-auto border-collapse border border-black">
                                    <thead class="bg-black text-white">
                                    <tr>
                                        <th class="p-2 text-left text-xs md:text-sm font-semibold text-white border-b border-black">{{ __('Service') }}</th>
                                        <th class="p-2 text-left text-xs md:text-sm font-semibold text-white border-b border-black">{{ __('Duration') }}</th>
                                        <th class="p-2 text-left text-xs md:text-sm font-semibold text-white border-b border-black">{{ __('Price') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($groupedServices as $category => $services)
                                        <!-- Category Row -->
                                        <tr class="bg-gray-500">
                                            <td colspan="3" class="p-2 text-left text-white text-xs md:text-sm font-semibold text-gray-800 border-b border-black">
                                                {{ ucfirst($category) }}
                                            </td>
                                        </tr>

                                        <!-- Services in Category -->
                                        @foreach($services as $service)
                                            @php
                                                $translation = $service->translate(); // Get the translation for the current locale
                                            @endphp
                                            <tr class="@if($loop->odd) bg-gray-100 @endif">
                                                <td class="p-2 text-xs md:text-sm border-b border-black font-extrabold">
                                                    {{ $translation->name }}
                                                </td>
                                                <td class="p-2 text-xs border-b border-black">
                                                    {{ $service->duration }} mins
                                                </td>
                                                <td class="p-2 text-xs border-b border-black">
                                                    {!! $service->price
                                                        ? '€' . number_format($service->price, 2)
                                                        : '<span class="text-xs text-indigo-800 underline">' . __('home.price_discussed') . '</span>' !!}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <!-- No Services Available -->
                                        <tr>
                                            <td colspan="3" class="p-2 text-xs md:text-sm text-center text-gray-600 border-b border-gray-300">
                                                {{ __('home.no_services') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>



                        <!-- Location on Map (Right Side) -->
                        <div class="lg:w-3/4 flex flex-col h-full">
                            <h2 class="text-3xl font-semibold text-gray-800 mb-6">{{ __('location.location_on_map') }}</h2>
                            <div class="w-full h-[400px] lg:h-full bg-gray-100 border border-black overflow-hidden shadow-lg flex-grow">
                                <iframe
                                    src="https://www.google.com/maps/embed/v1/place?key=AIzaSyCKU-CXwjABLay2_idjQ1Ydgedk4-7bPI4&q={{ urlencode($location->address) }}"
                                    width="100%"
                                    height="270px"
                                    style="border:0;"
                                    allowfullscreen=""
                                    loading="lazy"></iframe>
                            </div>
                            <h2 class="text-3xl font-semibold text-gray-800 mb-6 mt-8">{{ __('location.photos') }}</h2>
                            <!-- Modal for Image Gallery -->
                            <div x-data="{ modalOpen: false, selectedImage: '' }">
                                <!-- Images Grid -->
                                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
                                    @foreach($location->images as $image)
                                        <div class="bg-white text-left">
                                            <div class="w-full h-32 mx-auto bg-cover bg-center rounded-lg mb-4 cursor-pointer"
                                                 style="background-image: url('{{ asset('storage/' . $image) }}');"
                                                 @click="modalOpen = true; selectedImage = '{{ asset('storage/' . $image) }}'">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Modal for Image Gallery -->
                                <template x-teleport="body">
                                    <div x-show="modalOpen" class="fixed top-0 left-0 z-[99] flex items-center justify-center w-screen h-screen" x-cloak>
                                        <div x-show="modalOpen"
                                             x-transition:enter="ease-out duration-300"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="ease-in duration-300"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0"
                                             @click="modalOpen = false" class="absolute inset-0 w-full h-full bg-black bg-opacity-40"></div>
                                        <div x-show="modalOpen"
                                             x-trap.inert.noscroll="modalOpen"
                                             x-transition:enter="ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="ease-in duration-200"
                                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             class="relative w-full py-6 bg-white px-7 sm:max-w-lg sm:rounded-lg">
                                            <div class="flex items-center justify-between pb-2">
                                                <h3 class="text-lg font-semibold">Image Preview</h3>
                                                <button @click="modalOpen = false" class="absolute top-0 right-0 flex items-center justify-center w-8 h-8 mt-5 mr-5 text-gray-600 rounded-full hover:text-gray-800 hover:bg-gray-50">
                                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </div>
                                            <div class="relative w-auto">
                                                <img :src="selectedImage" alt="Location Image" class="w-full h-auto object-cover rounded-lg">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
        @endforeach








@endsection
