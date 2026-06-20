<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwbLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'courier_type',
        'status',
        'response',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
