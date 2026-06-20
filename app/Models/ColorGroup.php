<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ColorGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image_path'];

    public function colors()
    {
        return $this->hasMany(Color::class);
    }
}

