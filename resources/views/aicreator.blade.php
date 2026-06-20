@extends('layouts.base')

@section('content')
    <section class="relative text-center text-white py-10 min-h-[300px]">
        <div class="background-image_location absolute inset-0 z-0"></div>
        <div class="relative z-10 p-8 max-w-2xl mx-auto">
            <h1 class="text-4xl font-bold mb-4 uppercase">{{ __('ai.AI_Tattoo_Creator') }}</h1>
            <p class="text-base leading-relaxed mb-2">
                {{ __('ai.describe_your_idea') }}
            </p>
        </div>
    </section>

    <section class="relative container bg-white mx-auto my-8 min-h-[800px] p-6 rounded-2xl">
        <div class="grid gap-8">
            @guest
                <div class="text-center">
                    <p class="text-gray-700 text-lg">{{ __('ai.login_for_ussage') }}</p>
                    <x-primary-link
                        type="submit"
                        href="{{ route('login') }}"
                        class="mt-4"
                    >
                        {{ __('Login') }}
                    </x-primary-link>
                </div>
            @else
                <livewire:ai-tattoo-creator />
            @endguest
        </div>
    </section>
@endsection
