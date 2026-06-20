<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ManufactoringType;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::query()
            ->when(Auth::check(), function ($query) {
                $query->where('user_id', Auth::id());
            }, function ($query) {
                $query->where('session_id', session()->getId());
            })
            ->with('product')
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $shipping = ($subtotal > config('app.free_shipping_min')) ? 0 : config('app.shipping_cost');
        $total = $subtotal + $shipping;
        $manufactoringTypes = ManufactoringType::all();

        return view('cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'manufactoringTypes' => $manufactoringTypes,
        ]);
    }

    public function addStandardProduct(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $owner = auth()->check()
            ? ['user_id' => auth()->id()]
            : ['session_id' => session()->getId()];

        // A standard line is identified by product_id + NULL dimensions, so we
        // never match (and clobber) a custom row of the same product.
        $existing = Cart::where($owner)
            ->where('product_id', $product->id)
            ->whereNull('length')
            ->whereNull('height')
            ->whereNull('manufactoring_type_id')
            ->first();

        if ($existing) {
            $existing->increment('quantity', $validated['quantity']);
            $existing->update(['price' => $product->price()]); // refresh price only
        } else {
            Cart::create(array_merge($owner, [
                'product_id'            => $product->id,
                'quantity'              => $validated['quantity'],
                'length'                => null,
                'height'                => null,
                'manufactoring_type_id' => null,
                'pieces'                => 1,
                'price'                 => $product->price(),
            ]));
        }

        return back()->with('flashMessage', [
            'type' => 'success',
            'message' => 'Produsul standard a fost adăugat în coș.',
        ]);
    }

    public function addCustomProduct(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'length' => 'required|numeric|min:1|max:30',
            'height' => 'required|numeric|min:0.5|max:10',
            'manufactoring_type_id' => 'required|exists:manufactoring_types,id',
            'pieces' => 'required|integer|min:1|max:2',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check if height is within product limit
        if ($product->height && $validated['height'] > $product->height) {
            return back()->with('flashMessage', [
                'type' => 'error',
                'message' => 'Înălțimea selectată depășește limita permisă pentru acest produs.',
            ]);
        }

        $manufactoringType = ManufactoringType::findOrFail($validated['manufactoring_type_id']);

        // Calculate final price
        $materialPrice = $product->price(); // per meter
        $manoperaPrice = $manufactoringType->price; // per meter
        $finalPrice = ($materialPrice + $manoperaPrice) * $validated['length'];

        // Prepare cart conditions
        $conditions = [
            'product_id' => $product->id,
            'length' => $validated['length'],
            'height' => $validated['height'],
            'manufactoring_type_id' => $manufactoringType->id,
            'pieces' => $validated['pieces'],
        ];

        if (auth()->check()) {
            $conditions['user_id'] = auth()->id();
        } else {
            $conditions['session_id'] = session()->getId();
        }

        // Add to cart
        Cart::create(array_merge($conditions, [
            'price' => $finalPrice,
        ]));

        return back()->with('flashMessage', [
            'type' => 'success',
            'message' => 'Produsul personalizat a fost adăugat în coș.',
        ]);
    }

    public function updateCart(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::findOrFail($id);

        if ($cart->product->general_stock < $validated['quantity']) {
            session()->flash('flashMessage', [
                'type' => 'error',
                'message' => 'Stoc insuficient.',
            ]);
            return redirect()->back();
        }

        $cart->update(['quantity' => $validated['quantity']]);

        session()->flash('flashMessage', [
            'type' => 'success',
            'message' => 'Cantitatea a fost actualizată.',
        ]);

        return redirect()->back();
    }

    public function updateCustomCart(Request $request, $id)
    {
        $validated = $request->validate([
            'length' => 'required|numeric|min:1|max:30',
            'height' => 'required|numeric|min:0.5|max:10',
            'manufactoring_type_id' => 'required|exists:manufactoring_types,id',
            'pieces' => 'required|integer|min:1|max:2',
        ]);

        $cart = Cart::findOrFail($id);
        $product = $cart->product;

        if ($product->height && $validated['height'] > $product->height) {
            return back()->with('flashMessage', [
                'type' => 'error',
                'message' => 'Înălțimea selectată depășește limita permisă pentru acest produs.',
            ]);
        }

        $manufactoringType = ManufactoringType::findOrFail($validated['manufactoring_type_id']);
        $materialPrice = $product->price();
        $manoperaPrice = $manufactoringType->price;

        $total = ($materialPrice + $manoperaPrice) * $validated['length'];

        $cart->update(array_merge($validated, [
            'price' => $total,
        ]));

        return back()->with('flashMessage', [
            'type' => 'success',
            'message' => 'Produsul personalizat a fost actualizat.',
        ]);
    }

    public function removeFromCart($id)
    {
        Cart::findOrFail($id)->delete();

        session()->flash('flashMessage', [
            'type' => 'success',
            'message' => 'Produsul a fost eliminat din coș.',
        ]);

        return redirect()->back();
    }

}
