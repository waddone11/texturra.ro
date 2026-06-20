@extends('layouts.base')

@section('content')

    <section class="relative text-center text-white md:py-10 md:min-h-[300px]">
        <div class="background-image_booking absolute inset-0 z-0"></div>
        <div class="relative z-10 md:p-8">
            <h1 class="text-xl md:text-4xl font-bold mb-4 uppercase">{{ __('team.heading') }}</h1>
            <p class="text-base leading-relaxed mb-4">
            </p>
            <p class="text-xs md:text-sm text-white mb-2 md:text-black font-extrabold">{{ __('book.booking_info') }}</p>
            <x-tooltip-cta
                title="{{ __('home.connect_with_us') }}"
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

    <section class="relative container bg-white mx-auto my-8 min-h-[800px] p-6 md:rounded-2xl">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            @foreach($employees as $employee)
                    <div class="bg-white text-left">
                        <!-- Employee Image -->
                        <div class="w-full h-64 mx-auto bg-cover bg-center rounded-lg mb-4"
                             style="background-image: url('{{ asset($employee->profile_image ? 'storage/' . $employee->profile_image : 'storage/images/no_user.webp') }}');">
                        </div>


                        <!-- Employee Name and Role -->
                        <h4 class="text-xl font-semibold">{{ $employee->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $employee->specialty }}</p>

                        <!-- Social Icons -->
                        <div class="mt-4 flex justify-left">
                            <!-- Left-aligned button -->
                            <x-simple-link href="{{ route('team.show', $employee->slug) }}" class="bg-white text-black border border-black">
                                {{ __('location.my_work') }}
                            </x-simple-link>

                            <!-- Right-aligned button -->
                            <x-primary-link href="{{ route('book-appointment') }}" class="ml-4 border border-red-500 text-red-500">
                                {{ __('home.book_now') }}
                            </x-primary-link>
                        </div>
                    </div>
                @endforeach
        </div>

        <div class="mt-8 text-center">
            <p class="font-bold mb-4">{{ __('home.dont_forget') }}</p>
            <x-tooltip-cta
                title="{{ __('home.connect_with_us') }}"
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
