<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout extends Component
{
    public function logout(): void
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        // Use Livewire's redirectRoute method
        $this->redirectRoute('login');
    }

    public function render()
    {
        return view('livewire.pages.auth.logout');
    }
}
