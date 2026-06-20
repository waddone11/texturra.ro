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
            Cart::mergeSessionIntoUser($oldSessionId, auth()->id());
            session()->forget('eligible_to_transfer_cart');
            $this->redirect(route('checkout.index'));
            return;
        }

        $this->redirect(session('url.intended', route('home')));
    }

    public function render()
    {
        return view('livewire.pages.auth.register')
            ->extends('layouts.base')
            ->section('content');
    }
}
