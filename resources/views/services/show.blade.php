@extends('layouts.base')

@section('content')
    <section class="container mx-auto my-8 p-6 bg-white rounded-lg">
        <h1 class="text-3xl font-bold mb-4">{{ $service->translate(app()->getLocale())->name ?? $service->name }}</h1>
        <p class="text-gray-600 text-lg mb-6">{{ $service->translate(app()->getLocale())->description ?? '' }}</p>

        <!-- Associated Employees with Service Details -->
        <div class="lg:w-full flex flex-col h-full">
            <h2 class="text-3xl font-semibold text-gray-800 mb-6">{{ __('Associated Employees') }}</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse border border-black">
                    <thead class="bg-black text-white">
                    <tr>
                        <th class="p-2 text-left text-xs md:text-sm font-semibold text-white border-b border-black">{{ __('Employee') }}</th>
                        <th class="p-2 text-left text-xs md:text-sm font-semibold text-white border-b border-black">{{ __('Specialty') }}</th>
                        <th class="p-2 text-left text-xs md:text-sm font-semibold text-white border-b border-black">{{ __('Duration') }}</th>
                        <th class="p-2 text-left text-xs md:text-sm font-semibold text-white border-b border-black">{{ __('Price') }}</th>
                        <th class="p-2 text-left text-xs md:text-sm font-semibold text-white border-b border-black">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($service->employees as $employee)
                        @php
                            $translation = $service->translate(app()->getLocale());
                        @endphp
                        <tr class="@if($loop->odd) bg-gray-100 @endif">
                            <td class="p-2 text-xs md:text-sm border-b border-black font-extrabold">
                                {{ $employee->name }}
                            </td>
                            <td class="p-2 text-xs md:text-sm border-b border-black">
                                {{ $employee->specialty }}
                            </td>
                            <td class="p-2 text-xs md:text-sm border-b border-black">
                                {{ $service->duration }} mins
                            </td>
                            <td class="p-2 text-xs md:text-sm border-b border-black">
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
                            <td class="p-2 text-xs md:text-sm border-b border-black">
                                <a href="{{ route('team.show', $employee->slug) }}" class="text-indigo-600 hover:underline">
                                    {{ __('View Profile') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-2 text-xs md:text-sm text-center text-gray-600 border-b border-gray-300">
                                {{ __('No employees associated with this service.') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="container mx-auto text-center m-8">
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



        <!-- Portfolio Section -->
        <div class="container mx-auto text-left m-8">
            <h3 class="text-3xl font-bold our-red mb-0 md:mb-4 uppercase mt-8 md:mt-0">
                {{ __('home.portfolio_examples', ['service' => $service->translate(app()->getLocale())->name ?? $service->name]) }}
            </h3>
            <p class="text-black text-xs text-white">{{ __('home.our_work_description') }}</p>
        </div>

        <!-- Mobile Horizontal Scrollable Gallery -->
        <div class="container mx-auto">
            <div class="flex gap-4 overflow-x-scroll md:hidden pb-8 relative" style="scroll-snap-type: x mandatory;">
                @foreach($portfolios as $portfolio)
                    <a href="{{ route('portfolio.show', $portfolio->id) }}" class="relative flex-shrink-0 w-[40%] scroll-snap-align: start group">
                        <img src="{{ asset('storage/' . $portfolio->main_image) }}" alt="{{ $portfolio->description }}" class="w-full h-full object-cover rounded-2xl">

                        <!-- Artist Name Badge -->
                        <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
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
            <div class="text-left w-full md:w-1/2 mt-0 text-white">
                <strong class="text-xs md:text-sm">{!! __('book.info') !!}</strong><br/>
                <x-primary-link class="bg-red-500 text-white" href="{{ route('portfolios') }}">
                    {{ __('home.view_portfolios') }}
                </x-primary-link>
            </div>
        </div>
    </section>
@endsection
