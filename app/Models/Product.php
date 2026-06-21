<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'emag_price',
        'commission_percentage',
        'category_id',
        'emag_category_id',
        'images',
        'images_emag',
        'images_emag2',
        'product_code',
        'emag_id',
        'brand_name',
        'part_number',
        'sale_price',
        'currency',
        'warranty',
        'family_type_id',
        'characteristics',
        'attachments',
        'offer_details',
        'barcode',
        'ean',
        'ownership',
        'min_sale_price',
        'max_sale_price',
        'recommended_price',
        'general_stock',
        'status',
        'vat_id',
        'is_synced',
        'height',
        'type',
        'source_link',
        'acquisition_price',
        'model_group_id',
    ];

    protected static function boot()
    {
        parent::boot();

        // Before creating, ensure the slug is set to a base slug.
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            // Also create a plain-text version of the description.
            $product->description_plain = $product->description ? strip_tags($product->description) : '';
        });

        // After creating, append the product ID to the slug to guarantee uniqueness.
        static::created(function ($product) {
            $baseSlug = Str::slug($product->name);
            $newSlug = $baseSlug . '-id-' . $product->id;
            // If the new slug differs from the current one, update it.
            if ($product->slug !== $newSlug) {
                // Use saveQuietly to avoid firing model events recursively.
                $product->updateQuietly(['slug' => $newSlug]);
            }
        });

        // On updating, if the name has changed, update the slug accordingly.
        static::updating(function ($product) {
            // Check if the name is dirty (changed)
            if ($product->isDirty('name')) {
                $baseSlug = Str::slug($product->name);
                $newSlug = $baseSlug . '-id-' . $product->id;
                $product->slug = $newSlug;
            }
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Accessor to decode JSON attributes
    public function getImagesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getCharacteristicsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getAttachmentsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getOfferDetailsAttribute($value)
    {
        return json_decode($value, true);
    }

    // Mutators to encode JSON attributes
    public function setImagesAttribute($value)
    {
        $this->attributes['images'] = json_encode($value);
    }

    public function setCharacteristicsAttribute($value)
    {
        $this->attributes['characteristics'] = json_encode($value);
    }

    public function setAttachmentsAttribute($value)
    {
        $this->attributes['attachments'] = json_encode($value);
    }

    public function setOfferDetailsAttribute($value)
    {
        $this->attributes['offer_details'] = json_encode($value);
    }

    public function getDecodedImagesAttribute()
    {
        $images = json_decode($this->images, true);

        if ($images) {
            return $images;
        }

        return [];
    }

    public function familyType()
    {
        return $this->belongsTo(FamilyType::class, 'family_type_id');
    }

    public function parsedCharacteristics()
    {
        return json_decode($this->characteristics, true) ?? [];
    }

    public function vat()
    {
        return $this->belongsTo(Vat::class);
    }

    public function priceWithoutVat()
    {
        return $this->price / (1 + ($this->vat->rate / 100));
    }

    public function vatAmount()
    {
        return $this->price - $this->priceWithoutVat();
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function price()
    {
        $originalPrice = $this->attributes['price'];

        // Get all related category IDs (parents + children)
        $relatedCategoryIds = $this->category->allRelatedCategoryIds();

        // Fetch product-specific discount
        $productDiscount = $this->discount()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        // Fetch category discount (including parents & children)
        $categoryDiscount = Discount::whereIn('category_id', $relatedCategoryIds)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('percentage', 'desc') // Prioritize highest discount
            ->first();

        // Debugging
        \Log::info("Checking discounts for Product ID {$this->id}", [
            'originalPrice' => $originalPrice,
            'relatedCategoryIds' => $relatedCategoryIds,
            'productDiscount' => $productDiscount,
            'categoryDiscount' => $categoryDiscount
        ]);

        // Choose the best discount
        $bestDiscount = $productDiscount ?? $categoryDiscount;

        if (!$bestDiscount) {
            return $originalPrice; // No discount, return normal price
        }

        // Apply the discount (percentage-based or fixed)
        if ($bestDiscount->percentage) {
            $discountedPrice = $originalPrice - ($originalPrice * ($bestDiscount->percentage / 100));
        } else {
            $discountedPrice = max($originalPrice - $bestDiscount->fixed_amount, 0); // Prevent negative prices
        }

        return round($discountedPrice, 2);
    }




    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    /**
     * Colors this product is offered in, from the shared palette, with
     * per-color stock on the pivot. Price stays uniform on the product.
     */
    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_color')
            ->withPivot('stock')
            ->withTimestamps();
    }

    public function discount()
    {
        return $this->hasMany(Discount::class);
    }

    /**
     * Sibling products = the same model in other dimensions (shared model_group_id),
     * excluding self. Returns a query (ordered by price) — empty when ungrouped.
     * Admin/model only; the storefront display is a later phase.
     */
    public function siblings()
    {
        if (empty($this->model_group_id)) {
            return static::query()->whereRaw('1 = 0');
        }

        return static::query()
            ->where('model_group_id', $this->model_group_id)
            ->where('id', '!=', $this->getKey())
            ->orderBy('price');
    }

    /**
     * Whether this product belongs to a model group with at least one other member.
     */
    public function hasSiblings(): bool
    {
        return ! empty($this->model_group_id)
            && static::query()
                ->where('model_group_id', $this->model_group_id)
                ->where('id', '!=', $this->getKey())
                ->exists();
    }

    // Stock calculation
    public function stock()
    {
        //return $this->variations()->sum('stock');
        return $this->stock;
    }

    public function getPivotMeta($key = null)
    {
        $meta = json_decode($this->pivot->meta ?? '{}', true);
        return $key ? ($meta[$key] ?? null) : $meta;
    }

}
