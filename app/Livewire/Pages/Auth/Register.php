<?php

namespace App\Livewire\Pages\Auth;

use App\Livewire\Forms\RegisterForm;
use App\Models\Cart;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Register extends Component
{
    public RegisterForm $form;

    /**
     * Handle the registration process.
     */
    public function register(): void
    {
        // Capture the current session ID before regenerating
        $oldSessionId = session()->getId();

        // Register the user and store the instance
        $user = $this->form->registerUser();

        // Log the user in
        auth()->login($user);

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // Regenerate session for security
        Session::regenerate();

        // Check for cart transfer flag
        if (session('eligible_to_transfer_cart')) {
            $this->mergeCartWithUserCart($oldSessionId);
            session()->forget('eligible_to_transfer_cart');
            $this->redirect(route('checkout.index'));
            return;
        }

        $this->redirect(session('url.intended', route('home')));
    }

    /**
     * Merge cart items from the old session into the authenticated user's cart.
     */
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
        return view('livewire.pages.auth.register')
            ->extends('layouts.base')
            ->section('content');
    }
}
