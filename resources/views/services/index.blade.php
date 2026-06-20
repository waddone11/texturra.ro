@extends('layouts.base')

@section('content')
    <section class="container mx-auto my-8 p-6 bg-white rounded-lg">
        <h1 class="text-3xl font-bold mb-6">{{ __('Services') }}</h1>
        @foreach($services as $category => $groupedServices)
            <div class="mb-8">
                <!-- Category Title -->
                <h2 class="text-2xl font-semibold text-gray-800 mb-4 uppercase">{{ __($category) }}</h2>

                <!-- Services Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($groupedServices as $service)
                        <div class="p-6 bg-gray-100 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                            <!-- Service Title -->
                            <h3 class="text-xl font-bold text-gray-700 mb-4">{{ $service->translate(app()->getLocale())->name ?? $service->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4">
                                {{ $service->translate(app()->getLocale())->short_description ?? '' }}
                            </p>

                            <!-- Service Details in a Table -->
                            <table class="w-full text-sm text-left border-collapse border border-gray-300">
                                <tbody>
                                <tr>
                                    <th class="px-4 py-2 border border-gray-300 bg-gray-200 text-gray-700">{{ __('Duration') }}</th>
                                    <td class="px-4 py-2 border border-gray-300">{{ $service->duration }} mins</td>
                                </tr>
                                <tr>
                                    <th class="px-4 py-2 border border-gray-300 bg-gray-200 text-gray-700">{{ __('Price') }}</th>
                                    <td class="px-4 py-2 border border-gray-300">
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
                                <tr>
                                    <th class="px-4 py-2 border border-gray-300 bg-gray-200 text-gray-700">{{ __('Employees') }}</th>
                                    <td class="px-4 py-2 border border-gray-300">
                                        {{ $service->employees_count }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <!-- Employees List -->
                            <div class="mt-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ __('Employees providing this service:') }}</h4>
                                <ul class="text-sm text-gray-600 list-disc pl-5">
                                    @forelse($service->employees as $employee)
                                        <li>
                                            <a href="{{ route('team.show', $employee->slug) }}" class="text-indigo-600 hover:underline">
                                                {{ $employee->name }}
                                            </a>
                                        </li>
                                    @empty
                                        <li class="text-gray-500">{{ __('No employees assigned to this service.') }}</li>
                                    @endforelse
                                </ul>
                            </div>

                            <!-- Learn More Button -->
                            <x-primary-link href="{{ route('services.show', $service->slug) }}" class="mt-6 bg-red-500 text-black">
                                {{ __('Explore This Service') }}
                            </x-primary-link>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>
@endsection
