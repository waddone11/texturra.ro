<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwbLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'awb_number',
        'status',
        'courier_name',
        'response',
        'created_at',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
