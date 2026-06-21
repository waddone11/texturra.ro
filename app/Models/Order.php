<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'subtotal_excluding_vat',
        'total_vat',
        'discount',
        'shipping_cost',
        'status',
        'notes',
        'shipping_address_id',
        'billing_address_id',
        'payment_method',
    ];

    /**
     * Relationship: Many-to-Many with Product model
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Read-only access to order lines incl. the pivot `meta` (custom dimensions).
     * products() belongsToMany only loads quantity+price; this exposes meta.
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Relationship: Belongs to Shipping Address
     */
    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    /**
     * Relationship: Belongs to Billing Address
     */
    public function billingAddress()
    {
        return $this->belongsTo(InvoiceAddress::class, 'billing_address_id');
    }

    /**
     * Relationship: Has Many Invoices
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Relationship: Has Many AWB Logs
     */
    public function awbLogs()
    {
        return $this->hasMany(AwbLog::class);
    }

    /**
     * Scope for Filtering Orders by Status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Method to check if order is refundable
     */
    public function isRefundable()
    {
        return $this->status === OrderStatus::Completed;
    }

    /**
     * Method to calculate total items in the order
     */
    public function totalItems()
    {
        return $this->products->sum('pivot.quantity');
    }

    /**
     * Method to update order status
     */
    public function updateStatus($newStatus)
    {
        $this->update(['status' => $newStatus]);
    }

    public function voucher()
    {
        return $this->hasOne(VoucherUsage::class, 'order_id');
    }

    public function voucherUsage()
    {
        return $this->hasOne(VoucherUsage::class);
    }


    public function calculateShippingCost()
    {
        $freeShippingThreshold = config('app.free_shipping_min');
        return $this->products->sum(fn($product) => $product->pivot->price * $product->pivot->quantity) >= $freeShippingThreshold ? 0 : config('app.shipping_cost');
    }

    public function subtotalExcludingVat()
    {
        return $this->products->sum(fn($product) => $product->priceWithoutVat() * $product->pivot->quantity);
    }

    public function totalVat()
    {
        return $this->products->sum(fn($product) => $product->vatAmount() * $product->pivot->quantity);
    }

    public function subtotalIncludingVat()
    {
        return $this->subtotalExcludingVat() + $this->totalVat();
    }
}
