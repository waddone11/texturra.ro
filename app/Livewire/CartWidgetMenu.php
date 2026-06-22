<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;

class CartWidgetMenu extends Component
{
    public $cartCount = 0;

    protected $listeners = ['cartUpdated' => 'updateCart'];

    public function mount()
    {
        $this->updateCart();
    }

    public function updateCart()
    {
        // Check if user is authenticated
        if (auth()->check()) {
            // Count cart items for authenticated user
            $cart = Cart::where('user_id', auth()->id())->get();
        } else {
            // Count cart items for guest session
            $cart = Cart::where('session_id', session()->getId())->get();
        }

        // Badge count: standard lines use `quantity`, custom lines (perdele la comandă)
        // store NULL quantity but carry `pieces` — count those so a custom-only cart
        // is not shown as empty. Counting only; price/cart-line logic is untouched.
        $this->cartCount = $cart->sum(fn ($item) => $item->quantity ?? $item->pieces ?? 1);
    }

    public function render()
    {
        return view('livewire.cart-widget-menu', [
            'cartCount' => $this->cartCount,
        ]);
    }
}
