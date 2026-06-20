<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model
{
    use HasFactory;

    protected $fillable = ['color_group_id', 'name', 'cod_css'];

    public function group()
    {
        return $this->belongsTo(ColorGroup::class, 'color_group_id');
    }
}
