<?php

namespace App\Livewire\Account;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChangePassword extends Component
{
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    protected $rules = [
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ];

    public function updatePassword()
    {
        $this->validate();

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', __('The current password is incorrect.'));
            return;
        }

        // Update the password
        $user->update([
            'password' => Hash::make($this->new_password),
            'raw_password' => $this->new_password,
        ]);

        session()->flash('success', __('Your password has been updated successfully.'));
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }

    public function render()
    {
        return view('livewire.account.change-password', [

        ])->extends('layouts.base')->section('content');
    }
}
