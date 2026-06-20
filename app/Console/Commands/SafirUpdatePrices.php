<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SafirUpdatePrices extends Command
{
    protected $signature = 'safir:update-prices {path : The path to the Excel file}';
    protected $description = 'Update product prices based on "OBSERVATII MODEL / CULOARE" column from a Safir Excel file using the source_link from the "ARTICOL" column';

    public function handle()
    {
        $path = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("File not found at: $path");
            return 1;
        }

        $this->info("Loading file: $path");

        try {
            $spreadsheet = IOFactory::load($path);
        } catch (\Exception $e) {
            $this->error("Error loading file: " . $e->getMessage());
            return 1;
        }

        // Use the active sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Set header row (assume headers start at row 1)
        $headerRow = 1;
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Build header map from row 1
        $headerMap = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $cellValue = trim((string) $sheet->getCell($columnLetter . $headerRow)->getValue());
            if ($cellValue) {
                // Convert to uppercase for case-insensitive matching
                $headerMap[strtoupper($cellValue)] = $col;
            }
        }

        // Uncomment the following line to debug the header map
        // dd($headerMap);

        // Check that required columns exist: "ARTICOL" and "OBSERVATII MODEL / CULOARE"
        if (!isset($headerMap['ARTICOL']) || !isset($headerMap['OBSERVATII MODEL / CULOARE'])) {
            $this->error('Missing required columns "ARTICOL" and/or "OBSERVATII MODEL / CULOARE" in the Excel file (row 1).');
            return 1;
        }

        $updatedCount = 0;

        // Loop through each row from row 2 onward
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $articolColIndex = $headerMap['ARTICOL'];
            $observatiiColIndex = $headerMap['OBSERVATII MODEL / CULOARE'];

            // Convert column indexes to letters
            $articolColLetter = Coordinate::stringFromColumnIndex($articolColIndex);
            $observatiiColLetter = Coordinate::stringFromColumnIndex($observatiiColIndex);

            // Retrieve the cell from the "ARTICOL" column
            $articolCell = $sheet->getCell($articolColLetter . $row);
            // Use hyperlink URL if available, otherwise the cell value
            $sourceLink = $articolCell->hasHyperlink()
                ? trim($articolCell->getHyperlink()->getUrl())
                : trim((string)$articolCell->getValue());

            // Get the "OBSERVATII MODEL / CULOARE" cell value
            $observatiiValue = trim((string)$sheet->getCell($observatiiColLetter . $row)->getValue());

            if (empty($sourceLink)) {
                continue;
            }

            // Extract the last numeric value from the observations text
            $newPrice = $this->extractLastNumber($observatiiValue);
            if ($newPrice === null) {
                $this->warn("No numeric value found in Observatii for source_link: {$sourceLink}");
                continue;
            }

            // Find the product by matching the source_link field
            $product = Product::where('source_link', $sourceLink)->first();
            if (!$product) {
                $this->warn("No product found with source_link: {$sourceLink}");
                continue;
            }

            $oldPrice = $product->price;
            $product->price = $newPrice;
            $product->save();

            $updatedCount++;
            $this->info("Updated product [ID {$product->id}, name: '{$product->name}'] from price {$oldPrice} to {$newPrice}");
        }

        $this->info("Finished updating acquisition prices. Total products updated: {$updatedCount}");
        return 0;
    }

    /**
     * Extract the last numeric value (integer or decimal) from a given string.
     * Examples:
     * - "0.04 lei buc 4 lei set" returns 4
     * - "0.16 lei buc 8 lei set" returns 8
     * - "19 lei set" returns 19
     * - "44 LEI BAX" returns 44
     *
     * Returns null if no numeric value is found.
     */
    private function extractLastNumber(string $text): ?float
    {
        preg_match_all('/(\d+(\.\d+)?)/', $text, $matches);
        if (empty($matches[1])) {
            return null;
        }
        $lastNumericString = end($matches[1]);
        return (float)$lastNumericString;
    }
}
