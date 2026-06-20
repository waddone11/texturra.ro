<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\VoucherUsage;
use Illuminate\Support\Facades\Auth;
use App\Jobs\GenerateInvoiceJob;
use App\Jobs\SendOrderConfirmationEmailJob;

class OrderController_OLD extends Controller
{
    public function store(Request $request)
    {
        // Step 1: Validate Request Data
        $validated = $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:invoice_addresses,id',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Step 2: Retrieve Cart Items
        $cartItems = Cart::query()
            ->when(Auth::check(), function ($query) {
                $query->where('user_id', Auth::id());
            }, function ($query) {
                $query->where('session_id', session()->getId());
            })
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('flashMessage', [
                'type' => 'error',
                'message' => 'Coșul tău este gol!',
            ]);
        }

        // Step 3: Calculate Subtotal and Shipping Costs
        $subtotalExcludingVat = $cartItems->sum(fn($item) => $item->product->priceWithoutVat() * $item->quantity);
        $totalVat = $cartItems->sum(fn($item) => $item->product->vatAmount() * $item->quantity);
        $total_initial = $cartItems->sum(fn($item) => $item->product->price() * $item->quantity);

        // Step 4: Handle Voucher Usage
        $voucherUsage = VoucherUsage::where('user_id', Auth::id())
            ->where('status', 'applied')
            ->latest()
            ->first();

        $discount = $voucherUsage ? $voucherUsage->discount : 0;

        // Step 5: Calculate Shipping
        $shipping = $total_initial >= config('app.free_shipping_min') ? 0 : config('app.shipping_cost');

        // Step 6: Validate Stock Availability
        foreach ($cartItems as $item) {
            $isCustom = $item->length || $item->height || $item->manufactoring_type_id;

            $requiredStock = $isCustom
                ? ($item->length * $item->pieces)  // total metri liniari de material
                : $item->quantity; // număr de bucăți pentru produs standard

            if ($requiredStock > $item->product->general_stock) {
                return redirect()->route('cart.index')->with('flashMessage', [
                    'type' => 'error',
                    'message' => "Produsul {$item->product->name} nu mai este disponibil în cantitatea solicitată!",
                ]);
            }

            /*
            // (opțional) În viitor, poți verifica și stocurile pe locații:
            $productStocks = $item->product->stocks; // Relationship definit în modelul Product
            $totalAvailableStock = $productStocks->sum('quantity');

            if ($requiredStock > $totalAvailableStock) {
                return redirect()->route('cart.index')->with('flashMessage', [
                    'type' => 'error',
                    'message' => "Produsul {$item->product->name} nu are suficient stoc disponibil la locații!",
                ]);
            }
            */
        }


        // Step 7: Calculate Total
        $total = $total_initial + $shipping - $discount;

        // Step 8: Create Order
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => uniqid('ORD-'),
            'total_amount' => $total, // Total amount including VAT, discounts, and shipping
            'subtotal_excluding_vat' => $subtotalExcludingVat, // Subtotal excluding VAT
            'total_vat' => $totalVat, // Total VAT
            'discount' => $discount, // Applied discount
            'shipping_cost' => $shipping, // Shipping cost
            'status' => 'placed', // Initial status
            'notes' => $validated['notes'],
            'shipping_address_id' => $validated['shipping_address_id'],
            'billing_address_id' => $validated['billing_address_id'],
            'payment_method' => $validated['payment_method'],
        ]);

        // Step 9: Attach Cart Items to Order
        foreach ($cartItems as $item) {
            $order->products()->attach($item->product_id, [
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);

            // Reduce general stock in Product
            $item->product->decrement('general_stock', $item->quantity);

            // Reduce stock in ProductStock (distributed across locations)
            $remainingQuantity = $item->quantity;
            $productStocks = $item->product->stocks;

            foreach ($productStocks as $stock) {
                if ($remainingQuantity <= 0) break;

                if ($stock->quantity >= $remainingQuantity) {
                    $stock->decrement('quantity', $remainingQuantity);
                    $remainingQuantity = 0;
                } else {
                    $remainingQuantity -= $stock->quantity;
                    $stock->update(['quantity' => 0]);
                }
            }
        }

        // Step 10: Mark Voucher as Used
        if ($voucherUsage) {
            $voucherUsage->update([
                'status' => 'used',
                'order_id' => $order->id, // Attach the order ID to the voucher usage
            ]);

            // Increment the times_used for the voucher
            $voucher = $voucherUsage->voucher;
            if ($voucher) {
                $voucher->incrementUsage();
            }
        }

        // Step 11: Clear Cart
        Cart::where('user_id', Auth::id())
            ->orWhere('session_id', session()->getId())
            ->delete();

        // Step 12: Dispatch Jobs for Invoice and Email
        //Log::info('Dispatching GenerateInvoiceJob', ['order_id' => $order->id, 'relationships' => $order->load(['user', 'products.vat', 'billingAddress', 'voucherUsage.voucher'])]);

        GenerateInvoiceJob::dispatch($order);
        SendOrderConfirmationEmailJob::dispatch($order);

        // Step 13: Redirect with Success Message
        return redirect()->route('home')->with('flashMessage', [
            'type' => 'success',
            'message' => 'Comanda ta a fost plasată cu succes!',
        ]);
    }
}
