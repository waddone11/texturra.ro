<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'street',
        'city',
        'state',
        'postal_code',
        'is_default',
        'latitude',
        'longitude',
    ];

    /**
     * Relationship with User.
     * An address belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Orders.
     * An address can be linked to many orders as a shipping address.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_address', 'id');
    }
}
