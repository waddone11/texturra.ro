<?php

namespace App\Jobs;

use App\Models\Order;
use App\Mail\OrderConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        // Load necessary relationships
        $this->order->load(['user', 'products.vat', 'billingAddress', 'voucherUsage.voucher']);

        if (!$this->order->user || !$this->order->user->email) {
            throw new \Exception("User or user email not found for order ID: {$this->order->id}");
        }

        // Send the email
        Mail::to($this->order->user->email)->send(
            new OrderConfirmationMail($this->order)
        );
    }
}
