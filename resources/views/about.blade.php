@extends('layouts.base')

@section('content')
    {{-- Despre noi — redesigned 2026. Copy rewritten textile-accurate (old template wrongly
         mentioned electronics/food). Real stats (mirror homepage proof band) + 2 real Ploiești
         showrooms. No invented history. --}}
    <div class="w-full bg-[#FCFAF7] font-dm text-[#171411]">
        <div class="mx-auto max-w-[1100px] px-5 py-14 sm:px-8 md:py-20">

            {{-- Header --}}
            <div class="mx-auto max-w-[720px] text-center">
                <p class="mb-3 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#B58A43]">Despre noi</p>
                <h1 class="font-display text-3xl font-semibold leading-tight text-[#171411] md:text-[44px]">
                    Textile premium, create cu grijă pentru casa ta
                </h1>
                <p class="mx-auto mt-5 max-w-[600px] text-[15px] leading-relaxed text-[#5f594f]">
                    La <strong class="font-semibold text-[#171411]">{{ config('app.store_owner.name', 'TEXTURRA HOME') }}</strong> creăm perdele, draperii și textile premium pentru casă — de la materiale atent selecționate până la confecția la comandă, pe dimensiunile exacte ale ferestrei tale.
                </p>
            </div>

            {{-- Stats (real — mirror homepage proof band) --}}
            <div class="mt-14 grid grid-cols-2 gap-y-10 border-y border-[#171411]/10 py-10 md:grid-cols-4 md:gap-y-0">
                @foreach ([
                    ['11+', 'Ani de experiență'],
                    ['500.000+', 'Produse vândute'],
                    ['10+', 'Consultanți specializați'],
                    ['2', 'Showroom-uri Ploiești'],
                ] as $stat)
                    <div class="flex flex-col items-center px-2 text-center {{ !$loop->first ? 'md:border-l md:border-[#171411]/10' : '' }}">
                        <span class="font-display text-3xl font-semibold leading-none text-[#171411] md:text-[40px]">{{ $stat[0] }}</span>
                        <span class="mt-2.5 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#171411]/55">{{ $stat[1] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Values --}}
            <div class="mt-14 grid grid-cols-1 gap-5 md:grid-cols-3">
                @foreach ([
                    ['fa-gem', 'Materiale premium', 'Catifea, in și țesături blackout atent selecționate pentru cădere naturală și durabilitate.'],
                    ['fa-scissors', 'Confecție la comandă', 'Croim fiecare comandă manual, pe dimensiunile tale exacte, cu finisaje îngrijite.'],
                    ['fa-comments', 'Consultanță personalizată', 'Te ghidăm în alegerea materialului și a stilului potrivit pentru fiecare cameră.'],
                ] as $v)
                    <div class="rounded-[14px] border border-[#171411]/10 bg-white p-7">
                        <span class="grid h-12 w-12 place-items-center rounded-full bg-[#B58A43]/10 text-[#8c6529]">
                            <i class="fa-solid {{ $v[0] }} text-lg"></i>
                        </span>
                        <h3 class="mt-5 font-display text-xl font-semibold text-[#171411]">{{ $v[1] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-[#5f594f]">{{ $v[2] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Showrooms (2 real, Ploiești) --}}
            <div class="mt-16">
                <div class="mb-8 text-center">
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#B58A43]">Showroom-uri</p>
                    <h2 class="font-display text-2xl font-semibold text-[#171411] md:text-3xl">Ne găsești în Ploiești</h2>
                </div>
                <div class="mx-auto grid max-w-3xl grid-cols-1 gap-6 sm:grid-cols-2">
                    @foreach ([
                        ['George Coșbuc nr. 13', 'Zona Halelor Centrale, Ploiești', 'https://www.google.com/maps/search/?api=1&query=George+Cosbuc+13+Ploiesti'],
                        ['Omnia Winmark, Etaj 2', 'Ploiești — magazin în centrul comercial', 'https://www.google.com/maps/search/?api=1&query=Omnia+Winmark+Ploiesti'],
                    ] as $shop)
                        <div class="flex flex-col rounded-[16px] border border-[#171411]/10 bg-white p-7 shadow-sm">
                            <span class="mb-5 grid h-12 w-12 place-items-center rounded-full bg-[#171411] text-[#FCFAF7]">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <h3 class="font-display text-xl font-semibold text-[#171411]">{{ $shop[0] }}</h3>
                            <p class="mt-2 text-sm text-[#5f594f]">{{ $shop[1] }}</p>
                            <a href="{{ $shop[2] }}" target="_blank" rel="noopener"
                               class="mt-6 inline-flex w-fit items-center gap-2 border-b border-[#171411]/30 pb-1 text-[12px] font-semibold uppercase tracking-[0.12em] text-[#171411] transition-colors hover:border-[#B58A43] hover:text-[#8c6529]">
                                Vezi pe Google Maps
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- CTA --}}
            <div class="mt-16 rounded-[18px] bg-[#171411] px-6 py-12 text-center text-[#FCFAF7] md:py-16">
                <h2 class="font-display text-2xl font-semibold md:text-[32px]">Ai o fereastră de îmbrăcat?</h2>
                <p class="mx-auto mt-3 max-w-md text-sm text-[#FCFAF7]/70">Programează o consultație gratuită — te ajutăm să alegi soluția potrivită pentru spațiul tău.</p>
                <a href="tel:{{ config('app.store_owner.phone') }}"
                   class="mt-7 inline-flex min-h-[48px] items-center justify-center gap-2 rounded-md bg-[#B58A43] px-8 text-[13px] font-semibold uppercase tracking-[0.1em] text-[#171411] transition-colors hover:bg-[#FCFAF7]">
                    Programează o consultație
                </a>
            </div>
        </div>
    </div>
@endsection
