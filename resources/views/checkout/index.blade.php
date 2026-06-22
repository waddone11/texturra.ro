@extends('layouts.base')

@section('content')
    <div class="w-full bg-[#FCFAF7] font-dm text-[#171411]">
    <div class="max-w-[1180px] mx-auto py-12 px-4 sm:px-8">
        <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#B58A43]">Checkout</p>
        <h1 class="font-display text-3xl md:text-[40px] font-semibold mb-8">Finalizează comanda</h1>

        <!-- Shipping Address -->
        <div class="border border-[#171411]/10 bg-white shadow-sm px-5 py-5 rounded-[16px] mb-6">
            <h2 class="font-display text-xl font-semibold mb-4">Adresa de livrare</h2>

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
                            <button
                                type="button"
                                data-id="{{ $address->id }}"
                                class="w-auto bg-[#171411] text-[#FCFAF7] text-center py-1 rounded-lg font-semibold hover:bg-[#B58A43] transition text-xs px-2 border border-[#171411]/25 edit-shipping-address">
                                Modifică
                            </button>

                            <form action="{{ route('address.destroy', $address->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="button"
                                    data-id="{{ $address->id }}"
                                    class="w-auto bg-[#171411] text-[#FCFAF7] text-center py-1 rounded-lg font-semibold hover:bg-[#B58A43] transition text-xs px-2 border border-[#171411]/25" onclick="return confirm('Sigur doriți să ștergeți această adresă?')">
                                    Șterge
                                </button>
                            </form>
                            @if (!$address->is_default)
                                <form action="{{ route('address.default', $address->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-white p-1 px-2 bg2 rounded text-xs shadow-xl hover:text-black">Setează ca implicită</button>
                                </form>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 inline">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                                <span class="2  text-xs">adresa implicită</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Nu există adrese de livrare salvate.</p>
                @endforelse

                <button type="button" id="addShippingAddressBtn"
                        class="w-auto bg-[#171411] text-[#FCFAF7] text-center py-2 rounded-lg font-semibold hover:bg-[#B58A43] transition text-xs px-4 border border-[#171411]/25">
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
                        class="mt-1 block w-full  rounded-md"
                        placeholder="Introduceți adresa"
                    />
                </div>
                <input type="hidden" id="shipping_lat" name="lat">
                <input type="hidden" id="shipping_lng" name="lng">
                <div class="mb-4">
                    <label for="shipping_street" class="block text-sm font-medium">Stradă</label>
                    <input id="shipping_street" name="street" type="text" class="mt-1 block w-full  rounded-md">
                </div>
                <div class="mb-4">
                    <label for="shipping_city" class="block text-sm font-medium">Oraș</label>
                    <input id="shipping_city" name="city" type="text" class="mt-1 block w-full  rounded-md">
                </div>
                <div class="mb-4">
                    <label for="shipping_county" class="block text-sm font-medium">Județ</label>
                    <input id="shipping_county" name="state" type="text" class="mt-1 block w-full  rounded-md">
                </div>
                <div class="mb-4">
                    <label for="shipping_postal_code" class="block text-sm font-medium">Cod Poștal</label>
                    <input id="shipping_postal_code" name="postal_code" type="text" class="mt-1 block w-full  rounded-md">
                </div>

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium">Nume Adresă</label>
                    <input id="name" name="name" type="text" class="mt-1 block w-full  rounded-md" placeholder="Ex: Acasă, Birou">
                </div>
                {{--                <button type="submit" class="bg text-white px-4 py-2 text-sm rounded-lg hover:bg-blue-700 transition duration-200">--}}
                {{--                    Salvează Adresa de Livrare--}}
                {{--                </button>--}}
                <button type="submit"
                        class="w-auto bg-[#171411] text-[#FCFAF7] text-center py-2 rounded-lg font-semibold hover:bg-[#B58A43] transition text-xs px-4 border border-[#171411]/25">
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
                            <input id="edit_name" name="name" type="text" class="mt-1 block w-full  text-xs rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_street" class="block text-sm font-medium">Stradă</label>
                            <input id="edit_street" name="street" type="text" class="mt-1 block w-full  rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_city" class="block text-sm font-medium">Oraș</label>
                            <input id="edit_city" name="city" type="text" class="mt-1 block w-full  rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_county" class="block text-sm font-medium">Județ</label>
                            <input id="edit_county" name="state" type="text" class="mt-1 block w-full  rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_postal_code" class="block text-sm font-medium">Cod Poștal</label>
                            <input id="edit_postal_code" name="postal_code" type="text" class="mt-1 block w-full  rounded-md">
                        </div>
                        <button type="submit" class="text-black p-1 px-2 bg2 rounded text-xs shadow-xl hover:text-white">Salvează Modificările</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Billing Address -->
        <div class="border border-[#171411]/10 bg-white shadow-sm px-5 py-5 rounded-[16px] mb-6">
            <h2 class="font-display text-xl font-semibold mb-4">Adresă de facturare</h2>

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
                            <button
                                type="button"
                                data-id="{{ $address->id }}"
                                class="w-auto bg-[#171411] text-[#FCFAF7] text-center py-1 rounded-lg font-semibold hover:bg-[#B58A43] transition text-xs px-2 border border-[#171411]/25 edit-billing-address">
                                Modifică
                            </button>

                            <form action="{{ route('invoice_address.destroy', $address->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    data-id="{{ $address->id }}"
                                    class="w-auto bg-[#171411] text-[#FCFAF7] text-center py-1 rounded-lg font-semibold hover:bg-[#B58A43] transition text-xs px-2 border border-[#171411]/25" onclick="return confirm('Sigur doriți să ștergeți această adresă?')">
                                    Șterge
                                </button>
                            </form>
                            @if (!$address->is_default)
                                <form action="{{ route('invoice_address.default', $address->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-white p-1 px-2 bg2 rounded text-xs shadow-xl hover:text-black">Setează ca implicită</button>
                                </form>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 inline">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                                <span class="2  text-xs">adresa implicită</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 mb-2">Nu există adrese de facturare salvate.</p>
                    <a
                        href="#" id="useShippingAsBilling"
                        class="w-auto bg-[#171411] text-[#FCFAF7] text-center py-2 rounded-lg font-semibold hover:bg-[#B58A43] transition text-xs px-4 border border-[#171411]/25">
                        Folosește adresa primară de livrare
                    </a>

                    <br/>
                @endforelse


                <button
                    type="button"
                    id="addBillingAddressBtn"
                    class="w-auto bg-[#171411] text-[#FCFAF7] text-center py-2 rounded-lg font-semibold hover:bg-[#B58A43] transition text-xs px-4 border border-[#171411]/25 mt-4">
                    Adaugă adresă de facturare
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
                        class="mt-1 block w-full  rounded-md"
                        placeholder="Introduceți adresa"
                    />
                </div>
                <input type="hidden" id="billing_lat" name="lat">
                <input type="hidden" id="billing_lng" name="lng">
                <div class="mb-4">
                    <label for="billing_name" class="block text-sm font-medium">Nume Adresă</label>
                    <input id="billing_name" name="name" type="text" class="mt-1 block w-full  rounded-md" placeholder="Ex: Acasă, Birou">
                </div>
                <div class="mb-4">
                    <label for="billing_street" class="block text-sm font-medium">Stradă</label>
                    <input id="billing_street" name="street" type="text" class="mt-1 block w-full  rounded-md">
                </div>
                <div class="mb-4">
                    <label for="billing_city" class="block text-sm font-medium">Oraș</label>
                    <input id="billing_city" name="city" type="text" class="mt-1 block w-full  rounded-md">
                </div>
                <div class="mb-4">
                    <label for="billing_county" class="block text-sm font-medium">Județ</label>
                    <input id="billing_county" name="state" type="text" class="mt-1 block w-full  rounded-md">
                </div>
                <div class="mb-4">
                    <label for="billing_postal_code" class="block text-sm font-medium">Cod Poștal</label>
                    <input id="billing_postal_code" name="postal_code" type="text" class="mt-1 block w-full  rounded-md">
                </div>

                <button
                    type="submit"
                    class="w-auto bg-[#171411] text-[#FCFAF7] text-center py-2 rounded-lg font-semibold hover:bg-[#B58A43] transition text-xs px-4 border border-[#171411]/25">
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
                            <input id="edit_billing_name" name="name" type="text" class="mt-1 block w-full  rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_billing_street" class="block text-sm font-medium">Stradă</label>
                            <input id="edit_billing_street" name="street" type="text" class="mt-1 block w-full  rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_billing_city" class="block text-sm font-medium">Oraș</label>
                            <input id="edit_billing_city" name="city" type="text" class="mt-1 block w-full  rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_billing_county" class="block text-sm font-medium">Județ</label>
                            <input id="edit_billing_county" name="state" type="text" class="mt-1 block w-full  rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="edit_billing_postal_code" class="block text-sm font-medium">Cod Poștal</label>
                            <input id="edit_billing_postal_code" name="postal_code" type="text" class="mt-1 block w-full  rounded-md">
                        </div>
                        <button type="submit" class="text-black p-1 px-2 bg2 rounded text-xs shadow-xl hover:text-white">
                            Salvează Modificările
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notes & Order review -->
        <div class="border border-[#171411]/10 bg-white shadow-sm px-5 py-5 rounded-[16px] mb-6">
            <h2 class="font-display text-xl font-semibold mb-4">Produse comandate</h2>

            <!-- Cart Items Summary -->
            <div class="p-4 space-y-4 bg-white rounded-lg">
                @forelse ($cartItems as $item)
                    @php
                        $isCustom = $item->length || $item->height || $item->manufactoring_type_id;
                    @endphp
                    <div class="flex items-start justify-between {{ !$loop->last ? 'border-b' : '' }} pb-4 gap-4">
                        <!-- Info -->
                        <div class="flex items-start space-x-4">
                            <img
                                src="{{ $item->product->images[0] ?? '/placeholder-image.png' }}"
                                alt="{{ $item->product->name }}"
                                class="w-16 h-16 rounded object-cover"
                            />
                            <div class="space-y-1 text-sm">
                                <p class="font-semibold">{{ $item->product->name }}</p>
                                @if ($isCustom)
                                    <ul class="text-xs text-gray-600 space-y-1">
                                        <li><strong>Lățime:</strong> {{ $item->length }} m</li>
                                        <li><strong>Înălțime:</strong> {{ $item->height }} m</li>
                                        <li><strong>Bucăți:</strong> {{ $item->pieces }}</li>
                                        <li><strong>Manoperă:</strong> {{ optional($item->manufactoringType)->name }}</li>
                                    </ul>
                                @else
                                    <p class="text-xs text-gray-600">Cantitate: {{ $item->quantity }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Total</p>
                            <p class="text-sm font-bold">
                                {{ number_format(($isCustom ? $item->price * $item->pieces : $item->price * $item->quantity), 2) }} RON
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Coșul tău este gol.</p>
                @endforelse
            </div>

            <!-- Order Summary & Voucher -->
            @php
                $calculatedSubtotal = $cartItems->sum(function ($item) {
                    return ($item->length || $item->height || $item->manufactoring_type_id)
                        ? $item->pieces * $item->price
                        : $item->quantity * $item->price;
                });

                $voucher = $cart['voucher'] ?? null;

                $calculatedDiscount = 0;
                if ($voucher) {
                    if ($voucher->discount_amount) {
                        $calculatedDiscount = $voucher->discount_amount;
                    } elseif ($voucher->discount_percentage) {
                        $calculatedDiscount = $calculatedSubtotal * ($voucher->discount_percentage / 100);
                    }
                }

                $calculatedShipping = ($calculatedSubtotal > config('app.free_shipping_min'))
                    ? 0
                    : config('app.shipping_cost');

                $calculatedTotal = $calculatedSubtotal + $calculatedShipping - $calculatedDiscount;
            @endphp


            <div class="p-6 bg-gray-50 space-y-4 rounded-lg">
                <div class="flex justify-between text-sm">
                    <p>Subtotal</p>
                    <p>{{ number_format($calculatedSubtotal, 2) }} lei</p>
                </div>

                <div class="flex justify-between text-sm">
                    <p>Transport</p>
                    <p>{{ number_format($calculatedShipping, 2) }} lei</p>
                </div>

                @if ($voucher)
                    <div class="flex justify-between text-sm text-green-600">
                        <p>Reducere ({{ $voucher->code }})</p>
                        <p>-{{ number_format($calculatedDiscount, 2) }} lei</p>
                    </div>

                    <div class="text-sm text-gray-500 mt-1">
                        <p>Cod aplicat: <strong>{{ $voucher->code }}</strong></p>
                        <p>
                            Tip reducere:
                            {{ $voucher->discount_amount ? 'sumă fixă' : 'procentaj' }}
                            ({{ $voucher->discount_amount ?? $voucher->discount_percentage }}
                            {{ $voucher->discount_amount ? 'lei' : '%' }})
                        </p>
                    </div>
                @endif

                <div class="flex justify-between text-sm font-bold pt-2 border-t mt-2">
                    <p>Total</p>
                    <p>{{ number_format($calculatedTotal, 2) }} lei</p>
                </div>

                <!-- Voucher Form (only if not applied) -->
                @if (!$cart['voucher'])
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold mb-2">Ai un cod de reducere?</h3>
                        <form action="{{ route('checkout.apply-voucher') }}" method="POST">
                            @csrf
                            <div class="flex items-center gap-2">
                                <input
                                    type="text"
                                    name="voucher_code"
                                    class="block w-full max-w-[200px] border rounded-md text-sm px-2 py-1"
                                    placeholder="Introdu codul"
                                />
                                <button
                                    type="submit"
                                    class="bg-[#171411] text-[#FCFAF7] px-4 py-2 text-xs rounded-lg font-semibold border border-[#171411]/25 hover:bg-[#B58A43] transition">
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
                @endif
            </div>





            <div class="mt-4 p-4 rounded-lg">
                Vrei produsele sau cantitatea din cosul de cumparaturi? . <a href="{{ route('cart.index') }}" class="text-sm  font-bold hover:underline">Click aici pentru modificare coș cumparaturi</a>
            </div>

        </div>


        <!-- Payment -->
{{--        <div class="border border-[#171411]/10 bg-white shadow-sm px-5 py-5 rounded-[16px] mb-6">--}}
{{--            <h2 class="font-display text-xl font-semibold mb-4">Metode de plată</h2>--}}
{{--            <div class="space-y-4 mb-2" id="payment-options">--}}
{{--                <!-- Payment Accordion -->--}}
{{--                <div data-payment="online" class="payment-option active">--}}
{{--                    <label class="flex items-center space-x-2 cursor-pointer">--}}
{{--                        <input type="radio" name="payment_method" value="online" class="hidden peer">--}}
{{--                        <div class="w-5 h-5 border-2 border-black rounded-full flex justify-center items-center bg-[#171411] text-[#FCFAF7]">--}}
{{--                            <div class="w-5 h-5 bg-white rounded-full"></div>--}}
{{--                        </div>--}}
{{--                        <div class="flex items-center space-x-2 2">--}}
{{--                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">--}}
{{--                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />--}}
{{--                            </svg>--}}
{{--                            <h3 class="font-extrabold">Plată Online</h3>--}}
{{--                        </div>--}}
{{--                    </label>--}}
{{--                    <p class="text-sm  font-bold mt-2 ml-8">Plătește rapid și sigur folosind cardul tău bancar.</p>--}}
{{--                    <span class="text-xs text-black ml-8">Plata se va face prin intermediul platformei de plăți online, vei fi redirecționat pe o altă pagină.</span>--}}
{{--                </div>--}}
{{--                <div data-payment="courier" class="payment-option">--}}
{{--                    <label class="flex items-center space-x-2 cursor-pointer">--}}
{{--                        <input type="radio" name="payment_method" value="courier" class="hidden peer">--}}
{{--                        <div class="w-5 h-5 border-2 border-gray-400 rounded-full flex justify-center items-center">--}}
{{--                            <div class="w-2.5 h-2.5 bg-white rounded-full"></div>--}}
{{--                        </div>--}}
{{--                        <div class="flex items-center space-x-2">--}}
{{--                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">--}}
{{--                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />--}}
{{--                            </svg>--}}
{{--                            <h3 class="font-medium">Plată la livrare</h3>--}}
{{--                        </div>--}}
{{--                    </label>--}}
{{--                    <p class="text-sm text-gray-500 mt-2 ml-8">Plătește direct curierului în momentul livrării.</p>--}}
{{--                    <span class="text-xs text-black ml-8">Vom genera un AWB și îl vom trimite pe emailul dumneavoastră, împreună cu factura proformă și detaliile de plată.</span>--}}
{{--                </div>--}}
{{--                <div data-payment="bank_transfer" class="payment-option">--}}
{{--                    <label class="flex items-center space-x-2 cursor-pointer">--}}
{{--                        <input type="radio" name="payment_method" value="bank_transfer" class="hidden peer">--}}
{{--                        <div class="w-5 h-5 border-2 border-gray-400 rounded-full flex justify-center items-center">--}}
{{--                            <div class="w-2.5 h-2.5 bg-white rounded-full"></div>--}}
{{--                        </div>--}}
{{--                        <div class="flex items-center space-x-2">--}}
{{--                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">--}}
{{--                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />--}}
{{--                            </svg>--}}
{{--                            <h3 class="font-medium">Transfer Bancar</h3>--}}
{{--                        </div>--}}
{{--                    </label>--}}
{{--                    <p class="text-sm text-gray-500 mt-2 ml-8">Detaliile de plată vor fi afișate după confirmarea comenzii.</p>--}}
{{--                    <span class="text-xs text-black ml-8">Verificați emailul pentru factura proformă și detaliile de plată.</span>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="border border-[#171411]/10 bg-white shadow-sm px-5 py-5 rounded-[16px] mb-6">
            <h2 class="font-display text-xl font-semibold mb-4">Metode de plată</h2>

            <div class="space-y-4 mb-2" id="payment-options">
                {{-- LANSARE: DOAR ramburs (plata la livrare). Plata Online (card) și Transferul Bancar
                     sunt ASCUNSE până la integrarea unui gateway de plată real — codul lor e păstrat
                     comentat mai jos pentru reactivare. NU s-a atins logica de creare comandă. --}}

                <!-- Cash on Delivery (ramburs) — singura metodă activă -->
                <label class="flex items-start space-x-3 cursor-pointer">
                    <input type="radio" name="payment_method" value="courier" class="hidden peer" checked>

                    <div class="w-5 h-5 border-2 rounded-full flex justify-center items-center
                        peer-checked:bg-black peer-checked:border-black border-gray-400">
                        <div class="w-2.5 h-2.5 rounded-full
                            bg-transparent peer-checked:bg-white"></div>
                    </div>

                    <div class="flex flex-col">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                            <h3 class="font-medium peer-checked:font-extrabold peer-checked:text-black">Plată la livrare (ramburs)</h3>
                        </div>
                        <p class="text-sm mt-1 text-gray-600 peer-checked:font-bold">Plătești direct curierului în momentul livrării.</p>
                        <span class="text-xs text-gray-500">AWB + factură proformă vor fi trimise pe email.</span>
                    </div>
                </label>

                {{-- ASCUNSE până la integrarea gateway-ului de plată (NU șterge — se reactivează):
                <label class="flex items-start space-x-3 cursor-pointer"> <!-- Plată Online (card) -->
                    <input type="radio" name="payment_method" value="online" class="hidden peer">
                    ... card online — reactivează după integrare Netopia/Stripe ...
                </label>
                <label class="flex items-start space-x-3 cursor-pointer"> <!-- Transfer Bancar -->
                    <input type="radio" name="payment_method" value="bank_transfer" class="hidden peer">
                    ... transfer bancar — reactivează când se confirmă fluxul ...
                </label>
                --}}
            </div>
        </div>

        <!-- Submit Button and collect all data-->
        <div class="border border-[#171411]/10 bg-white shadow-sm px-5 py-5 rounded-[16px] mb-6">
            <form action="{{ route('order.store') }}" method="POST">
                @csrf
                <input type="hidden" id="selected_shipping_address_id" name="shipping_address_id" value="{{ $addresses->firstWhere('is_default', true)->id ?? '' }}">
                <input type="hidden" id="selected_billing_address_id" name="billing_address_id" value="{{ $invoiceAddresses->firstWhere('is_default', true)->id ?? '' }}">
                <input type="hidden" name="payment_method" id="payment_method" value="courier"> <!-- Default payment method -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Note opționale</label>
                    <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
                </div>
                <button
                    type="submit"
                    class="w-auto bg-[#171411] text-[#FCFAF7] text-center py-2 rounded-lg font-semibold hover:bg-[#B58A43] transition text-sm px-4 border border-[#171411]/25 edit-shipping-address">
                    Finalizează Comanda
                </button>
            </form>

        </div>

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

                    label.classList.toggle('2', isActive);
                    label.classList.toggle('font-extrabold', isActive);

                    description.classList.toggle('', isActive);
                    description.classList.toggle('font-bold', isActive);

                    extraInfo.classList.toggle('text-black', isActive);

                    h3.classList.toggle('2', isActive);
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
        const radios = document.querySelectorAll('input[name="payment_method"]');
        const paymentMethodInput = document.getElementById('payment_method');

        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.checked) {
                    paymentMethodInput.value = this.value;
                }
            });

            // Set initial value
            if (radio.checked) {
                paymentMethodInput.value = radio.value;
            }
        });
    });



</script>
