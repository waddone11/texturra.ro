<?php

namespace Tests\Feature\Console;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

/**
 * Safety net for `products:update-acquisition-prices` BEFORE the phpspreadsheet
 * 4 -> 5 major bump. The command reads an Excel file (ARTICOL = source_link,
 * PRET/UM = acquisition price) and writes products.acquisition_price by matching
 * source_link. It touches real prices and was previously untested.
 *
 * Pins the current (v4) behaviour: matched products get the price, unmatched
 * products stay untouched, missing file fails cleanly. If the major bump breaks
 * the Xlsx reader API, this test goes red.
 */
class UpdateAcquisitionPricesTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(string $sourceLink, float $acquisition): Product
    {
        $cat = Category::create(['name' => 'Perdele', 'slug' => 'perdele-' . uniqid()]);

        $product = Product::create([
            'name'          => 'Produs ' . uniqid(),
            'description'   => 'desc',
            'price'         => 10.00,
            'ean'           => 'EAN-' . uniqid(),
            'category_id'   => $cat->id,
            'general_stock' => 1,
            'product_code'  => 'TEX-' . uniqid(),
            'status'        => 1,
        ]);

        // source_link / acquisition_price are not in $fillable — set directly.
        $product->source_link = $sourceLink;
        $product->acquisition_price = $acquisition;
        $product->save();

        return $product;
    }

    /** Build a real .xlsx fixture; rows = [ [col1, col2], ... ] with row 1 the headers. */
    private function makeFixture(array $rows): string
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($rows, null, 'A1');

        $path = tempnam(sys_get_temp_dir(), 'acq') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }

    public function test_acquisition_prices_updated_from_excel_by_source_link(): void
    {
        $matchA   = $this->makeProduct('https://example.com/p1', 0.00);
        $matchB   = $this->makeProduct('https://example.com/p2', 0.00);
        $untouched = $this->makeProduct('https://example.com/other', 5.55);

        $path = $this->makeFixture([
            ['ARTICOL', 'PRET/UM'],
            ['https://example.com/p1', 12.34],
            ['https://example.com/p2', 56.78],
            ['https://example.com/nomatch', 99.99], // no product -> ignored
        ]);

        $this->artisan('products:update-acquisition-prices', ['path' => $path])
            ->assertExitCode(0);

        $this->assertEqualsWithDelta(12.34, (float) $matchA->fresh()->acquisition_price, 0.001);
        $this->assertEqualsWithDelta(56.78, (float) $matchB->fresh()->acquisition_price, 0.001);
        // product whose source_link is absent from the sheet stays as it was
        $this->assertEqualsWithDelta(5.55, (float) $untouched->fresh()->acquisition_price, 0.001);

        @unlink($path);
    }

    public function test_missing_file_fails_cleanly(): void
    {
        $this->artisan('products:update-acquisition-prices', ['path' => '/tmp/does-not-exist-' . uniqid() . '.xlsx'])
            ->assertExitCode(1);
    }
}
