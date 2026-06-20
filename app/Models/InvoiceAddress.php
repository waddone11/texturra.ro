<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAddress extends Model
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
     * An invoice address belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Orders.
     * An invoice address can be linked to many orders as a billing address.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'billing_address', 'id');
    }
}
