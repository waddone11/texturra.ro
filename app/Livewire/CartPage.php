<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;

class CartPage extends Component
{
    public $cartItems = [];
    public $subtotal = 0;
    public $shipping = 5.00; // Example shipping cost
    public $taxes = 0;
    public $total = 0;

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cartItems = Cart::with('product')
            ->where('session_id', session()->getId())
            ->get();

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $this->taxes = $this->subtotal * 0.08; // Example 8% tax
        $this->total = $this->subtotal + $this->shipping + $this->taxes;
    }

    public function increaseQuantity($cartId)
    {
        $cart = Cart::find($cartId);
        if ($cart->product->general_stock > $cart->quantity) {
            $cart->update(['quantity' => $cart->quantity + 1]);
            $this->emit('cartUpdated');
        } else {
            $this->dispatch('flashMessage', [
                'type' => 'error',
                'message' => 'Not enough stock available.',
            ]);
        }
    }

    public function decreaseQuantity($cartId)
    {
        $cart = Cart::find($cartId);
        if ($cart->quantity > 1) {
            $cart->update(['quantity' => $cart->quantity - 1]);
            $this->emit('cartUpdated');
        }
    }

    public function removeFromCart($cartId)
    {
        Cart::find($cartId)->delete();
        $this->emit('cartUpdated');
    }

    public function confirmOrder()
    {
        // Example order confirmation logic
        Session::forget('cart');
        $this->emit('cartUpdated');
        $this->dispatchBrowserEvent('flashMessage', [
            'type' => 'success',
            'message' => 'Order confirmed successfully!',
        ]);
    }

    public function render()
    {
        return view('livewire.cart-page');
    }
}
