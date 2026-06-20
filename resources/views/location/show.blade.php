@extends('layouts.base')

@section('content')
    <!-- Hero Section with Background Image -->


    <section class="relative text-center text-white pt-2 md:py-10 md:min-h-[300px]">
        <div class="background-image_booking absolute inset-0 z-0"></div>
        <div class="relative z-10 md:p-8">
            <h1 class="text-xl md:text-4xl font-bold mb-4 uppercase">{{ $location->name }}</h1>
            <p class="text-xs md:text-sm mt-2 font-extrabold">{{ $location->address }}</p>
            <p class="text-xs md:text-sm mt-1 mb-2 font-extrabold">{{ __('home.phone') }}: {{ $location->phone }}</p>
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

    <!-- Location Details Section -->
    <section>
        <div class="relative container bg-white mx-auto my-8 min-h-[800px] p-6 md:rounded-2xl">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 pb-6 border-b border-gray-300">
                <!-- Location Images -->
                <div class="w-full md:w-1/2 lg:w-1/4">
                    <div class="h-64 bg-cover rounded-lg shadow-md" style="background-image: url('{{ asset('storage/'.$location->background_image) }}');"></div>
                    <div class="w-48 h-48 mx-auto -mt-24 z-10 rounded-full border-4 border-red-500 bg-cover shadow-lg"
                         style="background-image: url('{{ asset('storage/'.$location->profile_image) }}');"></div>
                </div>

                <!-- Location Information and Schedule -->
                <div class="w-full md:w-1/2 lg:w-3/4 text-center md:text-left">
                    <h2 class="text-3xl font-semibold mb-6">{{ __('location.details') }}</h2>
                    <p class="text-black text-lg mb-4 font-extrabold">{{ __('location.title') }}</p>
                    <p class="text-black text-lg mb-4 text-sm">{!! __('location.desc') !!}</p>

                    <!-- Schedule -->
                    <div class="mt-8">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">{{ __('location.schedule') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                <div class="flex justify-between bg-gray-100 p-4 rounded-lg shadow-sm">
                                    <span class="font-medium text-gray-800">{{ __('schedule.' . $day) }}</span>
                                    <span class="text-gray-600">
                                        @if($location->{"is_open_{$day}"})
                                            {{ \Carbon\Carbon::parse($location->{"{$day}_open"})->format('H:i') }} - {{ \Carbon\Carbon::parse($location->{"{$day}_close"})->format('H:i') }}
                                        @else
                                            <span class="text-red-500">{{ __('schedule.closed') }}</span>
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services and Map Section -->
            <section class="container mx-auto mt-12">
                <div class="flex flex-col lg:flex-row gap-12 items-stretch">
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
                                                @if($service->price)
                                                    €{{ number_format($service->price, 2) }}
                                                @else
                                                    <x-tooltip-cta
                                                        title="{{ __('home.price_discussed') }}"
                                                        type="link"
                                                        :links="[
                                                    ['label' => __('home.call_us'), 'url' => 'tel:' . config('app.tel')],
                                                    ['label' => __('home.whatsapp_chat'), 'url' => 'https://wa.me/+491744552303'],
                                                    ['label' => __('home.messenger_chat'), 'url' => 'https://m.me/1693515170877100'],
                                                    ['label' => __('home.tiktok_chat'), 'url' => 'https://www.tiktok.com/@shark_tattoo_studio'],
                                                    ['label' => __('home.instagram_chat'), 'url' => 'https://www.instagram.com/shark.tattoo.marius'],
                                                ]"
                                                    />
                                                @endif
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
                        <div class="w-full h-[400px] lg:h-full bg-gray-100 overflow-hidden shadow-lg flex-grow">
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
                            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
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

            <!-- Employees Working at Location -->
            <section class="container mx-auto mt-12 pb-6">
                <h2 class="text-3xl font-semibold text-gray-800 mb-6 text-left">{{ __('location.our_team') }} - {{ $location->name }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
                    @foreach($location->employees as $employee)
                        <div class="bg-white text-left">
                            <!-- Employee Image -->
                            <div class="w-full h-64 mx-auto bg-cover bg-center mb-4"
                                 style="background-image: url('{{ asset($employee->profile_image ? 'storage/' . $employee->profile_image : 'storage/images/no_user.webp') }}');">
                            </div>

                            <!-- Employee Name and Role -->
                            <h4 class="text-xl font-semibold">{{ $employee->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $employee->specialty }}</p>

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
            </section>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section class="container mx-auto mt-12 mb-12 px-6">
        <h2 class="text-3xl font-semibold text-white mb-6">{{ __('location.portfolio') }} - {{ $location->name }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-6">
            @foreach($location->employees as $employee)
                @foreach($employee->portfolios as $portfolio)
                    <div class="relative group">
                        <a href="{{ route('portfolio.show', $portfolio->id) }}" class="block">
                        <!-- Portfolio Image -->
                            <img src="{{ asset('storage/' . $portfolio->main_image) }}" alt="{{ $portfolio->description }}" class="w-full h-full object-cover rounded-2xl">

                            <!-- Employee Name Badge -->
                            <span class="absolute top-2 left-2 bg-red-500 text-white text-sm font-bold px-2 py-1 rounded">
                                {{ $employee->name }}
                            </span>

                            <!-- Overlay on Hover -->
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center rounded-2xl">
                                <span class="text-white font-bold">{{ __('home.view_work') }}</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endforeach
        </div>
    </section>

@endsection
