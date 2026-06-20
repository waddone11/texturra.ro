<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'quantity',
        'price',
        'length',
        'height',
        'manufactoring_type_id',
        'pieces',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function manufactoringType()
    {
        return $this->belongsTo(ManufactoringType::class, 'manufactoring_type_id');
    }

}
