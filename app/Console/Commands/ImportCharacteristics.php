<?php

namespace App\Console\Commands;

use App\Models\Characteristic;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportCharacteristics extends Command
{
    protected $signature = 'import:characteristics {file}';
    protected $description = 'Import characteristics from an XLSX file';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return;
        }

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $headers = array_shift($rows);

        $this->info('Headers: ' . implode(', ', $headers));

        // Найдем индекс первого столбца с заголовком "ХАРАКТЕРИСТИКИ"
        $characteristicIndex = array_search('ХАРАКТЕРИСТИКИ', $headers);

        if ($characteristicIndex === false) {
            $this->error("No 'ХАРАКТЕРИСТИКИ' column found in headers.");
            return;
        }

        foreach ($rows as $row) {
            $characteristics = [];
            for ($i = $characteristicIndex; $i < count($headers); $i++) {
                if (str_starts_with($headers[$i], 'ХАРАКТЕРИСТИКИ') && !empty($row[$i])) {
                    $characteristics[] = $row[$i];
                }
            }

            $this->createCharacteristics($characteristics);
        }

        $this->info('Characteristics imported successfully.');
    }

    protected function createCharacteristics($characteristics)
    {
        $characteristicPairs = [];
        $currentKey = null;

        foreach ($characteristics as $characteristic) {
            if (empty($characteristic)) {
                continue;
            }

            if ($currentKey === null) {
                $currentKey = $characteristic;
            } else {
                $characteristicPairs[$currentKey] = $characteristic;
                $currentKey = null;
            }
        }

        foreach ($characteristicPairs as $key => $value) {
            $characteristic = Characteristic::firstOrCreate(['name' => $key]);

            if (!$characteristic) {
                $this->error("Failed to create characteristic: $key");
                continue;
            }

            $this->info('Created characteristic: ' . $characteristic->name);
        }
    }
}


