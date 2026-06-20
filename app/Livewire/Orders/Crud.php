<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class Crud extends Component
{
    use WithPagination;

    public $orderId, $status, $notes, $isEditMode = false, $modalOpen = false, $search = '';

    protected $rules = [
        'status' => 'required|string|max:255',
        'notes' => 'nullable|string',
    ];

    public function render()
    {
        $orders = Order::with(['user', 'products.vat', 'shippingAddress', 'billingAddress'])
            ->where('order_number', 'like', '%' . $this->search . '%')
            ->orWhereHas('user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.orders.crud', [
            'orders' => $orders,
        ])->extends('layouts.base')->section('content');
    }

    public function editOrder($id)
    {
        $order = Order::findOrFail($id);
        $this->orderId = $order->id;
        $this->status = $order->status;
        $this->notes = $order->notes;
        $this->isEditMode = true;
        $this->modalOpen = true;
    }

    public function updateOrder()
    {
        $this->validate();

        $order = Order::findOrFail($this->orderId);
        $order->update([
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Order updated successfully');
        $this->resetFields();
        $this->modalOpen = false;
        $this->resetPage();
    }

    public function deleteOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        session()->flash('success', 'Order deleted successfully');
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->orderId = null;
        $this->status = '';
        $this->notes = '';
        $this->isEditMode = false;
    }
}
