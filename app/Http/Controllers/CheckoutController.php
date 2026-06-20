<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Address;
use App\Models\InvoiceAddress;
use Illuminate\Support\Facades\Auth;
use App\Models\Voucher;
use App\Models\VoucherUsage;

class CheckoutController extends Controller
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

        $voucherUsage = VoucherUsage::where('user_id', Auth::id())
            ->where('status', 'applied')
            ->latest()
            ->first();

        $discount = $voucherUsage ? $voucherUsage->discount : 0;
        $total = $subtotal + $shipping - $discount;

        // Create a cart array to pass to the view
        $cart = [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => $total,
            'voucher' => $voucherUsage ? $voucherUsage->voucher : null,
        ];

        $addresses = Address::where('user_id', Auth::id())->get();
        $invoiceAddresses = InvoiceAddress::where('user_id', Auth::id())->get();

        return view('checkout.index', compact('cartItems', 'cart', 'addresses', 'invoiceAddresses'));
    }

    public function storeAddress(Request $request)
    {
        //dd('Request Data:', $request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $address = Address::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'is_default' => Address::where('user_id', auth()->id())->doesntExist(), // Set default if no other exists
        ]));

        return redirect()->route('checkout.index')->with('flashMessage', [
            'type' => 'success',
            'message' => 'Adresa de livrare a fost salvată.',
        ]);
    }

    public function storeInvoiceAddress(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $invoiceAddress = InvoiceAddress::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'is_default' => InvoiceAddress::where('user_id', auth()->id())->doesntExist(), // Set default if no other exists
        ]));

        return redirect()->route('checkout.index')->with('flashMessage', [
            'type' => 'success',
            'message' => 'Adresa de facturare a fost salvată.',
        ]);
    }

    public function setDefaultAddress($id)
    {
        $user = auth()->user();
        Address::where('user_id', $user->id)->update(['is_default' => false]); // Reset defaults
        $address = Address::findOrFail($id);
        $address->update(['is_default' => true]);

        return redirect()->route('checkout.index')->with('flashMessage', [
            'type' => 'success',
            'message' => 'Adresa de livrare implicită a fost actualizată.',
        ]);
    }

    public function setDefaultInvoiceAddress($id)
    {
        $user = auth()->user();
        InvoiceAddress::where('user_id', $user->id)->update(['is_default' => false]); // Reset defaults
        $invoiceAddress = InvoiceAddress::findOrFail($id);
        $invoiceAddress->update(['is_default' => true]);

        return redirect()->route('checkout.index')->with('flashMessage', [
            'type' => 'success',
            'message' => 'Adresa de facturare implicită a fost actualizată.',
        ]);
    }

    public function updateAddress(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
        ]);

        $address = Address::findOrFail($id);
        $address->update($validated);

        return redirect()->route('checkout.index')->with('flashMessage', [
            'type' => 'success',
            'message' => 'Adresa a fost actualizată cu succes.',
        ]);
    }

    public function destroyAddress($id)
    {
        $address = Address::findOrFail($id);
        $address->delete();

        return redirect()->route('checkout.index')->with('flashMessage', [
            'type' => 'success',
            'message' => 'Adresa a fost ștearsă cu succes.',
        ]);
    }


    public function updateInvoiceAddress(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
        ]);

        $address = InvoiceAddress::findOrFail($id);
        $address->update($validated);

        return redirect()->route('checkout.index')->with('flashMessage', [
            'type' => 'success',
            'message' => 'Adresa a fost actualizată cu succes.',
        ]);
    }

    public function destroyInvoiceAddress($id)
    {
        $address = InvoiceAddress::findOrFail($id);
        $address->delete();

        return redirect()->route('checkout.index')->with('flashMessage', [
            'type' => 'success',
            'message' => 'Adresa a fost ștearsă cu succes.',
        ]);
    }

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
        ]);

        $voucher = Voucher::where('code', $request->voucher_code)->first();

        if (!$voucher || !$voucher->isValid()) {
            return back()->withErrors(['voucher_code' => 'Codul de reducere este invalid sau a expirat.']);
        }

        // Calculate discount
        $cartItems = Cart::query()
            ->when(Auth::check(), function ($query) {
                $query->where('user_id', Auth::id());
            }, function ($query) {
                $query->where('session_id', session()->getId());
            })
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $discount = 0;
        if ($voucher->discount_amount) {
            $discount = $voucher->discount_amount;
        } elseif ($voucher->discount_percentage) {
            $discount = $subtotal * ($voucher->discount_percentage / 100);
        }

        // Avoid negative total discount
        $discount = min($discount, $subtotal);

        // Create voucher usage record with "applied" status
        $voucherUsage = VoucherUsage::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'voucher_id' => $voucher->id,
            ],
            [
                'discount' => $discount,
                'status' => 'applied',
            ]
        );

        return back()->with('success', 'Codul de reducere a fost aplicat cu succes!');
    }
}

