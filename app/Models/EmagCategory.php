<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmagCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'emag_id',
        'emag_parent_id',
        'is_ean_mandatory',
        'is_warranty_mandatory',
        'is_allowed',
        'characteristics',
        'family_types',
    ];

    /**
     * Boot method for the model to automatically generate slugs if not provided.
     */
//    public static function boot()
//    {
//        parent::boot();
//
//        // Automatically create a slug before saving
//        static::saving(function ($model) {
//            if (empty($model->slug)) {
//                $model->slug = Str::slug($model->name);
//            }
//        });
//    }

    /**
     * Recursive relationship to fetch all children categories.
     */
    public function children()
    {
        return $this->hasMany(EmagCategory::class, 'parent_id')->with('children');
    }

    /**
     * Relationship to fetch the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(EmagCategory::class, 'parent_id');
    }

    /**
     * A recursive relationship to fetch all descendants.
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Fetch all ancestors up to the root category.
     */
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    /**
     * Decode the characteristics JSON field.
     */
    public function getCharacteristicsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Encode the characteristics JSON field before saving.
     */
    public function setCharacteristicsAttribute($value)
    {
        $this->attributes['characteristics'] = json_encode($value);
    }

    /**
     * Decode the family_types JSON field.
     */
    public function getFamilyTypesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Encode the family_types JSON field before saving.
     */
    public function setFamilyTypesAttribute($value)
    {
        $this->attributes['family_types'] = json_encode($value);
    }

    /**
     * Check if the category is a root category (has no parent).
     */
    public function isRootCategory()
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if the category has children.
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }
}
