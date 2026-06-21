<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clean material entity (mirror of Color) — replaces the legacy "Material"
 * Attribute/AttributeValue + ProductVariation tagging.
 */
class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_material')->withTimestamps();
    }
}
