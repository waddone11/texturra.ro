<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyType extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'family_type_id',
        'name',
        'characteristics',
    ];

    /**
     * A family type belongs to a category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * A family type may have many products.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'family_type_id');
    }

    /**
     * Parse characteristics from JSON.
     */
    public function parsedCharacteristics()
    {
        return json_decode($this->characteristics, true);
    }
}
