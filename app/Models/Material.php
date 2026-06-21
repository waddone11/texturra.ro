<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clean material entity (mirror of Color) — replaced the now-retired legacy
 * "Material" attribute/variation tagging.
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
