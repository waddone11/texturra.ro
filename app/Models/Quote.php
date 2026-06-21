<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    /** Current RO standard VAT rate. */
    public const VAT_RATE = 0.21;

    protected $fillable = [
        'quote_number',
        'client_name',
        'client_cif',
        'client_address',
        'client_email',
        'client_phone',
        'notes',
        'status',
        'total_net',
        'total_vat',
        'total_gross',
    ];

    public function lines()
    {
        return $this->hasMany(QuoteLine::class)->orderBy('position');
    }

    /**
     * Next quote number, per-year sequential: OF-2026-0001, OF-2026-0002, …
     */
    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = static::where('quote_number', 'like', "OF-{$year}-%")
            ->orderByDesc('id')
            ->value('quote_number');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return sprintf('OF-%d-%04d', $year, $seq);
    }

    /**
     * Recompute the quote totals from its (already-computed) lines.
     */
    public function recalculateTotals(): void
    {
        $lines = $this->relationLoaded('lines') ? $this->lines : $this->lines()->get();

        $this->total_net = round($lines->sum('line_net'), 2);
        $this->total_vat = round($lines->sum('line_vat'), 2);
        $this->total_gross = round($lines->sum('line_total'), 2);
    }
}
