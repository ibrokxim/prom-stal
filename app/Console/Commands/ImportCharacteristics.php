<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Characteristic;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportCharacteristics extends Command
{
    protected $signature = 'import:characteristics {file}';
    protected $description = 'Import characteristics for products from an XLSX file';

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

        // Индекс колонки с наименованием продукта
        $productIndex = array_search('НАИМЕНОВАНИЕ', $headers);
        if ($productIndex === false) {
            $this->error("No 'НАИМЕНОВАНИЕ' column found in headers.");
            return;
        }

        // Индекс колонки с характеристиками
        $characteristicIndex = array_search('ХАРАКТЕРИСТИКИ', $headers);
        if ($characteristicIndex === false) {
            $this->error("No 'ХАРАКТЕРИСТИКИ' column found in headers.");
            return;
        }

        foreach ($rows as $row) {
            $productName = $row[$productIndex];

            // Найдем продукт по названию
            $product = Product::where('name', $productName)->first();
            if (!$product) {
                $this->error("Product not found: $productName");
                continue;
            }

            // Извлекаем пары характеристик (ключ - значение)
            $characteristics = [];
            for ($i = $characteristicIndex; $i < count($headers); $i++) {
                if (str_starts_with($headers[$i], 'ХАРАКТЕРИСТИКИ') && !empty($row[$i])) {
                    $characteristics[] = $row[$i];
                }
            }

            $this->createCharacteristics($product, $characteristics);
        }

        $this->info('Characteristics imported successfully.');
    }

    protected function createCharacteristics($product, $characteristics)
    {
        $characteristicPairs = [];
        $currentKey = null;

        foreach ($characteristics as $characteristic) {
            if (empty($characteristic)) {
                continue;
            }

            // Пара ключ-значение для характеристики
            if ($currentKey === null) {
                $currentKey = $characteristic;
            } else {
                $characteristicPairs[$currentKey] = $characteristic;
                $currentKey = null;
            }
        }

        // Сохранение характеристик для продукта
        foreach ($characteristicPairs as $key => $value) {
            $characteristic = Characteristic::firstOrCreate(['name' => $key]);

            if (!$characteristic) {
                $this->error("Failed to create characteristic: $key");
                continue;
            }

            // Связываем характеристику с продуктом и добавляем значение
            $product->characteristics()->attach($characteristic->id, ['value' => $value]);

            $this->info('Linked characteristic: ' . $characteristic->name . ' with value: ' . $value);
        }
    }
}
