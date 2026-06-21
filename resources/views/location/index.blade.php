@extends('layouts.base')

@section('content')
    <!-- Hero Section -->
    <section class="relative text-center text-white md:py-10 md:min-h-[300px]">
        <div class="background-image_location absolute inset-0 z-0"></div>
        <div class="relative z-10 p-8">
            <h1 class="text-xl md:text-4xl text-black md:text-white font-bold mb-4 uppercase">{{ __('location.heading') }}</h1>
        </div>
    </section>

    <!-- Locations Section -->
    <section class="relative container bg-white mx-auto my-8 min-h-[800px] p-6 md:rounded-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($locations as $location)
                <div class="bg-white rounded-lg border-gray-400 shadow-lg overflow-hidden transition-transform transform ">
                    <!-- Background Image with Gradient Overlay -->
                    <div class="relative h-48">
                        <img src="{{ asset('storage/'.$location->background_image) }}" alt="{{ $location->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-linear-to-t from-black opacity-60"></div>
                    </div>

                    <!-- Profile Image -->
                    <div class="flex justify-center -mt-12">
                        <img src="{{ asset('storage/'.$location->profile_image) }}" alt="Profile" class="w-24 h-24 rounded-full border-4 border-white shadow-lg object-cover z-10">
                    </div>

                    <!-- Location Details -->
                    <div class="text-center p-6">
                        <h3 class="text-2xl font-semibold">{{ $location->name }}</h3>
                        <p class="text-gray-600 mt-2">{{ $location->address }}</p>
                        <p class="text-gray-600 mt-1">{{ __('home.phone') }}: {{ $location->phone }}</p>

                        <!-- Compact Schedule Section with Icons -->
                        <div class="text-left mt-4">
                            <h4 class="text-md font-semibold mb-2 text-gray-800">{{ __('home.working_hours') }}</h4>
                            <div class="border-t border-gray-200 pt-2 text-sm">
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center">
                                            <i class="fas {{ $location->{"is_open_{$day}"} ? 'fa-door-open text-green-500' : 'fa-door-closed text-red-500' }} mr-2"></i>
                                            <span class="capitalize">{{ __('schedule.' . $day) }}</span>
                                        </span>
                                        <span class="font-light">
                                            @if($location->{"is_open_{$day}"})
                                                {{ \Carbon\Carbon::parse($location->{"{$day}_open"})->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($location->{"{$day}_close"})->format('H:i') }}
                                            @else
                                                <span class="text-red-500">{{ __('schedule.closed') }}</span>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Services Offered Section -->
                        <div class="text-left mt-4">
                            <h4 class="text-md font-semibold mb-2 text-gray-800">{{ __('home.services_offered') }}</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full table-auto border-collapse border border-black">
                                    <thead class="bg-black text-white">
                                    <tr>
                                        <th class="p-2 text-left text-xs md:text-sm font-semibold text-white border-b border-black">{{ __('Service') }}</th>
                                        <th class="p-2 text-left text-xs md:text-sm font-semibold text-white border-b border-black">{{ __('Duration') }}</th>
                                        <th class="p-2 text-left text-xs md:text-sm font-semibold text-white border-b border-black">{{ __('Price') }}</th>
                                    </tr>
                                    </thead>
                                    @forelse($location->services->take(3) as $service)
                                        @php
                                            $translation = $service->translate();
                                        @endphp
                                        <tr class="@if($loop->odd) bg-gray-100 @endif">
                                            <td class="p-2 text-xs md:text-sm border-b border-black font-extrabold">
                                                {{ $translation->name }}
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
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="p-2 text-xs md:text-sm text-center text-gray-600 border-b border-gray-300">
                                                {{ __('home.no_services') }}
                                            </td>
                                        </tr>
                                    @endforelse

                                    <!-- Link to see all services -->
                                    <tr>
                                        <td colspan="3" class="p-2 text-xs md:text-sm text-right text-indigo-600 hover:text-indigo-800 font-semibold">
                                            <a href="{{ $location->url() }}">{{ __('home.see_all_services') }}</a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- View Details Button -->
                        <x-primary-link class="ml-4 mt-4 bg-red-500 text-white" href="{{ $location->url() }}">
                            {{ __('home.view_details') }}
                        </x-primary-link>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
