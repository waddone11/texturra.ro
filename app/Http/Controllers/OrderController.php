<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\VoucherUsage;
use Illuminate\Support\Facades\Auth;
use App\Jobs\GenerateInvoiceJob;
use App\Jobs\SendOrderConfirmationEmailJob;

class OrderController extends Controller
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
            ->when(Auth::check(), fn($query) => $query->where('user_id', Auth::id()))
            ->when(!Auth::check(), fn($query) => $query->where('session_id', session()->getId()))
            ->with(['product', 'manufactoringType'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('flashMessage', [
                'type' => 'error',
                'message' => 'Coșul tău este gol!',
            ]);
        }

        // Step 3: Calculate Subtotal and Shipping Costs
        $subtotalExcludingVat = 0;
        $totalVat = 0;
        $total_initial = 0;

        // Debug array (optional): captures full breakdown per product
        $debug = [];

        foreach ($cartItems as $item) {
            $isCustom = $item->length || $item->height || $item->manufactoring_type_id;

            // Use product price directly; no need to rely on cart-stored price if it's outdated
            $productPrice = (float) $item->product->price;
            $manufPrice = (float) optional($item->manufactoringType)->price;

            if ($isCustom) {
                // For custom products: price is (length × price + length × manopera) × pieces
                $materialCost = $item->length * $productPrice;
                $manufCost = $item->length * $manufPrice;
                $itemTotal = ($materialCost + $manufCost) * $item->pieces;
            } else {
                // For standard products: price × quantity
                $itemTotal = $item->price * $item->quantity;
            }

            $units = ($isCustom ? $item->pieces : $item->quantity) ?: 1;

            // VAT logic (ensure fallback if VAT is null)
            $vatRate = optional($item->product->vat)->rate ?? 19;
            $unitPriceWithVat = $itemTotal / $units;
            //$unitPriceWithoutVat = $unitPriceWithVat / (1 + $vatRate / 100);
            $unitPriceWithoutVat = round($unitPriceWithVat / (1 + $vatRate / 100), 2);

            $unitVat = $unitPriceWithVat - $unitPriceWithoutVat;

            $subtotalExcludingVat += $unitPriceWithoutVat * $units;
            $totalVat += $unitVat * $units;
            $total_initial += $itemTotal;

            // Optional debug entry
            $debug[] = [
                'product_id' => $item->product->id,
                'product_name' => $item->product->name,
                'is_custom' => $isCustom,
                'quantity' => $item->quantity,
                'pieces' => $item->pieces,
                'length' => $item->length,
                'price_per_meter' => $productPrice,
                'manuf_price_per_meter' => $manufPrice,
                'vat_rate' => $vatRate,
                'item_total' => $itemTotal,
                'unit_price_with_vat' => $unitPriceWithVat,
                'unit_price_without_vat' => $unitPriceWithoutVat,
                'unit_vat' => $unitVat,
                'total_vat_for_product' => $unitVat * $units,
                'subtotal_ex_vat_for_product' => $unitPriceWithoutVat * $units,
            ];
        }
        // Final totals (added to debug log)
        $debug[] = [
            '--- TOTALS ---' => true,
            'subtotalExcludingVat' => round($subtotalExcludingVat, 2),
            'totalVat' => round($totalVat, 2),
            'total_initial' => round($total_initial, 2),
        ];
        // Uncomment to debug in development:
        // dd($debug);



        // Step 4: Handle Voucher Usage
        $voucherUsage = VoucherUsage::where('user_id', Auth::id())
            ->where('status', 'applied')
            ->latest()
            ->first();

        $discount = $voucherUsage?->discount ?? 0;

        // Step 5: Calculate Shipping
        $shipping = $total_initial >= config('app.free_shipping_min') ? 0 : config('app.shipping_cost');

        // Step 6: Validate Stock Availability
        foreach ($cartItems as $item) {
            $isCustom = $item->length || $item->height || $item->manufactoring_type_id;
            $requiredStock = $isCustom ? ($item->length * $item->pieces) : $item->quantity;

            if ($requiredStock > $item->product->general_stock) {
                return redirect()->route('cart.index')->with('flashMessage', [
                    'type' => 'error',
                    'message' => "Produsul {$item->product->name} nu mai este disponibil în cantitatea solicitată!",
                ]);
            }

            // (opțional) În viitor, poți verifica și stocurile pe locații:
            // $productStocks = $item->product->stocks;
            // $totalAvailableStock = $productStocks->sum('quantity');
            // if ($requiredStock > $totalAvailableStock) {
            //     return redirect()->route('cart.index')->with('flashMessage', [
            //         'type' => 'error',
            //         'message' => "Produsul {$item->product->name} nu are suficient stoc disponibil la locații!",
            //     ]);
            // }
        }

        // Step 7: Calculate Total
        $total = $total_initial + $shipping - $discount;

        // Step 8: Create Order
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => uniqid('ORD-'),
            'total_amount' => $total,
            'subtotal_excluding_vat' => $subtotalExcludingVat,
            'total_vat' => $totalVat,
            'discount' => $discount,
            'shipping_cost' => $shipping,
            'status' => 'placed',
            'notes' => $validated['notes'],
            'shipping_address_id' => $validated['shipping_address_id'],
            'billing_address_id' => $validated['billing_address_id'],
            'payment_method' => $validated['payment_method'],
        ]);

        // Step 9: Attach Cart Items to Order
        foreach ($cartItems as $item) {
            $isCustom = $item->length || $item->height || $item->manufactoring_type_id;

            $order->products()->attach($item->product_id, [
                'quantity' => $isCustom ? $item->pieces : $item->quantity,
                'price' => $item->product->price,
                'meta' => json_encode([
                    'is_custom' => $isCustom,
                    'length' => $item->length,
                    'height' => $item->height,
                    'manufactoring_type_id' => $item->manufactoring_type_id,
                    'manufactoring_type_name' => optional($item->manufactoringType)->name,
                    'manufactoring_price' => optional($item->manufactoringType)->price,
                ]),
            ]);


            // Step 10: Reduce stock (only general stock for now)
            $requiredStock = $isCustom ? ($item->length * $item->pieces) : $item->quantity;
            $item->product->decrement('general_stock', $requiredStock);

            // (opțional) În viitor, vom scădea și stocurile din locații
            // $remainingQuantity = $requiredStock;
            // foreach ($item->product->stocks as $stock) {
            //     if ($remainingQuantity <= 0) break;
            //     if ($stock->quantity >= $remainingQuantity) {
            //         $stock->decrement('quantity', $remainingQuantity);
            //         break;
            //     } else {
            //         $remainingQuantity -= $stock->quantity;
            //         $stock->update(['quantity' => 0]);
            //     }
            // }
        }

        // Step 11: Mark Voucher as Used
        if ($voucherUsage) {
            $voucherUsage->update([
                'status' => 'used',
                'order_id' => $order->id,
            ]);
            $voucherUsage->voucher?->incrementUsage();
        }

        // Step 12: Clear Cart
        Cart::where('user_id', Auth::id())
            ->orWhere('session_id', session()->getId())
            ->delete();

        // Step 13: Dispatch Jobs for Invoice and Email
        GenerateInvoiceJob::dispatch($order);
        SendOrderConfirmationEmailJob::dispatch($order);

        return redirect()->route('home')->with('flashMessage', [
            'type' => 'success',
            'message' => 'Comanda ta a fost plasată cu succes!',
        ]);
    }
}
