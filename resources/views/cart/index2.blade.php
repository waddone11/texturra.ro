@extends('layouts.base')

@section('content')
    <div class="max-w-7xl mx-auto py-12 px-2 sm:px-0">
        <h1 class="text-2xl font-bold mb-8">Cosul tau de cumparaturi</h1>

        <!-- Flash Message -->
        @if (session('flashMessage'))
            <div
                class="p-4 mb-4 text-sm font-medium rounded-lg
                       {{ session('flashMessage.type') === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                {{ session('flashMessage.message') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg">
            <div class="p-6 space-y-6">
                <!-- Cart Items -->
                @forelse ($cartItems as $item)
                    <div class="flex items-center justify-between border-b pb-4">
                        <!-- Product Info -->
                        <div class="flex items-center space-x-4">
                            <img
                                src="{{ $item->product->images[0] ?? '/placeholder-image.png' }}"
                                alt="{{ $item->product->name }}"
                                class="w-16 h-16 rounded p2"
                            />

                            <div>
                                <p class="text-lg font-semibold">{{ $item->product->name }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-6">
                            <!-- Quantity -->
                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center">
                                @csrf
                                @method('PATCH')

                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                       class="w-12 text-center text-xs border border-gray-300 mr-1 rounded focus:outline-none p-1">

                                <button type="submit" class="px-2 py-1 bgOurColor2 text-xs rounded text-white font-bold">
                                    Actualizează
                                </button>
                            </form>

                            <!-- Price -->
                            <p class="text-lg font-bold">{{ number_format($item->quantity * $item->price, 2) }} RON</p>

                            <!-- Delete Button -->
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Your cart is empty.</p>
                @endforelse
            </div>

            <!-- Summary Section -->
            <div class="p-6 bg-gray-50 space-y-4 rounded-b-lg">
                <div class="flex justify-between text-xs">
                    <p>Subtotal</p>
                    <p>{{ number_format($subtotal, 2) }} RON</p>
                </div>
                <div class="flex justify-between text-xs">
                    <p>Shipping</p>
                    <p>{{ number_format($shipping, 2) }} RON</p>
                </div>
                <div class="flex justify-between text-sm font-bold">
                    <p>Total</p>
                    <p>{{ number_format($total, 2) }} RON</p>
                </div>
            </div>
        </div>

        <div class="mt-8 text-right">
            <div class="mt-8 text-right">
                @if (auth()->check())
                    <a href="{{ route('checkout.index') }}" class="bgOurColor text-white px-4 py-2 text-sm rounded-lg hover:bg-blue-700 transition duration-200">
                        Confirma comanda
                    </a>
                @else
{{--                    {{dd(session()->getId());}}--}}
                    <a href="{{ route('login') }}"
                       onclick="event.preventDefault(); document.getElementById('transfer-cart-form').submit();"
                       class="bgOurColor text-white px-4 py-2 text-sm rounded-lg hover:bg-blue-700 transition duration-200">
                        Loghează-te pentru a finaliza comanda
                    </a>
                    <form id="transfer-cart-form" action="{{ route('set.cart.transfer') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <p>Nu vei pierde produsele din cos.</p>
                @endif

            </div>

        </div>
    </div>
@endsection
