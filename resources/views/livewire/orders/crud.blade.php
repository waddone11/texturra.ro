<div class="max-w-7xl mx-auto py-12 px-3 md:px-0">
    <div class="flex flex-wrap md:flex-nowrap">
        <!-- Sidebar (1/5) -->
        <aside class="w-full md:w-1/5 p-3 md:p-0">
            <livewire:sidebar-stats />
        </aside>

        <!-- Main Content (4/5) -->
        <main class="w-full md:w-4/5 pl-3">
            <div class="container">
                <h1 class="text-2xl font-semibold mb-6">Orders</h1>

                <!-- Navigation Bar with Search -->
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center space-x-4">
                        <input type="text" wire:model="search" placeholder="Search orders..." class="px-4 py-2 border rounded" />
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="w-full overflow-x-auto">
                    <table class="min-w-full table-auto mt-4">
                        <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 text-left text-xs md:text-md">Order Number</th>
                            <th class="p-2 text-left text-xs md:text-md">User</th>
                            <th class="p-2 text-left text-xs md:text-md">Status</th>
                            <th class="p-2 text-left text-xs md:text-md">Total Amount</th>
                            <th class="p-2 text-right text-xs md:text-md">Details</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <tr class="@if($loop->even) bg-gray-50 @endif border-t">
                                <!-- Order Number -->
                                <td class="px-4 py-2 text-xs md:text-md">{{ $order->order_number }}</td>
                                <!-- User -->
                                <td class="px-4 py-2 text-xs md:text-md">{{ $order->user->name }}</td>
                                <!-- Status -->
                                <td class="px-4 py-2 text-xs md:text-md">{{ $order->status }}</td>
                                <!-- Total Amount -->
                                <td class="px-4 py-2 text-xs md:text-md">{{ number_format($order->total_amount, 2) }} RON</td>
                                <!-- Accordion Toggle -->
                                <td class="px-4 py-2 text-right">
                                    <x-secondary-button data-id="{{ $order->id }}" class="accordion-toggle text-xs">See all details</x-secondary-button>
                                </td>
                            </tr>
                            <!-- Hidden Accordion Row -->
                            <tr id="accordion-{{ $order->id }}" class="hidden">
                                <td colspan="5" class="p-2 bg-gray-50">
                                    <div class="flex flex-col space-y-2">
                                        <div class="border-b border-black text-xs md:text-sm pt-3"><strong>Order Date:</strong><br/> {{ $order->created_at->format('d-m-Y') }}</div>
                                        <div class="border-b border-black text-xs md:text-sm pt-3"><strong>Shipping Address:</strong><br/> {{ $order->shippingAddress->street }}, {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}, {{ $order->shippingAddress->postal_code }}</div>
                                        <div class="border-b border-black text-xs md:text-sm pt-3"><strong>Billing Address:</strong><br/> {{ $order->billingAddress->street }}, {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }}, {{ $order->billingAddress->postal_code }}</div>
                                        <div class="border-b border-black text-xs md:text-sm pt-3"><strong>Subtotal (Excluding VAT):</strong><br/> {{ number_format($order->subtotalExcludingVat(), 2) }} RON</div>
                                        <div class="border-b border-black text-xs md:text-sm pt-3"><strong>VAT:</strong><br/> {{ number_format($order->totalVat(), 2) }} RON</div>
                                        <div class="border-b border-black text-xs md:text-sm pt-3"><strong>Shipping Cost:</strong><br/> {{ number_format($order->shipping_cost, 2) }} RON</div>
                                        <div class="border-b border-black text-xs md:text-sm pt-3"><strong>Total:</strong><br/> {{ number_format($order->total_amount, 2) }} RON</div>
                                        <div class="border-b border-black text-xs md:text-sm pt-3"><strong>Products:</strong>
                                            <ul>
                                                @foreach ($order->products as $product)
                                                    <li>{{ $product->pivot->quantity }}x {{ $product->name }} - {{ number_format($product->pivot->price, 2) }} RON</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="flex justify-start space-x-2 text-xs md:text-sm py-4">
                                            <x-secondary-button wire:click="editOrder({{ $order->id }})" class="ml-0">Edit</x-secondary-button>
                                            <x-secondary-button wire:click="deleteOrder({{ $order->id }})" class="text-red-600">Delete</x-secondary-button>
                                            @if ($order->invoices->isNotEmpty())
                                                <a href="{{ asset('storage/' . $order->invoices->first()->file_path) }}" target="_blank" class="text-blue-600 hover:underline text-xs md:text-sm flex items-center">
                                                     Download Invoice
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <script>
                        document.querySelectorAll('.accordion-toggle').forEach(button => {
                            button.addEventListener('click', function () {
                                const id = this.dataset.id;
                                const content = document.getElementById(`accordion-${id}`);
                                content.classList.toggle('hidden');
                            });
                        });
                    </script>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </main>
    </div>
</div>
