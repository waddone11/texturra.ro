@extends('layouts.base')

@section('content')
    <section class="relative text-center text-white md:py-10 md:min-h-[300px]">
        <div class="background-image_booking absolute inset-0 z-0"></div>
        <div class="relative z-10 md:p-8">
            <h1 class="text-xl md:text-4xl font-bold mb-4 uppercase">{{ __('book.heading') }}</h1>
            <p>
                <strong class="block text-xs md:text-sm mb-2">{{ __('book.address') }}</strong>
                <x-tooltip-cta
                    title="{{ __('home.book_now') }}"
                    :links="[
                            ['label' => __('home.call_us'), 'url' => 'tel:' . config('app.tel')],
                            ['label' => __('home.whatsapp_chat'), 'url' => 'https://wa.me/+491744552303'],
                            ['label' => __('home.messenger_chat'), 'url' => 'https://m.me/1693515170877100'],
                            ['label' => __('home.tiktok_chat'), 'url' => 'https://www.tiktok.com/@shark_tattoo_studio'],
                            ['label' => __('home.instagram_chat'), 'url' => 'https://www.instagram.com/shark.tattoo.marius'],
                        ]"
                />
            </p>
            <p class="mt-2 block text-xs md:text-sm">{{ __('book.booking_info') }}</p>
        </div>
    </section>

    <section>
        <div class="relative container text-center bg-white mx-auto my-8 min-h-[200px] md:min-h-[800px] p-6 md:rounded-2xl">
            <livewire:calendar-component view="timeGridWeek" />
            <br/><br/>
            <p class="text-sm md:text-lg">
            <strong>{!! __('book.info') !!}</strong><br>
            </p>
            <br/>

            <x-tooltip-cta
                title="{{ __('navigation.call_us_now') }}"
                :links="[
                    ['label' => __('home.call_us'), 'url' => 'tel:' . config('app.tel')],
                    ['label' => __('home.whatsapp_chat'), 'url' => 'https://wa.me/+491744552303'],
                    ['label' => __('home.messenger_chat'), 'url' => 'https://m.me/1693515170877100'],
                    ['label' => __('home.tiktok_chat'), 'url' => 'https://www.tiktok.com/@shark_tattoo_studio'],
                    ['label' => __('home.instagram_chat'), 'url' => 'https://www.instagram.com/shark.tattoo.marius'],
                ]"
            />
        </div>
    </section>
@endsection
