<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ManufactoringType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
