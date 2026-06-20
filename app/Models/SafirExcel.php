<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafirExcel extends Model
{
    // Define the table if it doesn't follow Laravel's naming conventions
    protected $table = 'safir_excel';

    // Allow mass-assignment on all attributes (or list only the ones you want)
    protected $guarded = [];

    // Optionally, set casts for specific fields
    protected $casts = [
        'safir_link_exist'       => 'boolean',
        'safir_parsed'           => 'boolean',
        'safir_acquisition_price'=> 'decimal:2',
        'safir_sell_price'       => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
