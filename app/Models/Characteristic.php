<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Characteristic extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'characteristic_id',
        'name',
        'type_id',
        'display_order',
        'is_mandatory',
        'is_mandatory_for_mktp',
        'allow_new_value',
        'is_filter',
        'tags',
        'value_tags',
    ];

    /**
     * A characteristic belongs to a category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public static function getLabel($id)
    {
        return self::where('characteristic_id', $id)->value('name');
    }


}
