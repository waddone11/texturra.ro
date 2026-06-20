<?php

namespace App\Livewire;

use Livewire\Component;

class AdminDashboardController extends Component
{
    public function render()
    {
        return view('livewire.admin-dashboard-controller', [

        ])->extends('layouts.base')->section('content');
    }
}
