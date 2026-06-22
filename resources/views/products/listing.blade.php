@extends('layouts.base')

@section('content')
    <div class="mx-auto w-full max-w-[1320px] px-4 md:px-8">
        <livewire:product-listing :categorySlug="$activeCategory->slug" />
    </div>

@endsection
