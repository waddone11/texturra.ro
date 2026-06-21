<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\QuoteLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_line_compute_adds_21_percent_vat_on_net(): void
    {
        // 2 × 10 (without VAT) → net 20, VAT 4.20, total 24.20
        $this->assertSame(
            ['net' => 20.0, 'vat' => 4.20, 'total' => 24.20],
            QuoteLine::compute(2, 10)
        );

        // rounding check: 3 × 33.33 → net 99.99, VAT 21.00 (99.99*0.21=20.9979), total 120.99
        $this->assertSame(
            ['net' => 99.99, 'vat' => 21.0, 'total' => 120.99],
            QuoteLine::compute(3, 33.33)
        );
    }

    public function test_quote_totals_sum_the_lines(): void
    {
        $quote = Quote::create([
            'quote_number' => Quote::generateNumber(),
            'client_name' => 'ACME SRL',
        ]);

        foreach ([[2, 10.0], [1, 50.0]] as [$qty, $price]) {
            $line = new QuoteLine(['quantity' => $qty, 'unit_price' => $price, 'description' => 'x', 'unit' => 'buc']);
            $line->quote_id = $quote->id;
            $line->computeTotals()->save();
        }

        $quote->load('lines');
        $quote->recalculateTotals();

        // line1: net 20 / vat 4.20 / total 24.20 ; line2: net 50 / vat 10.50 / total 60.50
        $this->assertEquals(70.00, $quote->total_net);
        $this->assertEquals(14.70, $quote->total_vat);
        $this->assertEquals(84.70, $quote->total_gross);
    }

    public function test_generate_number_is_per_year_sequential(): void
    {
        $year = now()->year;

        $this->assertSame("OF-{$year}-0001", Quote::generateNumber());

        Quote::create(['quote_number' => "OF-{$year}-0001", 'client_name' => 'A']);
        $this->assertSame("OF-{$year}-0002", Quote::generateNumber());
    }
}
