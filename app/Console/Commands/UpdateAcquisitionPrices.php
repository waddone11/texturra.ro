<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class UpdateAcquisitionPrices extends Command
{
    protected $signature = 'products:update-acquisition-prices {path : The path to the Excel file}';
    protected $description = 'Update the acquisition_price for products based on an Excel file mapping';

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

        // Use the first (active) sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Set the header row (headers start at row 1)
        $headerRow = 1;

        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Build a header map from row 1
        $headerMap = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $cellValue = trim((string) $sheet->getCell($columnLetter . $headerRow)->getValue());
            if ($cellValue) {
                $headerMap[$cellValue] = $col;
            }
        }

        // Convert header keys to uppercase for case-insensitive matching
        $headerMap = array_change_key_case($headerMap, CASE_UPPER);

        // For debugging: uncomment the next line to see the header map
        // dd($headerMap);

        // Check that required columns exist. We need "ARTICOL" and "PRET/UM"
        if (!isset($headerMap['ARTICOL']) || !isset($headerMap['PRET/UM'])) {
            $this->error('Missing required columns "ARTICOL" and/or "PRET/UM" in the Excel file (row 1).');
            return 1;
        }

        $updatedCount = 0;

        // Loop through each row from row 2 onward
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $articolColLetter = Coordinate::stringFromColumnIndex($headerMap['ARTICOL']);
            $pretUmColLetter  = Coordinate::stringFromColumnIndex($headerMap['PRET/UM']);

            // Use the hyperlink URL if available, otherwise the cell value.
            $articolCell = $sheet->getCell($articolColLetter . $row);
            $sourceLink = $articolCell->hasHyperlink()
                ? trim($articolCell->getHyperlink()->getUrl())
                : trim((string)$articolCell->getValue());

            $acquisitionPrice = $sheet->getCell($pretUmColLetter . $row)->getCalculatedValue();

            // For debugging: dump the values
            // dd($sourceLink, $acquisitionPrice);

            if (empty($sourceLink)) {
                continue;
            }

            // Find the product by source_link
            $product = Product::where('source_link', $sourceLink)->first();
            if ($product) {
                $product->acquisition_price = $acquisitionPrice;
                $product->save();
                $updatedCount++;
                $this->info("Updated product ID {$product->id} with acquisition price: {$acquisitionPrice}");
            } else {
                $this->warn("No product found with source_link: {$sourceLink}");
            }
        }

        $this->info("Finished updating acquisition prices. Total updated: {$updatedCount}");
        return 0;
    }
}
