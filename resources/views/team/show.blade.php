@extends('layouts.base')

@section('content')
    <!-- Hero Section with Background Image -->
    <section class="relative text-center text-white py-10 min-h-[300px]">
        <div class="background-image_cover absolute inset-0 z-0"
             style="
             background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('{{ asset('storage/'.$employee->background_image) }}');
             ">
        </div>

        <div class="relative z-10 p-12">
            <h1 class="text-4xl font-bold mb-4 uppercase">{{ $employee->name }}</h1>
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

    <section>
        <div class="relative container bg-white mx-auto my-16 p-6 md:rounded-2xl min-h-[800px]">
            <!-- Main Section: Image and Employee Details -->
            <div class="flex flex-col md:flex-row items-stretch gap-8 pb-6">
                <!-- Employee Profile Image (Left, 1/4 width) -->
                <div class="w-full md:w-1/4 pt-1 grow md:h-full ">
                    <img src="{{ asset('storage/' . $employee->profile_image) }}" alt="{{ $employee->name }}" class="rounded-lg shadow-md border border-gray-300 w-full max-h-[300px] md:h-full object-cover">
                </div>

                <!-- Employee Details (Center, 1/2 width) -->
                <div class="w-full md:w-1/2 text-center md:text-left grow h-full flex flex-col">
                    <h1 class="text-5xl font-extrabold text-black mb-4 -mt-2 SkModernist uppercase">{{ $employee->name }}</h1>
                    <p class="text-gray-600 text-lg mt-8 underline">{{ $employee->specialty }}</p>
                    <p class="text-gray-600 text-sm mt-8 grow">
                        <span class="font-extrabold text-red-500">{{ __('team.artist_title_' . $employee->id) }}</span>
                        <br/>
                        {!! __('team.artist_desc_' . $employee->id) !!}
                    </p>
                </div>

                <!-- Services (Right, 1/4 width) -->
                <div class="w-full md:w-1/4 text-center md:text-right border-l pb-4 border-black grow h-full flex flex-col">
                    <h3 class="text-4xl font-bold mb-4 uppercase">{{ __('home.services') }}</h3>

                    @if($employee->services->isNotEmpty())
                        @php
                            $groupedServices = $employee->services->groupBy('category');
                        @endphp
                        @foreach($groupedServices as $category => $services)
                            <div class="mb-4">
                                <h4 class="text-lg font-semibold text-black uppercase">{{ __($category) }}</h4> <!-- Category Title -->
                                <ul class="text-right mt-2 ml-4">
                                    @foreach($services as $service)
                                        <li class="text-sm text-gray-700 pl-4">
                                            {{ $service->translate(app()->getLocale())->name ?? $service->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500">{{ __('home.no_services') }}</p>
                    @endif

                    <x-tooltip-cta
                        title="{{ __('home.book_now') }} {{ $employee->name }}"
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

            <div class="flex flex-col md:flex-row items-stretch gap-8 pb-6">
                <!-- Employee Profile Image (Left, 1/4 width) -->
                <div class="w-full md:w-1/4 pt-1 grow h-full">


                    @switch($employee->specialty)
                        @case('Tattoo Artist')
                            <h2 class="text-2xl lg:text-9xl">{{ __('team.what_i_can_tattoo_artist') }}</h2>
                            <p class="mt-8 text-xl lg:text-3xl">
                                {{ __('team.what_i_can_text_tattoo_artist') }}
                            </p>
                            <div x-data="{ modalOpen: false, selectedImage: '', selectedTitle: '' }" class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-6 xl:grid-cols-8 gap-4 mt-6">
                                @foreach ($allTattooStyles as $style)
                                    <div class="flex flex-col bg-gray-100 px-2 rounded-lg shadow-md h-full group relative">
                                        <!-- Tattoo Style Name -->
                                        <h3 class="text-sm font-semibold text-left mt-2">
                                            {{ $style->translate(app()->getLocale())->name ?? $style->name }}
                                        </h3>
                                        <p class="text-black text-xs text-left mt-2 md:h-12">
                                            {{ $style->translate(app()->getLocale())->short_description ?? $style->short_description }}
                                        </p>

                                        <!-- Style Image with Zoom Icon -->
                                        <div class="relative mt-4">
                                            @if($style->image)
                                                <img src="{{ asset('storage/' . $style->image) }}"
                                                     alt="{{ $style->name }}"
                                                     class="w-full object-cover rounded cursor-pointer"
                                                     @click="modalOpen = true; selectedImage = '{{ asset('storage/' . $style->image) }}'; selectedTitle = '{{ $style->translate(app()->getLocale())->name ?? $style->name }}'">
                                                <!-- Zoom Icon -->
                                                <div class="absolute top-2 right-2 bg-white bg-opacity-75 rounded-full p-1 shadow-md hover:bg-opacity-100 cursor-pointer"
                                                     @click="modalOpen = true; selectedImage = '{{ asset('storage/' . $style->image) }}'; selectedTitle = '{{ $style->translate(app()->getLocale())->name ?? $style->name }}'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <circle cx="11" cy="11" r="8" />
                                                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Skill Status -->
                                        <div class="w-full flex mb-4 mt-1 items-center justify-center">
                                            @if ($employee->tattooStyles->contains($style->id))
                                                <span class="text-green-500 text-xs font-semibold flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    {{ __('team.i_got_this_skill') }}
                                                </span>
                                            @else
                                                <span class="text-red-500 text-xs font-semibold flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    {{ __('team.sorry_dont_do_that') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Modal for Image Preview -->
                                <template x-teleport="body">
                                    <div x-show="modalOpen" class="fixed top-0 left-0 z-99 flex items-center justify-center w-screen h-screen" x-cloak>
                                        <!-- Modal Background -->
                                        <div x-show="modalOpen"
                                             x-transition:enter="ease-out duration-300"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="ease-in duration-300"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0"
                                             @click="modalOpen = false"
                                             class="absolute inset-0 w-full h-full bg-black bg-opacity-40">
                                        </div>

                                        <!-- Modal Content -->
                                        <div x-show="modalOpen"
                                             x-trap.inert.noscroll="modalOpen"
                                             x-transition:enter="ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="ease-in duration-200"
                                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             class="relative bg-white rounded-lg shadow-lg w-11/12 max-w-3xl p-6">
                                            <!-- Modal Header -->
                                            <div class="flex items-center justify-between pb-4 border-b border-gray-300">
                                                <h3 class="text-lg font-semibold text-gray-800" x-text="selectedTitle"></h3>
                                                <button @click="modalOpen = false" class="text-gray-600 hover:text-gray-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Image Preview -->
                                            <div class="mt-4">
                                                <img :src="selectedImage" alt="Tattoo Style Image" class="rounded-lg w-full h-auto object-cover">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            @break

                        @case('PMU Artist')
                            <h2 class="text-2xl lg:text-9xl">{{ __('team.what_i_can_pmu_artist') }}</h2>
                            <p class="mt-8 text-xl lg:text-3xl">
                                {{ __('team.what_i_can_text_pmu_artist') }}
                            </p>
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold">{{ __('team.pmu_services') }}</h3>
                                <ul class="list-disc list-inside mt-4">
                                    @foreach ($employee->services as $service)
                                        <li class="text-gray-800">{{ $service->translate(app()->getLocale())->name ?? $service->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @break

                        @case('piercer Artist')
                            <h2 class="text-2xl lg:text-9xl">{{ __('team.what_i_can_piercer_artist') }}</h2>
                            <p class="mt-8 text-xl lg:text-3xl">
                                {{ __('team.what_i_can_text_piercer_artist') }}
                            </p>
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold">{{ __('team.piercing_styles') }}</h3>
                                <ul class="list-disc list-inside mt-4">
                                    @foreach ($employee->piercingStyles as $style)
                                        <li class="text-gray-800">{{ $style->translate(app()->getLocale())->name ?? $style->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @break

                        @default
                            <p class="text-gray-500">{{ __('team.no_specialization_found') }}</p>
                    @endswitch

                    <br/>
                    <x-tooltip-cta
                        title="{{ __('home.book_now') }} {{ $employee->name }}"
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

        </div>
    </section>

    <!-- Portfolio Section -->
    @if($employee->portfolios->isNotEmpty())
        <section class="container mx-auto my-8 p-6 md:rounded-2xl">
            <h3 class="text-2xl font-semibold text-white mb-6">{{ $employee->name }} - {{ __('home.portfolio') }} </h3>
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-8 gap-6">
                @foreach($employee->portfolios as $portfolio)
                    <a href="{{ route('portfolio.show', $portfolio->id) }}" class="group relative">
                        <img src="{{ asset('storage/' . $portfolio->main_image) }}" alt="{{ $portfolio->description }}" class="w-full h-full object-cover rounded-lg shadow-md">
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center rounded-lg">
                            <span class="text-white font-bold">{{ __('home.view_work') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
