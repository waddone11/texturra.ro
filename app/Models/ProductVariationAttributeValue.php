<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationAttributeValue extends Model
{
    use HasFactory;

    protected $table = 'product_variation_attribute_values';
    protected $fillable = ['product_variation_id', 'attribute_value_id'];

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
