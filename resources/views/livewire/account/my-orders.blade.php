<div class="max-w-7xl mx-auto py-12 px-3 md:px-0">
    <div class="flex flex-wrap md:flex-nowrap">
        <!-- Sidebar (1/5) -->
        <aside class="w-full md:w-1/5 p-3 md:p-0">
            <livewire:sidebar-account />
        </aside>

        <!-- Main Content (4/5) -->
        <main class="w-full md:w-4/5 pl-3">
            <div class="container">
                <h1 class="text-2xl font-semibold mb-6">{{ __('Comenzile mele') }}</h1>

                <!-- Search -->
                <div class="mb-4">
                    <input
                        type="text"
                        wire:model="search"
                        placeholder="{{ __('Search orders...') }}"
                        class="w-full px-4 py-2 border rounded"
                    />
                </div>

                <!-- Orders List -->
                <div class="w-full overflow-x-auto">
                    <table class="min-w-full table-auto mt-4">
                        <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 text-left">{{ __('account.order_number') }}</th>
                            <th class="p-2 text-left">{{ __('account.date') }}</th>
                            <th class="p-2 text-left">{{ __('account.status') }}</th>
                            <th class="p-2 text-right">{{ __('account.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($orders as $order)
                            <tr class="@if($loop->even) bg-gray-50 @endif border-t">
                                <td class="p-2">{{ $order->order_number }}</td>
                                <td class="p-2">{{ $order->created_at->format('Y-m-d') }}</td>
                                <td class="p-2">
                                    <span
                                        class="px-2 py-1 rounded-full text-white
                                        {{ $order->status === 'completed' ? 'bg-green-500' : ($order->status === 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="p-2 text-right">
                                    <a
                                        href="#"
                                        class="text-blue-500 hover:underline">
                                        {{ __('View Details') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">{{ __('No orders found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </main>
    </div>
</div>
