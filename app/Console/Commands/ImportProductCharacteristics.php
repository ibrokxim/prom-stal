<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Characteristic;
use App\Models\ProductCharacteristic;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportProductCharacteristics extends Command
{
    protected $signature = 'import:product_characteristics {file}';
    protected $description = 'Import products and their characteristics from an XLSX file';

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

        foreach ($rows as $row) {
            $record = array_combine($headers, $row);

            if (!isset($record['НАИМЕНОВАНИЕ']) || !isset($record['КАРТИНКА']) || !isset($record['ОПИСАНИЕ'])) {
                $this->error("Missing required keys in record: " . json_encode($record));
                continue;
            }

            $product = Product::create([
                'name' => $record['НАИМЕНОВАНИЕ'],
                'image' => $record['КАРТИНКА'],
                'description' => $record['ОПИСАНИЕ'],
            ]);

            $this->info('Product created: ' . $product->name);

            // Создаем характеристики
            $characteristics = [];
            for ($i = array_search('ХАРАКТЕРИСТИКИ', $headers); $i < count($headers); $i++) {
                if (str_starts_with($headers[$i], 'ХАРАКТЕРИСТИКИ') && !empty($record[$headers[$i]])) {
                    $characteristics[] = $record[$headers[$i]];
                }
            }

            $this->createCharacteristics($product, $characteristics);
        }

        $this->info('Products and characteristics imported successfully.');
    }

    protected function createCharacteristics($product, $characteristics)
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

            ProductCharacteristic::create([
                'product_id' => $product->id,
                'characteristic_id' => $characteristic->id,
                'value' => $value,
            ]);

            $this->info('Attached characteristic ' . $characteristic->name . ' with value ' . $value . ' to product ' . $product->name);
        }
    }
}
