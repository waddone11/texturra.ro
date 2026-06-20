<?php

namespace App\Livewire\Account;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class MyOrders extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function render()
    {
        $orders = Order::where('user_id', auth()->id())
            ->where(function ($query) {
                $query->where('status', 'like', '%' . $this->search . '%')
                    ->orWhere('order_number', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.account.my-orders', compact('orders'))
            ->extends('layouts.base')
            ->section('content');
    }
}
