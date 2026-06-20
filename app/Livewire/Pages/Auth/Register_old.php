<?php

namespace App\Livewire\Pages\Auth;

use App\Livewire\Forms\RegisterForm;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Register_old extends Component
{
    public RegisterForm $form;

    /**
     * Handle the registration process.
     */
    public function register(): void
    {
        $this->form->registerUser();  // Register the user

        Session::regenerate();  // Regenerate session for security

        $this->redirectIntended(route('home'));
    }

    public function render()
    {
        return view('livewire.pages.auth.register')
            ->extends('layouts.base')
            ->section('content');
    }
}
