<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the dashboard page.
     */
    public function index()
    {
        return view('livewire.dashboard.index', [
        ])->extends('layouts.base')->section('content');
    }


}
