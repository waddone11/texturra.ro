@extends('layouts.base')

@section('content')
    <!-- Header Section -->
    <section class="relative text-center text-white py-10 min-h-[300px]">
        <div class="background-image_store absolute inset-0 z-0"></div>
        <div class="relative z-10 p-8 max-w-2xl mx-auto">
            <h1 class="text-4xl font-bold mb-4 uppercase">{{ __('store.heading') }}</h1>
            <p class="text-base leading-relaxed">{{ __('store.description') }}</p>
            <a href="tel:{{ config('app.tel') }}" class="mt-4 inline-block bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                {{ __('home.book_cta') }}
            </a>
        </div>
    </section>

    <!-- Product Display Section -->
    <section class="relative container bg-white mx-auto my-8 min-h-[800px] p-6 rounded-2xl">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-lg pb-6 text-center">
                    <!-- Product Image with Heart Icon -->
                    <div class="relative">
                        @if(!empty($product->images) && is_array($product->images))
                            <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover rounded-t-lg">
                        @endif
                        <button class="absolute top-2 right-2 bg-white p-1 rounded-full shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Product Name, Price, and Description -->
                    <div class="text-left p-4">
                        <p class="text-sm text-gray-500">{{ $product->category->name }}</p>
                        <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($product->price, 2) }}</p>
                        <p class="text-sm text-gray-600 mt-2">{{ Str::limit($product->description, 60) }}</p>
                    </div>

                    <!-- Options and Add to Cart Button -->
                    <div class="flex justify-around items-center mt-4 text-sm p-4">
                        <select class="border border-gray-300 rounded p-2">
                            <option>Color</option>
                            <option>Lighter</option>
                            <option>Darker</option>
                        </select>

                        <button class="ml-2 bg-black text-white px-4 py-2 rounded-lg w-full">{{ __('store.add_to_cart') }}</button>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Reminder and CTA Section -->
        <div class="mt-8 text-center">
            <p class="font-bold">{{ __('home.dont_forget') }}</p>
            <a href="tel:+123456789" class="mt-4 inline-block bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                {{ __('home.book_cta') }}
            </a>
        </div>
    </section>
@endsection
