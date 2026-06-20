<?php

namespace App\Livewire\Account;

use Livewire\Component;

class AccountController extends Component
{
    public function render()
    {
        return view('livewire.account.account-controller', [

        ])->extends('layouts.base')->section('content');
    }
}
