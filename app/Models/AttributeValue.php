<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariation;
use App\Models\Color;


class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = ['attribute_id', 'value', 'extra_info'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function productVariations()
    {
        return $this->belongsToMany(ProductVariation::class, 'product_variation_attribute_values');
    }

    // In AttributeValue model
    public function color()
    {
        return $this->belongsTo(Color::class, 'value', 'name'); // assuming 'value' in AttributeValue matches 'name' in Color
    }

}

