@extends('layouts.base')

@section('content')
    {{-- Cart — 2026 redesign (shell only). MONEY LOGIC UNTOUCHED: Cart::lineTotal(), update
         forms (cart.update / cart.update.custom), delete (cart.remove), subtotal sum, checkout
         + guest cart-transfer are identical. Old shell kept in cart/index-old.blade.php. --}}
    <div class="w-full bg-[#FCFAF7] font-dm text-[#171411]">
        <div class="mx-auto max-w-[1180px] px-4 py-12 sm:px-8 md:py-16">

            <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#B58A43]">Coș</p>
            <h1 class="font-display text-3xl font-semibold leading-tight text-[#171411] md:text-[40px]">Coșul tău</h1>

            @if (session('flashMessage'))
                <div class="mt-6 rounded-lg px-4 py-3 text-sm font-medium
                            {{ session('flashMessage.type') === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-[#B58A43]/10 text-[#8c6529] border border-[#B58A43]/30' }}">
                    {{ session('flashMessage.message') }}
                </div>
            @endif

            @php
                $calculatedSubtotal = $cartItems->sum(fn ($item) => $item->lineTotal());
                $calculatedShipping = ($calculatedSubtotal > config('app.free_shipping_min')) ? 0 : config('app.shipping_cost');
                $calculatedTotal = $calculatedSubtotal + $calculatedShipping;
            @endphp

            @if ($cartItems->isEmpty())
                {{-- Empty state --}}
                <div class="mt-10 flex flex-col items-center rounded-[18px] border border-[#171411]/10 bg-white py-20 text-center">
                    <span class="grid h-16 w-16 place-items-center rounded-full bg-[#B58A43]/10 text-[#8c6529]">
                        <i class="fa-solid fa-cart-shopping fa-lg"></i>
                    </span>
                    <h2 class="mt-5 font-display text-2xl font-semibold">Coșul tău e gol</h2>
                    <p class="mt-2 text-sm text-[#5f594f]">Descoperă perdelele, draperiile și textilele noastre premium.</p>
                    <a href="{{ route('products.category', ['slug' => 'perdele']) }}"
                       class="mt-6 inline-flex min-h-[46px] items-center rounded-md bg-[#171411] px-7 text-[13px] font-semibold uppercase tracking-[0.1em] text-[#FCFAF7] transition-colors hover:bg-[#B58A43]">
                        Vezi produsele
                    </a>
                </div>
            @else
                <div class="mt-8 grid grid-cols-1 gap-7 lg:grid-cols-[1fr_340px] lg:items-start">

                    {{-- Items --}}
                    <div class="space-y-5">
                        @foreach ($cartItems as $item)
                            @php $isCustom = $item->length || $item->height || $item->manufactoring_type_id; @endphp
                            <div class="rounded-[16px] border border-[#171411]/10 bg-white p-5" wire:key="cart-{{ $item->id }}">
                                <div class="flex flex-col gap-4 sm:flex-row">
                                    {{-- Image + info --}}
                                    <div class="flex items-start gap-4 sm:w-2/5">
                                        <img src="{{ $item->product->images[0] ?? asset('storage/images/placeholder-images.webp') }}"
                                             alt="{{ $item->product->name }}" class="h-20 w-20 flex-shrink-0 rounded-[10px] object-cover" />
                                        <div>
                                            <a href="{{ route('product.show', ['slug' => $item->product->slug]) }}"
                                               class="font-display text-base font-semibold leading-tight text-[#171411] hover:text-[#8c6529]">
                                                {{ $item->product->name }}
                                            </a>
                                            @if ($isCustom)
                                                <ul class="mt-2 space-y-0.5 text-[12px] text-[#5f594f]">
                                                    <li>Lățime: <span class="text-[#171411]">{{ $item->length }} m</span> · Înălțime: <span class="text-[#171411]">{{ $item->height }} m</span></li>
                                                    <li>Bucăți: <span class="text-[#171411]">{{ $item->pieces }}</span></li>
                                                    <li>Manoperă: <span class="text-[#171411]">{{ optional($item->manufactoringType)->name }}</span></li>
                                                </ul>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Update form + total + delete --}}
                                    <div class="flex flex-1 flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            @if ($isCustom)
                                                @include('cart.custom-update-form', ['item' => $item, 'manufactoringTypes' => $manufactoringTypes])
                                            @else
                                                @include('cart.standard-update-form', ['item' => $item])
                                            @endif
                                        </div>

                                        <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                                            <p class="font-display text-lg font-semibold text-[#171411]">{{ number_format($item->lineTotal(), 2) }} lei</p>
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1.5 text-[12px] font-medium text-[#5f594f] transition-colors hover:text-red-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                                                    Șterge
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Summary (sticky) --}}
                    <aside class="lg:sticky lg:top-28">
                        <div class="rounded-[16px] border border-[#171411]/10 bg-white p-6">
                            <h2 class="font-display text-lg font-semibold text-[#171411]">Sumar comandă</h2>
                            <div class="mt-5 space-y-3 border-b border-[#171411]/10 pb-5 text-sm">
                                <div class="flex justify-between text-[#5f594f]"><span>Subtotal</span><span class="text-[#171411]">{{ number_format($calculatedSubtotal, 2) }} lei</span></div>
                                <div class="flex justify-between text-[#5f594f]"><span>Transport</span><span class="text-[#171411]">{{ $calculatedShipping == 0 ? 'Gratuit' : number_format($calculatedShipping, 2) . ' lei' }}</span></div>
                            </div>
                            <div class="flex items-baseline justify-between pt-5">
                                <span class="text-sm font-semibold uppercase tracking-[0.08em] text-[#171411]">Total</span>
                                <span class="font-display text-2xl font-semibold text-[#171411]">{{ number_format($calculatedTotal, 2) }} lei</span>
                            </div>

                            <div class="mt-6">
                                @auth
                                    <a href="{{ route('checkout.index') }}"
                                       class="flex min-h-[50px] w-full items-center justify-center rounded-md bg-[#171411] text-[13px] font-semibold uppercase tracking-[0.1em] text-[#FCFAF7] transition-colors hover:bg-[#B58A43]">
                                        Continuă spre checkout
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       onclick="event.preventDefault(); document.getElementById('transfer-cart-form').submit();"
                                       class="flex min-h-[50px] w-full items-center justify-center rounded-md bg-[#171411] px-4 text-center text-[12px] font-semibold uppercase tracking-[0.08em] text-[#FCFAF7] transition-colors hover:bg-[#B58A43]">
                                        Loghează-te pentru a finaliza
                                    </a>
                                    <form id="transfer-cart-form" action="{{ route('set.cart.transfer') }}" method="POST" class="hidden">@csrf</form>
                                    <p class="mt-3 text-center text-[11px] text-[#5f594f]">Nu vei pierde produsele din coș.</p>
                                @endauth
                            </div>

                            <p class="mt-5 flex items-center justify-center gap-2 text-[11px] text-[#5f594f]">
                                <i class="fa-solid fa-truck text-[#B58A43]"></i> Transport gratuit peste 500 lei
                            </p>
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </div>
@endsection
