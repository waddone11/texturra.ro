<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function values()
    {
//        return $this->hasMany(AttributeValue::class);
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }

}


//id	name	description	created_at	updated_at
//1	Unitate de măsură	Unitate de măsură	NULL	NULL
//2	Cantitate per ambalaj	Cantitate per ambalaj	NULL	NULL
//3	Image	Image	NULL	NULL
