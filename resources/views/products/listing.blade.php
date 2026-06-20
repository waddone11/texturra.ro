@extends('layouts.base')

@section('content')
    <div class="max-w-7xl mx-auto py-4 md:py-3 px-3 md:p-0">
        <livewire:product-listing :categorySlug="$activeCategory->slug" />
    </div>

@endsection
