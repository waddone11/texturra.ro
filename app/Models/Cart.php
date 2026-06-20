<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * Columns that, together, identify a distinct cart line. For custom curtains
     * the dimensions + manufacturing + pieces are part of the item's identity
     * (a 2.5x1.8 single-panel curtain != a 2.0x1.5 pair).
     */
    public const IDENTITY_KEYS = ['product_id', 'length', 'height', 'manufactoring_type_id', 'pieces'];

    /**
     * Merge a guest session's cart into the authenticated user's cart.
     *
     * Identity = product_id + length + height + manufactoring_type_id + pieces.
     * Same identity => quantities are summed (existing user price kept).
     * New identity  => a new row copying ALL custom fields (no data loss).
     */
    public static function mergeSessionIntoUser(string $sessionId, int $userId): void
    {
        DB::transaction(function () use ($sessionId, $userId) {
            $sessionItems = static::where('session_id', $sessionId)->get();

            foreach ($sessionItems as $item) {
                $query = static::where('user_id', $userId);

                foreach (self::IDENTITY_KEYS as $key) {
                    $value = $item->{$key};
                    is_null($value) ? $query->whereNull($key) : $query->where($key, $value);
                }

                $existing = $query->first();

                if ($existing) {
                    // Same item already in the user's cart: add the quantities,
                    // keep the user's existing price (don't overwrite).
                    $existing->quantity = ($existing->quantity ?? 0) + ($item->quantity ?? 0);
                    $existing->save();
                } else {
                    static::create([
                        'user_id'               => $userId,
                        'product_id'            => $item->product_id,
                        'quantity'              => $item->quantity,
                        'price'                 => $item->price,
                        'length'                => $item->length,
                        'height'                => $item->height,
                        'manufactoring_type_id' => $item->manufactoring_type_id,
                        'pieces'                => $item->pieces,
                    ]);
                }

                $item->delete();
            }
        });
    }

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
