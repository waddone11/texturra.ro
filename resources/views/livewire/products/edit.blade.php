@extends('layouts.base')

@section('content')
    <div class="max-w-9xl mx-auto py-12 px-3 md:px-6">
        <div class="flex flex-wrap md:flex-nowrap">
            <!-- Sidebar (1/5) -->
            <aside class="w-full md:w-1/12 p-3 md:p-0">
                <livewire:sidebar-stats />
            </aside>

            <!-- Main Content (4/5) -->
            <main class="w-full md:w-11/12 pl-3">
                <div class="pr-3">
                    <h1 class="text-2xl font-semibold mb-6">Produse</h1>

                    <!-- Navigation Bar with Search, Filter, and Add Button -->
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center space-x-4 hidden">
                            <input type="text" wire:model="search" placeholder="Search products..." class="px-4 py-2 border rounded" />
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="w-full overflow-x-auto border">
                       edit
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection
