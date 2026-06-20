@extends('layouts.base')

@section('content')
    <div class="max-w-7xl mx-auto py-12 px-2 sm:px-0">
        <h1 class="text-2xl font-bold mb-8">Cosul tău de cumpărături</h1>

        <!-- Flash Message -->
        @if (session('flashMessage'))
            <div
                class="p-4 mb-4 text-sm font-medium rounded-lg
                       {{ session('flashMessage.type') === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                {{ session('flashMessage.message') }}
            </div>
        @endif

        <div class="bg-white md:border md:border-black rounded-lg">
            <div class="p1- space-x-1 md:p-6 md:space-y-6">
                <!-- Cart Items -->
                @forelse ($cartItems as $item)
                    @php
                        $isCustom = $item->length || $item->height || $item->manufactoring_type_id;
                    @endphp

                    <div class="flex flex-col md:flex-row justify-between border-b border-black md:pb-4 gap-4 mb-8">
                        <!-- Product Info -->
                        <div class="flex items-start gap-4 md:w-1/3">
                            <img
                                src="{{ $item->product->images[0] ?? '/placeholder-image.png' }}"
                                alt="{{ $item->product->name }}"
                                class="w-16 h-16 rounded object-cover"
                            />
                            <div class="space-y-1">
                                <p class="text-base md:text-lg font-semibold">{{ $item->product->name }}</p>

                                @if ($isCustom)
                                    <ul class="text-xs text-gray-600 space-y-1">
                                        <li>Lățime: {{ $item->length }} m</li>
                                        <li>Înălțime: {{ $item->height }} m</li>
                                        <li>Bucăți: {{ $item->pieces }}</li>
                                        <li>Manoperă: {{ optional($item->manufactoringType)->name }}</li>
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <!-- Actions + Total + Delete -->
                        <div class="grid grid-cols-1 md:grid-cols-8 gap-2 md:w-2/3 w-full">
                            <!-- Update Form -->
                            <div class="md:col-span-6">
                                <div class="w-full md:max-w-xs md:ml-auto md:ml-0">
                                    @if ($isCustom)
                                        @include('cart.custom-update-form', [
                                            'item' => $item,
                                            'manufactoringTypes' => $manufactoringTypes
                                        ])
                                    @else
                                        @include('cart.standard-update-form', ['item' => $item])
                                    @endif
                                </div>
                            </div>

                            <!-- Total and Delete -->
                            <div class="md:col-span-2 w-full flex flex-col md:items-end md:justify-between pr-2 mt-2 bg-gray-50 md:bg-white p-2">
                                <!-- Mobile layout: flex row between total and delete -->
                                <div class="flex justify-between items-center w-full md:flex-col md:items-end md:justify-between gap-2">
                                    <!-- Total -->
                                    <div class="text-left md:text-right">
                                        <p class="text-xs text-gray-500">Total</p>
                                        <p class="text-sm font-bold">
                                            @if ($isCustom)
                                                {{ number_format($item->price * $item->pieces, 2) }} lei
                                            @else
                                                {{ number_format($item->quantity * $item->price, 2) }} lei
                                            @endif
                                        </p>
                                    </div>

                                    <!-- Delete -->
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST"
                                          class="flex items-center gap-1 text-red-600 hover:text-red-800">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                 stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            <span class="text-xs font-semibold">Șterge</span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>


                @empty
                    <p class="text-gray-500">Cosul este gol.</p>
                @endforelse

            </div>

            <!-- Summary -->
            @php
                $calculatedSubtotal = $cartItems->sum(function ($item) {
                    return ($item->length || $item->height || $item->manufactoring_type_id)
                        ? $item->pieces * $item->price
                        : $item->quantity * $item->price;
                });

                $calculatedShipping = ($calculatedSubtotal > config('app.free_shipping_min'))
                    ? 0
                    : config('app.shipping_cost');

                $calculatedTotal = $calculatedSubtotal + $calculatedShipping;
            @endphp

            <div class="p-6 bg-gray-50 space-y-4 rounded-b-lg">
                <div class="flex justify-between text-xs">
                    <p>Subtotal</p>
                    <p>{{ number_format($calculatedSubtotal, 2) }} lei</p>
                </div>
                <div class="flex justify-between text-xs">
                    <p>Transport</p>
                    <p>{{ number_format($calculatedShipping, 2) }} lei</p>
                </div>
                <div class="flex justify-between text-sm font-bold">
                    <p>Total</p>
                    <p>{{ number_format($calculatedTotal, 2) }} lei</p>
                </div>
            </div>

        </div>

        <div class="mt-8 text-right">
            @if (auth()->check())
                <a href="{{ route('checkout.index') }}"
                   class="w-[150px] bg-black text-white text-center py-2 rounded-lg font-semibold hover:bg-gray-900 transition ml-4 text-xs px-4 border border-black">
                    Confirma comanda
                </a>
            @else
                <a href="{{ route('login') }}"
                   onclick="event.preventDefault(); document.getElementById('transfer-cart-form').submit();"
                   class="w-[150px] bg-black text-white text-center py-2 rounded-lg font-semibold hover:bg-gray-900 transition ml-4 text-xs px-4 border border-black">
                    Loghează-te pentru a finaliza comanda
                </a>
                <form id="transfer-cart-form" action="{{ route('set.cart.transfer') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <p class="text-xs mt-2 text-gray-600">Nu vei pierde produsele din coș.</p>
            @endif
        </div>
    </div>
@endsection
