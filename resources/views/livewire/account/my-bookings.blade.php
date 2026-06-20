<div class="container bg-white mx-auto mt-4 md:mt-20 md:py-8 md:pr-8 rounded shadow">
    <div class="flex flex-wrap md:flex-nowrap">
        <!-- Sidebar -->
        <aside class="w-full md:w-1/5 p-3 md:p-4">
            <livewire:sidebar-account />
        </aside>

        <!-- Main Content -->
        <main class="w-full md:w-4/5 p-3 md:p-6">
            <h1 class="text-2xl font-semibold mb-6">{{ __('account.My Bookings') }}</h1>

            <!-- Search -->
            <div class="mb-4">
                <input
                    type="text"
                    wire:model="search"
                    placeholder="{{ __('account.Search bookings...') }}"
                    class="w-full px-4 py-2 border rounded"
                />
            </div>

            <!-- Booking List -->
            <div class="w-full overflow-x-auto">
                <table class="min-w-full table-auto mt-4">
                    <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2 text-left">{{ __('account.service') }}</th>
                        <th class="p-2 text-left">{{ __('account.location') }}</th>
                        <th class="p-2 text-left">{{ __('account.date&time') }}</th>
                        <th class="p-2 text-left">{{ __('account.status') }}</th>
                        <th class="p-2 text-right">{{ __('account.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($bookings as $booking)
                        <tr class="@if($loop->even) bg-gray-50 @endif border-t">
                            <td class="p-2">
                                {{ $booking->service->translate(app()->getLocale())->name ?? $service->name }} - {{ $booking->employee->name }}
                            </td>
                            <td class="p-2">{{ $booking->location->name ?? 'N/A' }}</td>
                            <td class="p-2">{{ $booking->booking_date }} {{ $booking->start_time }}</td>
                            <td class="p-2">
                                    <span
                                        class="px-2 py-1 rounded-full text-white
                                        {{ $booking->status === 'confirmed' ? 'bg-green-500' : ($booking->status === 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                            </td>
                            <td class="p-2 text-right">
                                <a href="#" class="text-blue-500 hover:underline">
                                    {{ __('View Details') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">{{ __('No bookings found.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        </main>
    </div>
</div>
