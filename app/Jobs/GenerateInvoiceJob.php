<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        try {
            // Load related data
            $this->order->load(['user', 'products.vat', 'billingAddress', 'voucherUsage.voucher']);

            // Determine invoice type
            $type = $this->order->payment_method === 'online' ? 'factura' : 'proforma';

            // Define file name and path
            $fileName = "{$type}_{$this->order->id}.pdf";
            $filePath = storage_path("app/public/invoices/{$fileName}");

            // Access voucher details if available
            $voucherUsage = $this->order->voucherUsage;
            $voucher = $voucherUsage ? $voucherUsage->voucher : null;

            // Generate the PDF
            $pdf = Pdf::loadView("pdfs.$type", [
                'order' => $this->order,
                'subtotalExcludingVat' => $this->order->subtotal_excluding_vat,
                'totalVat' => $this->order->total_vat,
                'discount' => $this->order->discount,
                'shipping' => $this->order->shipping_cost,
                'voucher' => $voucher, // Pass voucher details to the view
                'total' => $this->order->total_amount,
            ]);

            // Store the PDF
            Storage::put("public/invoices/{$fileName}", $pdf->output());

            // Save the invoice in the database
            Invoice::create([
                'order_id' => $this->order->id,
                'user_id' => $this->order->user_id,
                'total_amount' => $this->order->total_amount,
                'status' => 'issued',
                'file_path' => "invoices/{$fileName}",
            ]);

        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error in GenerateInvoiceJob for Order ID: ' . $this->order->id . ' - ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

}
