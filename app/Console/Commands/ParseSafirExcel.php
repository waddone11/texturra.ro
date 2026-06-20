<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SafirExcel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ParseSafirExcel extends Command
{
    protected $signature = 'safir:parse {file : The path to the Excel file} {--test : Run in test mode (process only one row and dump the data)}';
    protected $description = 'Parse an Excel file and import its data into the safir_excel table';

    // List of fields that should be numeric (or computed as numeric)
    protected $numericFields = [
        'safir_excel_id',
        'safir_buc_set',
        'safir_set_bax',
        'safir_buc_bax'
        // Note: safir_sell_price is computed separately.
    ];

    public function handle()
    {
        $file = $this->argument('file');
        $isTest = $this->option('test');

        if (!file_exists($file)) {
            $this->error("File not found at: $file");
            return 1;
        }

        $this->info("Loading Excel file: $file");

        try {
            $spreadsheet = IOFactory::load($file);
        } catch (\Exception $e) {
            $this->error("Error loading file: " . $e->getMessage());
            return 1;
        }

        $sheet = $spreadsheet->getActiveSheet();
        $headerRow = 1;
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Build a header map: uppercase header => column index
        $headerMap = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $cellCoordinate = $columnLetter . $headerRow;
            $cellValue = trim((string)$sheet->getCell($cellCoordinate)->getValue());
            if ($cellValue) {
                $headerMap[strtoupper($cellValue)] = $col;
            }
        }

        // Define required headers mapping: Excel header => safir_excel field
        $requiredHeaders = [
            'NR. CRT.'                   => 'safir_excel_id',
            'ARTICOL'                    => 'safir_excel_name',
            'DETALII'                    => 'safir_excel_details',
            'BUC/SET'                    => 'safir_buc_set',
            'SET /BAX'                   => 'safir_set_bax',
            'BUC/BAX'                    => 'safir_buc_bax',
            'UM'                         => 'safir_um',
            'OBSERVATII MODEL / CULOARE' => 'safir_sell_price'
        ];

        // Verify that all required headers exist.
        foreach ($requiredHeaders as $header => $field) {
            if (!isset($headerMap[strtoupper($header)])) {
                $this->error("Missing required header: '$header'");
                return 1;
            }
        }

        $imported = 0;

        // Loop through each row starting at row 2
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $data = [];
            foreach ($requiredHeaders as $header => $field) {
                $colIndex = $headerMap[strtoupper($header)];
                $columnLetter = Coordinate::stringFromColumnIndex($colIndex);
                $cellCoordinate = $columnLetter . $row;
                $cell = $sheet->getCell($cellCoordinate);
                $value = trim((string)$cell->getValue());

                // For the "ARTICOL" header, check if the cell has a hyperlink.
                if (strtoupper($header) === 'ARTICOL') {
                    $hyperlink = $cell->getHyperlink();
                    if ($hyperlink && $hyperlink->getUrl()) {
                        $data['safir_excel_link'] = trim($hyperlink->getUrl());
                        $data['safir_link_exist'] = true;
                    } else {
                        $data['safir_excel_link'] = null;
                        $data['safir_link_exist'] = false;
                    }
                    // Save the cell value as the safir_excel_name
                    $data[$field] = $value !== '' ? $value : null;
                    continue;
                }

                // For the "OBSERVATII MODEL / CULOARE" header, extract the last numeric value.
                if (strtoupper($header) === 'OBSERVATII MODEL / CULOARE') {
                    $newPrice = $this->extractLastNumber($value);
                    $data[$field] = $newPrice;
                    continue;
                }

                // If the value is an empty string, set it to null.
                if ($value === '') {
                    $value = null;
                }

                // For fields that are meant to be numeric, check and cast the value.
                if (in_array($field, $this->numericFields)) {
                    if (!is_null($value) && !is_numeric($value)) {
                        $this->warn("Value '$value' for field '$field' in row $row is not numeric. Setting to null.");
                        $value = null;
                    } elseif (!is_null($value)) {
                        $value = $value + 0;
                    }
                }

                $data[$field] = $value;
            }

            // If in test mode, dump the first row's data and exit.
            if ($isTest) {
                dd("Test mode: Row $row parsed data:", $data);
            }

            // If safir_excel_id is empty, remove it (so that auto-increment takes over)
            if (empty($data['safir_excel_id'])) {
                unset($data['safir_excel_id']);
            }

            try {
                SafirExcel::create($data);
                $imported++;
            } catch (\Exception $e) {
                $this->error("Error importing row $row: " . $e->getMessage());
            }
        }

        $this->info("Finished importing. Total rows imported: $imported");
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
     *
     * @param string $text
     * @return float|null
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
