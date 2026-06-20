@extends('layouts.base')

@section('content')
    <!-- Hero Section with Background Image -->
    <section class="relative text-center text-white py-10 min-h-[300px]">
        <div class="background-image_cover absolute inset-0 z-0"
             style="
         background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('storage/'.$portfolio->employee->background_image) }}');
    ">
        </div>

        <div class="relative z-10 p-8">
            <h1 class="text-xl md:text-4xl font-bold mb-4 uppercase">{{ $portfolio->title }}</h1>
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
        <div class="relative container bg-white mx-auto my-8 p-6 min-h-[800px] md:rounded-2xl">
            <!-- Main Section: Image and Portfolio Details -->
            <div class="flex flex-col md:flex-row items-start gap-8 pb-6">
                <!-- Main Portfolio Image (Left, 2/3 width) -->
                <div class="w-full md:w-2/4">
                    <img src="{{ asset('storage/' . $portfolio->main_image) }}" alt="{{ $portfolio->description }}" class="rounded-lg shadow-md border border-gray-300">
                </div>

                <!-- Portfolio Details (Right, 1/3 width) -->
                <div class="w-full md:w-2/4 text-center md:text-left">
                    <h2 class="text-3xl font-semibold text-gray-800 mb-4">{{ $portfolio->employee->name }}</h2>
                    <p class="text-gray-600 text-sm md:text-lg mt-4">{{ $portfolio->description }}</p>

                    <!-- Social Links -->
                    <div class="flex justify-center md:justify-start space-x-4 mt-4">
                        @if($portfolio->facebook_link)
                            <a href="{{ $portfolio->facebook_link }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                        @endif
                        @if($portfolio->instagram_link)
                            <a href="{{ $portfolio->instagram_link }}" target="_blank" class="text-pink-500 hover:text-pink-700">
                                <i class="fab fa-instagram"></i> Instagram
                            </a>
                        @endif
                        @if($portfolio->tiktok_link)
                            <a href="{{ $portfolio->tiktok_link }}" target="_blank" class="text-black hover:text-gray-700">
                                <i class="fab fa-tiktok"></i> TikTok
                            </a>
                        @endif
                    </div>

                    @if($portfolio->additional_images)
                        <div class="mt-8">
                            <h3 class="text-2xl font-semibold text-gray-800 mb-4">{{ __('Additional Images') }}</h3>
                            <div x-data="{ modalOpen: false, selectedImage: '', selectedTitle: '' }" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-6 gap-4">
                                @foreach(json_decode($portfolio->additional_images) as $image)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $image) }}"
                                             alt="Additional image"
                                             class="rounded-lg shadow-md border border-gray-300 cursor-pointer"
                                             @click="modalOpen = true; selectedImage = '{{ asset('storage/' . $image) }}'; selectedTitle = '{{ $portfolio->title }}'">
                                        <!-- Zoom Icon -->
                                        <div class="absolute top-2 right-2 bg-white bg-opacity-75 rounded-full p-1 shadow-md hover:bg-opacity-100 cursor-pointer"
                                             @click="modalOpen = true; selectedImage = '{{ asset('storage/' . $image) }}'; selectedTitle = '{{ $portfolio->title }}'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <circle cx="11" cy="11" r="8" />
                                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                            </svg>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Modal for Image Preview -->
                                    <template x-teleport="body">
                                        <div x-show="modalOpen" class="fixed top-0 left-0 z-[99] flex items-center justify-center w-screen h-screen overflow-hidden" x-cloak>
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
                                                 class="relative bg-white rounded-lg shadow-lg max-w-3xl w-11/12 h-full max-h-screen overflow-y-auto p-6">
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
                                                <div class="mt-4 flex items-center justify-center">
                                                    <img :src="selectedImage" alt="Additional image" class="rounded-lg max-h-full max-w-full object-contain">
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </section>


    <!-- Other Works Section -->
    <section class="container mx-auto my-8 p-6">
        <h3 class="text-2xl font-semibold text-white mb-6"> {{ __('home.other_works') }} {{ $portfolio->employee->name }}</h3>
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($otherPortfolios as $otherPortfolio)
                @if($otherPortfolio->id !== $portfolio->id) <!-- Exclude current portfolio -->
                <a href="{{ route('portfolio.show', $otherPortfolio->id) }}" class="group relative">
                    <img src="{{ asset('storage/' . $otherPortfolio->main_image) }}" alt="{{ $otherPortfolio->description }}" class="w-full h-full object-cover rounded-lg">
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center rounded-lg">
                        <span class="text-white font-bold">{{ __('home.view_work') }}</span>
                    </div>
                </a>
                @endif
            @endforeach
        </div>
    </section>
@endsection
