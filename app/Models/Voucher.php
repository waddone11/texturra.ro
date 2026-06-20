<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_amount',
        'discount_percentage',
        'valid_from',
        'valid_to',
        'usage_limit',
        'times_used',
        'active',
    ];

    public function isValid()
    {
        return $this->active &&
            ($this->valid_from ? $this->valid_from <= now() : true) &&
            ($this->valid_to ? $this->valid_to >= now() : true) &&
            ($this->usage_limit === null || $this->times_used < $this->usage_limit);
    }

    public function incrementUsage()
    {
        if ($this->usage_limit === null || $this->times_used < $this->usage_limit) {
            $this->increment('times_used');
        } else {
            throw new \Exception("Voucher usage limit exceeded");
        }
    }

    public function display()
    {
        return $this->discount_amount
            ? $this->discount_amount . ' RON'
            : $this->discount_percentage . '%';
    }

}
