<?php

namespace App\Livewire\Pages\Auth;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use App\Models\Cart;

class Login extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();
        $oldSessionId = session()->getId();
        $this->form->authenticate();
        Session::regenerate();

        // Check for cart transfer flag
        if (session('eligible_to_transfer_cart')) {
            $this->mergeCartWithUserCart($oldSessionId);
            session()->forget('eligible_to_transfer_cart');

            $this->redirect(route('checkout.index'));
            return;
        }

        $this->redirect(session('url.intended', route('home')));

        //$this->redirectIntended(route('home'));
    }

    protected function mergeCartWithUserCart(string $oldSessionId): void
    {
        $sessionCartItems = Cart::where('session_id', $oldSessionId)->get();

        foreach ($sessionCartItems as $item) {
            Cart::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'product_id' => $item->product_id,
                ],
                [
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]
            );

            $item->delete();
        }
    }


    public function render()
    {
        return view('livewire.pages.auth.login')
            ->extends('layouts.base')
            ->section('content');
    }
}
