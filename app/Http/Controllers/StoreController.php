<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function store()
    {
        $products = Product::all(); // Retrieve all products
        return view('store', compact('products'));
    }
}
