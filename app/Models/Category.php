<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'emag_id',
        'emag_parent_id',
        'is_ean_mandatory',
        'is_warranty_mandatory',
        'characteristics',
        'family_types',
        'is_allowed',
        'status',
    ];

    /**
     * Automatically generate a slug if not provided.
     */
    public static function boot()
    {
        parent::boot();

        // Automatically create a slug before saving
        static::saving(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * A category may have many subcategories (children).
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children');
    }

    /**
     * A category may have one parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * A recursive relationship to fetch all descendants.
     */
//    public function descendants()
//    {
//        return $this->children()->with('descendants');
//    }

    public function descendants()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('descendants');
    }


    /**
     * Fetch all ancestors up to the root category.
     */
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    /**
     * A category may have many characteristics.
     */
    public function characteristics()
    {
        return $this->hasMany(Characteristic::class, 'category_id');
    }

    /**
     * A category may have many family types.
     */
    public function familyTypes()
    {
        return $this->hasMany(FamilyType::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function scopeActiveTree($query)
    {
        return $query->whereHas('products', function ($query) {
            $query->where('status', 'active'); // Ensure product status is active
        })
            ->where('is_allowed', 1) // Optional: If this condition is required
            ->get()
            ->groupBy('emag_parent_id'); // Group categories by parent ID for easier tree building
    }

    public function discount()
    {
        return $this->hasOne(Discount::class)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function allRelatedCategoryIds()
    {
        $allIds = [$this->id]; // Start with the current category ID

        // Get all descendant categories (children)
        $stack = [$this->id];
        while (!empty($stack)) {
            $parentId = array_pop($stack);
            $children = Category::where('parent_id', $parentId)->pluck('id')->toArray();

            $allIds = array_merge($allIds, $children);
            $stack = array_merge($stack, $children);
        }

        // Get all ancestor categories (parents)
        $parent = $this->parent;
        while ($parent) {
            $allIds[] = $parent->id;
            $parent = $parent->parent;
        }

        return array_unique($allIds); // Remove duplicates
    }





}
