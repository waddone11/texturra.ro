@extends('layouts.base')

@section('content')
    <!-- Hero Section with Background Image -->
    <section class="relative text-center text-white md:py-10 md:min-h-[300px]">
        <div class="background-image_booking absolute inset-0 z-0"></div>
        <div class="relative z-10 md:p-8">
            <h1 class="text-xl md:text-4xl font-bold mb-4 uppercase">{{ __('location.portfolio') }}</h1>
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

    <!-- Portfolios Section -->
    <section>
        <div class="relative container bg-white mx-auto my-8 min-h-[800px] p-6 md:rounded-2xl">
            <div class="flex flex-col items-center justify-between gap-8 pb-6 border-b border-gray-300">
                <h2 class="text-3xl font-semibold text-gray-800 mb-6">{{ __('location.portfolio') }}</h2>

                <!-- Portfolio Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    @foreach($portfolios as $portfolio)
                        <div class="relative group">
                            <!-- Portfolio Link -->
                            <a href="{{ route('portfolio.show', $portfolio->id) }}" class="block">
                                <!-- Portfolio Image -->
                                <img src="{{ asset('storage/' . $portfolio->main_image) }}" alt="{{ $portfolio->description }}" class="w-full h-full object-cover rounded-2xl">

                                <!-- Employee Name Badge -->
                                <span class="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                    {{ $portfolio->employee->name }}
                                </span>

                                <!-- Overlay on Hover -->
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center rounded-2xl">
                                    <span class="text-white font-bold">{{ __('home.view_work') }}</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div class="mt-8">
                    {{ $portfolios->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
