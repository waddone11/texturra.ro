<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'product_id',
        'description',
        'unit',
        'quantity',
        'unit_price',
        'line_net',
        'line_vat',
        'line_total',
        'position',
    ];

    protected static function booted(): void
    {
        // Always keep the computed columns correct, however the line is saved.
        static::saving(function (QuoteLine $line) {
            $line->computeTotals();
        });
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Compute net / VAT / total for this line. unit_price is WITHOUT VAT
     * (B2B proforma convention) — VAT is added on top at Quote::VAT_RATE.
     *
     * Returns the values so it is trivially unit-testable.
     *
     * @return array{net: float, vat: float, total: float}
     */
    public static function compute(float $quantity, float $unitPrice): array
    {
        $net = round($quantity * $unitPrice, 2);
        $vat = round($net * Quote::VAT_RATE, 2);
        $total = round($net + $vat, 2);

        return ['net' => $net, 'vat' => $vat, 'total' => $total];
    }

    /**
     * Fill this line's computed columns from quantity × unit_price.
     */
    public function computeTotals(): static
    {
        $c = static::compute((float) $this->quantity, (float) $this->unit_price);
        $this->line_net = $c['net'];
        $this->line_vat = $c['vat'];
        $this->line_total = $c['total'];

        return $this;
    }
}
