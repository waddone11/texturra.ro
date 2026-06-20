@extends('layouts.base')

@section('content')
    <div class="container bg-white mx-auto mt-4 md:mt-20 md:py-8 md:pr-8 rounded shadow">
        <div class="flex flex-wrap md:flex-nowrap">
            <!-- Sidebar (1/5) -->
            <aside class="w-full md:w-1/5 p-3 md:p-4">
                <livewire:sidebar-stats />
            </aside>

            <!-- Main Content (4/5) -->
            <main class="w-full md:w-4/5 p-3 md:p-6">
                <div class="container">
                    <h1 class="text-2xl font-semibold mb-6">Dashboards</h1>
                    we will provide usfull information here , about orders, bookings, etc , statistics
                </div>
            </main>
        </div>
    </div>
@endsection
