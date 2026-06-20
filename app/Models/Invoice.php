<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'total_amount',
        'status',
        'file_path',
        'invoice_number',
        'issued_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
