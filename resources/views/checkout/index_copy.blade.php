@extends('layouts.base')

@section('content')
    <div class="max-w-7xl mx-auto py-12 px-2 sm:px-0">
        <h1 class="text-2xl font-bold mb-8">Finalizează comanda</h1>

        <!-- Shipping Address -->
        <div class="border bg-gray-50 shadow-xl px-4 py-2 rounded mb-6">
            <h2 class="text-lg font-semibold mb-4">Adresa de livrare</h2>

            <div class="mb-6">
                @forelse ($addresses as $address)
                    <div class="flex justify-between items-center mb-2">
                        <label class="block w-3/4 text-xs">
                            <input type="radio" name="shipping_address_id" value="{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }}>
                            <span class="ml-2 text-xs font-bold">{{ $address->name }}</span> • {{ $address->street }} • {{ $address->city }} • {{ $address->state }} • {{ $address->postal_code }}
                            @if ($address->is_default)
                                <span class="text-xs text-green-600">(Default)</span>
                            @endif
                        </label>
                        <div class="space-x-4 w-1/4">
                            <button type="button" data-id="{{ $address->id }}" class="text-white p-1 px-2 bgOurColor2 rounded text-xs shadow-xl hover:text-black edit-shipping-address">Modifică</button>
                            <form action="{{ route('address.destroy', $address->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-white p-1 px-2 bgOurColor2 rounded text-xs shadow-xl hover:text-black" onclick="return confirm('Sigur doriți să ștergeți această adresă?')">Șterge</button>
                            </form>
                            @if (!$address->is_default)
                                <form action="{{ route('address.default', $address->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-white p-1 px-2 bgOurColor2 rounded text-xs shadow-xl hover:text-black">Setează ca implicită</button>
                                </form>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 inline">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                                <span class="ourColor2  text-xs">adresa implicită</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Nu există adrese de livrare salvate.</p>
                @endforelse

                <button type="button" id="addShippingAddressBtn"
                        class="w-auto bg-black text-white text-center py-2 rounded-lg font-semibold hover:bg-gray-900 transition text-xs px-4 border border-black">
                    Adaugă adresă de livrare
                </button>
            </div>

            <form action="{{ route('address.store') }}" method="POST" id="addShippingAddressForm" class="hidden mt-4">
                @csrf
                <div class="mb-4">
                    <label for="search_shipping_address" class="block text-sm font-medium">Caută adresă</label>
                    <input
                        id="search_shipping_address"
                        name="search_address"
                        type="text"
                        class="mt-1 block w-full borderOurColor2 rounded-md"
                        placeholder="Introduceți adresa"
                    />
                </div>
                <input type="hidden" id="shipping_lat" name="lat">
                <input type="hidden" id="shipping_lng" name="lng">
                <div class="mb-4">
                    <label for="shipping_street" class="block text-sm font-medium">Stradă</label>
                    <input id="shipping_street" name="street" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                </div>
                <div class="mb-4">
                    <label for="shipping_city" class="block text-sm font-medium">Oraș</label>
                    <input id="shipping_city" name="city" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                </div>
                <div class="mb-4">
                    <label for="shipping_county" class="block text-sm font-medium">Județ</label>
                    <input id="shipping_county" name="state" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                </div>
                <div class="mb-4">
                    <label for="shipping_postal_code" class="block text-sm font-medium">Cod Poștal</label>
                    <input id="shipping_postal_code" name="postal_code" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                </div>

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium">Nume Adresă</label>
                    <input id="name" name="name" type="text" class="mt-1 block w-full borderOurColor2 rounded-md" placeholder="Ex: Acasă, Birou">
                </div>
                {{--                <button type="submit" class="bgOurColor text-white px-4 py-2 text-sm rounded-lg hover:bg-blue-700 transition duration-200">--}}
                {{--                    Salvează Adresa de Livrare--}}
                {{--                </button>--}}
                <button type="submit"
                        class="w-auto bg-black text-white text-center py-2 rounded-lg font-semibold hover:bg-gray-900 transition text-xs px-4 border border-black">
                    Salvează Adresa de Livrare
                </button>
            </form>

            <!-- Modifică Adresa Modal -->
            <div id="editShippingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Modifică Adresa</h3>
                        <button id="closeShippingModal" class="text-gray-500 hover:text-gray-800">&times;</button>
                    </div>
                    <form id="editShippingForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="edit_address_id" name="address_id">
                        <div class="mb-4">
                            <label for="edit_name" class="block text-sm font-medium">Nume Adresă</label>
                            <input id="edit_name" name="name" type="text" class="mt-1 block w-full borderOurColor2 text-xs rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_street" class="block text-sm font-medium">Stradă</label>
                            <input id="edit_street" name="street" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_city" class="block text-sm font-medium">Oraș</label>
                            <input id="edit_city" name="city" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_county" class="block text-sm font-medium">Județ</label>
                            <input id="edit_county" name="state" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_postal_code" class="block text-sm font-medium">Cod Poștal</label>
                            <input id="edit_postal_code" name="postal_code" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                        </div>
                        <button type="submit" class="text-black p-1 px-2 bgOurColor2 rounded text-xs shadow-xl hover:text-white">Salvează Modificările</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Billing Address -->
        <div class="border bg-gray-50 shadow-xl p-4 py-2 rounded mb-6">
            <h2 class="text-lg font-semibold mb-4">Adresă de facturare</h2>

            <div class="mb-6">
                @forelse ($invoiceAddresses as $address)
                    <div class="flex justify-between items-center mb-2">
                        <label class="block w-3/4 text-xs">
                            <input type="radio" name="billing_address_id" value="{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }}>
                            <span class="ml-2 text-xs font-bold">{{ $address->name }}</span> • {{ $address->street }} • {{ $address->city }} • {{ $address->state }} • {{ $address->postal_code }}
                            @if ($address->is_default)
                                <span class="text-xs text-green-600">(Default)</span>
                            @endif
                        </label>
                        <div class="space-x-4 w-1/4">
                            <button type="button" data-id="{{ $address->id }}" class="text-white p-1 px-2 bgOurColor2 rounded text-xs shadow-xl hover:text-black edit-billing-address">Modifică</button>
                            <form action="{{ route('invoice_address.destroy', $address->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-white p-1 px-2 bgOurColor2 rounded text-xs shadow-xl hover:text-black" onclick="return confirm('Sigur doriți să ștergeți această adresă?')">Șterge</button>
                            </form>
                            @if (!$address->is_default)
                                <form action="{{ route('invoice_address.default', $address->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-white p-1 px-2 bgOurColor2 rounded text-xs shadow-xl hover:text-black">Setează ca implicită</button>
                                </form>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 inline">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                                <span class="ourColor2  text-xs">adresa implicită</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 mb-2">Nu există adrese de facturare salvate.</p>
                    <a href="#" id="useShippingAsBilling" class="text-black mt-3 p-1 px-2 bgOurColor2 rounded text-xs shadow-xl hover:text-white">
                        Folosește adresa primară de livrare
                    </a>
                    <br/>
                @endforelse
                <button type="button" id="addBillingAddressBtn" class="mt-6 text-black p-1 px-2 bgOurColor2 rounded text-xs shadow-xl hover:text-white">
                    Adaugă adresă de facturare
                </button>

                <button type="button"
                        id="addBillingAddressBtn"
                        class="w-auto bg-black text-white text-center py-2 rounded-lg font-semibold hover:bg-gray-900 transition text-xs px-4 border border-black">
                    Salvează Adresa de Livrare
                </button>
            </div>

            <form action="{{ route('invoice_address.store') }}" method="POST" id="addBillingAddressForm" class="hidden mt-4">
                @csrf
                <div class="mb-4">
                    <label for="search_billing_address" class="block text-sm font-medium">Caută adresă</label>
                    <input
                        id="search_billing_address"
                        name="search_address"
                        type="text"
                        class="mt-1 block w-full borderOurColor2 rounded-md"
                        placeholder="Introduceți adresa"
                    />
                </div>
                <input type="hidden" id="billing_lat" name="lat">
                <input type="hidden" id="billing_lng" name="lng">
                <div class="mb-4">
                    <label for="billing_name" class="block text-sm font-medium">Nume Adresă</label>
                    <input id="billing_name" name="name" type="text" class="mt-1 block w-full borderOurColor2 rounded-md" placeholder="Ex: Acasă, Birou">
                </div>
                <div class="mb-4">
                    <label for="billing_street" class="block text-sm font-medium">Stradă</label>
                    <input id="billing_street" name="street" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                </div>
                <div class="mb-4">
                    <label for="billing_city" class="block text-sm font-medium">Oraș</label>
                    <input id="billing_city" name="city" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                </div>
                <div class="mb-4">
                    <label for="billing_county" class="block text-sm font-medium">Județ</label>
                    <input id="billing_county" name="state" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                </div>
                <div class="mb-4">
                    <label for="billing_postal_code" class="block text-sm font-medium">Cod Poștal</label>
                    <input id="billing_postal_code" name="postal_code" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                </div>
                <button type="submit" class="bgOurColor text-white px-4 py-2 text-sm rounded-lg hover:bg-blue-700 transition duration-200">
                    Salvează Adresa de Facturare
                </button>
            </form>

            <!-- Edit Billing Address Modal -->
            <div id="editBillingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Modifică Adresa</h3>
                        <button id="closeBillingModal" class="text-gray-500 hover:text-gray-800">&times;</button>
                    </div>
                    <form id="editBillingForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="edit_billing_address_id" name="address_id">
                        <div class="mb-4">
                            <label for="edit_billing_name" class="block text-sm font-medium">Nume Adresă</label>
                            <input id="edit_billing_name" name="name" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_billing_street" class="block text-sm font-medium">Stradă</label>
                            <input id="edit_billing_street" name="street" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_billing_city" class="block text-sm font-medium">Oraș</label>
                            <input id="edit_billing_city" name="city" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_billing_county" class="block text-sm font-medium">Județ</label>
                            <input id="edit_billing_county" name="state" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_billing_postal_code" class="block text-sm font-medium">Cod Poștal</label>
                            <input id="edit_billing_postal_code" name="postal_code" type="text" class="mt-1 block w-full borderOurColor2 rounded-md">
                        </div>
                        <button type="submit" class="text-black p-1 px-2 bgOurColor2 rounded text-xs shadow-xl hover:text-white">
                            Salvează Modificările
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notes & Order review -->
        <div class="border bg-gray-50 shadow-xl p-4 py-2 pb-6 rounded mb-6">
            <h2 class="text-lg font-semibold mb-4">Produse comandate</h2>

            <!-- Cart Items -->
            <div class="p-4 space-y-4 bg-white shadow rounded-lg">
                @forelse ($cartItems as $item)
                    <div class="flex items-center justify-between {{ !$loop->last ? 'border-b' : '' }} pb-4">
                        <!-- Product Info -->
                        <div class="flex items-center space-x-4">
                            <img
                                src="{{ $item->product->images[0] ?? '/placeholder-image.png' }}"
                                alt="{{ $item->product->name }}"
                                class="w-16 h-16 rounded"
                            />
                            <div>
                                <p class="text-sm font-semibold">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500">Cantitate: {{ $item->quantity }}</p>
                            </div>
                        </div>
                        <!-- Price -->
                        <p class="text-sm font-bold">{{ number_format($item->quantity * $item->price, 2) }} RON</p>
                    </div>
                @empty
                    <p class="text-gray-500">Coșul tău este gol.</p>
                @endforelse
            </div>

            <!-- Order Summary & Voucher >-->
            <div class="mt-4 p-4 border rounded-lg">
                <div class="flex justify-between text-sm">
                    <p>Subtotal</p>
                    <p>{{ number_format($cart['subtotal'], 2) }} RON</p>
                </div>
                <div class="flex justify-between text-sm">
                    <p>Transport</p>
                    <p>{{ number_format($cart['shipping'], 2) }} RON</p>
                </div>

                @if ($cart['voucher'])
                    <!-- Voucher Details -->
                    <div class="flex justify-between text-sm text-green-600">
                        <p>Reducere ({{ $cart['voucher']->name }})</p>
                        <p>-{{ number_format($cart['discount'], 2) }} RON</p>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        <p>Cod aplicat: <strong>{{ $cart['voucher']->code }}</strong></p>
                        <p>
                            Tip reducere:
                            {{ $cart['voucher']->discount_amount ? 'sumă fixă' : 'procentaj' }}
                            ({{ $cart['voucher']->discount_amount ?? $cart['voucher']->discount_percentage }}
                            {{ $cart['voucher']->discount_amount ? 'lei' : '%' }})
                        </p>

                    </div>
                @endif

                <div class="flex justify-between text-sm font-bold">
                    <p>Total</p>
                    <p>{{ number_format($cart['total'], 2) }} RON</p>
                </div>

                <!-- Voucher -->
                @if (!$cart['voucher'])
                    <!-- Show voucher input if no voucher is applied -->
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold mb-4 block">Ai un cod de reducere?</h3>
                        <form action="{{ route('checkout.apply-voucher') }}" method="POST">
                            @csrf
                            <div class="flex items-center space-x-2">
                                <input
                                    type="text"
                                    name="voucher_code"
                                    class="block w-48 border rounded-md p-1"
                                    placeholder="Introdu codul de reducere"
                                />
                                <button
                                    type="submit"
                                    class="bgOurColor text-white px-4 py-2 text-sm rounded-lg hover:bg-blue-700 transition duration-200"
                                >
                                    Aplică
                                </button>
                            </div>
                            @error('voucher_code')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                            @if (session('success'))
                                <p class="text-green-500 text-sm mt-2">{{ session('success') }}</p>
                            @endif
                        </form>
                    </div>
                @else
                    <!-- Hide voucher input and button if a voucher is already applied -->
                    <div class="mt-4 text-sm text-green-600">
                        <p>Cod de reducere aplicat: <strong>{{ $cart['voucher']->code }}</strong></p>
                    </div>
                @endif
            </div>




            <div class="mt-4 p-4 border rounded-lg">
                Vrei produsele sau cantitatea din cosul de cumparaturi? . <a href="{{ route('cart.index') }}" class="text-sm ourColor font-bold hover:underline">Click aici pentru modificare coș cumparaturi</a>
            </div>

        </div>


        <!-- Payment -->
        <div class="border bg-gray-50 shadow-xl p-4 py-2 rounded mb-6">
            <h2 class="text-lg font-semibold mb-4">Metode de plată</h2>
            <div class="space-y-4 mb-2" id="payment-options">
                <!-- Payment Accordion -->
                {{--                <div data-payment="online" class="payment-option active">--}}
                {{--                    <label class="flex items-center space-x-2 cursor-pointer">--}}
                {{--                        <input type="radio" name="payment_method" value="online" class="hidden peer">--}}
                {{--                        <div class="w-5 h-5 border-2 border-black rounded-full flex justify-center items-center bg-black text-white">--}}
                {{--                            <div class="w-2.5 h-2.5 bg-white rounded-full"></div>--}}
                {{--                        </div>--}}
                {{--                        <div class="flex items-center space-x-2 ourColor2">--}}
                {{--                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">--}}
                {{--                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />--}}
                {{--                            </svg>--}}
                {{--                            <h3 class="font-extrabold">Plată Online</h3>--}}
                {{--                        </div>--}}
                {{--                    </label>--}}
                {{--                    <p class="text-sm ourColor font-bold mt-2 ml-8">Plătește rapid și sigur folosind cardul tău bancar.</p>--}}
                {{--                    <span class="text-xs text-black ml-8">Plata se va face prin intermediul platformei de plăți online, vei fi redirecționat pe o altă pagină.</span>--}}
                {{--                </div>--}}
                <div data-payment="courier" class="payment-option">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="payment_method" value="courier" class="hidden peer">
                        <div class="w-5 h-5 border-2 border-gray-400 rounded-full flex justify-center items-center">
                            <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                            <h3 class="font-medium">Plată la livrare</h3>
                        </div>
                    </label>
                    <p class="text-sm text-gray-500 mt-2 ml-8">Plătește direct curierului în momentul livrării.</p>
                    <span class="text-xs text-black ml-8">Vom genera un AWB și îl vom trimite pe emailul dumneavoastră, împreună cu factura proformă și detaliile de plată.</span>
                </div>
                <div data-payment="bank_transfer" class="payment-option">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="payment_method" value="bank_transfer" class="hidden peer">
                        <div class="w-5 h-5 border-2 border-gray-400 rounded-full flex justify-center items-center">
                            <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                            </svg>
                            <h3 class="font-medium">Transfer Bancar</h3>
                        </div>
                    </label>
                    <p class="text-sm text-gray-500 mt-2 ml-8">Detaliile de plată vor fi afișate după confirmarea comenzii.</p>
                    <span class="text-xs text-black ml-8">Verificați emailul pentru factura proformă și detaliile de plată.</span>
                </div>
            </div>
        </div>

        <!-- Submit Button and collect all data-->
        <div class="border bg-gray-50 shadow-xl p-4 py-2 rounded mb-6">
            <form action="{{ route('order.store') }}" method="POST">
                @csrf
                <input type="hidden" id="selected_shipping_address_id" name="shipping_address_id" value="{{ $addresses->firstWhere('is_default', true)->id ?? '' }}">
                <input type="hidden" id="selected_billing_address_id" name="billing_address_id" value="{{ $invoiceAddresses->firstWhere('is_default', true)->id ?? '' }}">
                <input type="hidden" name="payment_method" id="payment_method" value="online"> <!-- Default payment method -->
                <div class="border p-4 rounded mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Note opționale</label>
                    <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
                </div>
                <button type="submit" class="bgOurColor text-white px-4 py-2 mt-4 text-sm rounded-lg hover:bg-blue-700 transition duration-200 w-full">
                    Finalizează Comanda
                </button>
            </form>

        </div>

    </div>

@endsection


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDeRklLwCXsYbvUm3Ce4G2iUa1rNx_RxRA&libraries=places"></script>
<script>

    document.addEventListener('DOMContentLoaded', function () {
        // Toggle Shipping Address Form
        const addShippingAddressBtn = document.getElementById('addShippingAddressBtn');
        const addShippingAddressForm = document.getElementById('addShippingAddressForm');

        if (addShippingAddressBtn && addShippingAddressForm) {
            addShippingAddressBtn.addEventListener('click', () => {
                addShippingAddressForm.classList.toggle('hidden');
            });
        }

        // Toggle Billing Address Form
        const addBillingAddressBtn = document.getElementById('addBillingAddressBtn');
        const addBillingAddressForm = document.getElementById('addBillingAddressForm');

        if (addBillingAddressBtn && addBillingAddressForm) {
            addBillingAddressBtn.addEventListener('click', () => {
                addBillingAddressForm.classList.toggle('hidden');
            });
        }

        // Google Maps API Autocomplete
        const initAutocomplete = (inputId, latId, lngId, streetId, cityId, countyId, postalCodeId) => {
            const input = document.getElementById(inputId);
            if (input) {
                const autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    document.getElementById(latId).value = place.geometry.location.lat();
                    document.getElementById(lngId).value = place.geometry.location.lng();
                    place.address_components.forEach(component => {
                        const types = component.types;
                        if (types.includes('route')) {
                            document.getElementById(streetId).value = component.long_name;
                        }
                        if (types.includes('locality')) {
                            document.getElementById(cityId).value = component.long_name;
                        }
                        if (types.includes('administrative_area_level_1')) {
                            document.getElementById(countyId).value = component.long_name;
                        }
                        if (types.includes('postal_code')) {
                            document.getElementById(postalCodeId).value = component.long_name;
                        }
                    });
                });
            }
        };

        // Initialize Google Maps API Autocomplete
        initAutocomplete(
            'search_shipping_address',
            'shipping_lat',
            'shipping_lng',
            'shipping_street',
            'shipping_city',
            'shipping_county',
            'shipping_postal_code'
        );

        initAutocomplete(
            'search_billing_address',
            'billing_lat',
            'billing_lng',
            'billing_street',
            'billing_city',
            'billing_county',
            'billing_postal_code'
        );
    });

    document.addEventListener('DOMContentLoaded', function () {
        const editShippingModal = document.getElementById('editShippingModal');
        const editShippingForm = document.getElementById('editShippingForm');
        const closeShippingModal = document.getElementById('closeShippingModal');

        document.querySelectorAll('.edit-shipping-address').forEach(button => {
            button.addEventListener('click', function () {
                const addressId = this.dataset.id;
                const address = @json($addresses).find(addr => addr.id == addressId);

                if (address) {
                    document.getElementById('edit_address_id').value = address.id;
                    document.getElementById('edit_name').value = address.name;
                    document.getElementById('edit_street').value = address.street;
                    document.getElementById('edit_city').value = address.city;
                    document.getElementById('edit_county').value = address.state;
                    document.getElementById('edit_postal_code').value = address.postal_code;

                    editShippingForm.action = `/address/${address.id}/edit`;
                    editShippingModal.classList.remove('hidden');
                }
            });
        });

        closeShippingModal.addEventListener('click', () => {
            editShippingModal.classList.add('hidden');
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Prefill billing address form with default shipping address
        const useShippingAsBillingBtn = document.getElementById('useShippingAsBilling');
        const editBillingModal = document.getElementById('editBillingModal');
        const editBillingForm = document.getElementById('editBillingForm');
        const closeBillingModal = document.getElementById('closeBillingModal');
        const billingForm = document.getElementById('addBillingAddressForm');

        if (useShippingAsBillingBtn && editBillingForm) {
            useShippingAsBillingBtn.addEventListener('click', () => {
                const defaultShippingAddress = @json($addresses).find(addr => addr.is_default);

                if (defaultShippingAddress) {
                    document.getElementById('billing_name').value = defaultShippingAddress.name;
                    document.getElementById('billing_street').value = defaultShippingAddress.street;
                    document.getElementById('billing_city').value = defaultShippingAddress.city;
                    document.getElementById('billing_county').value = defaultShippingAddress.state;
                    document.getElementById('billing_postal_code').value = defaultShippingAddress.postal_code;

                    billingForm.classList.remove('hidden');
                } else {
                    alert('Nu există o adresă de livrare implicită.');
                }
            });
        }

        document.querySelectorAll('.edit-billing-address').forEach(button => {
            button.addEventListener('click', function () {
                const addressId = this.dataset.id;
                const address = @json($invoiceAddresses).find(addr => addr.id == addressId); // Corrected to use $invoiceAddresses

                if (address) {
                    document.getElementById('edit_billing_address_id').value = address.id;
                    document.getElementById('edit_billing_name').value = address.name;
                    document.getElementById('edit_billing_street').value = address.street;
                    document.getElementById('edit_billing_city').value = address.city;
                    document.getElementById('edit_billing_county').value = address.state;
                    document.getElementById('edit_billing_postal_code').value = address.postal_code;

                    editBillingForm.action = `/invoice-address/${address.id}/edit`;
                    editBillingModal.classList.remove('hidden');
                } else {
                    alert("Adresă de facturare nu a fost găsită.");
                }
            });
        });

        closeBillingModal.addEventListener('click', () => {
            editBillingModal.classList.add('hidden');
        });

    });

    document.addEventListener('DOMContentLoaded', function () {
        const paymentOptions = document.querySelectorAll('.payment-option');

        paymentOptions.forEach(option => {
            const input = option.querySelector('input[type="radio"]');

            input.addEventListener('change', function () {
                paymentOptions.forEach(opt => {
                    const isActive = opt === option; // Check if the current option is active

                    opt.classList.toggle('active', isActive);

                    const circle = opt.querySelector('div.w-5');
                    const label = opt.querySelector('div.flex.items-center.space-x-2');
                    const description = opt.querySelector('p');
                    const extraInfo = opt.querySelector('span');
                    const h3 = opt.querySelector('h3');

                    // Toggle classes based on active state
                    circle.classList.toggle('border-gray-400', !isActive);
                    circle.classList.toggle('border-black', isActive);
                    circle.classList.toggle('bg-black', isActive);
                    circle.classList.toggle('text-white', isActive);

                    label.classList.toggle('ourColor2', isActive);
                    label.classList.toggle('font-extrabold', isActive);

                    description.classList.toggle('ourColor', isActive);
                    description.classList.toggle('font-bold', isActive);

                    extraInfo.classList.toggle('text-black', isActive);

                    h3.classList.toggle('ourColor2', isActive);
                    h3.classList.toggle('font-extrabold', isActive);
                    h3.classList.toggle('font-medium', !isActive);
                });
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Update selected shipping address
        document.querySelectorAll('input[name="shipping_address_id"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.getElementById('selected_shipping_address_id').value = this.value;
            });
        });

        // Update selected billing address
        document.querySelectorAll('input[name="billing_address_id"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.getElementById('selected_billing_address_id').value = this.value;
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const paymentOptions = document.querySelectorAll('.payment-option input[type="radio"]');

        paymentOptions.forEach(option => {
            option.addEventListener('change', function () {
                document.getElementById('payment_method').value = this.value;
            });
        });
    });


</script>
