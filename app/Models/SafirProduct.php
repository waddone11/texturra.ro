<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafirProduct extends Model
{
    protected $table = 'safir_products';

    // Whitelist attributes if needed
    protected $fillable = [
        'category_id',
        'product_link',
        'product_title',
    ];
}
