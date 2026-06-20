<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'category_id',
        'percentage',
        'fixed_amount',
        'start_date',
        'end_date'
    ];

    // Relationship: each discount can belong to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship: each discount can belong to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

