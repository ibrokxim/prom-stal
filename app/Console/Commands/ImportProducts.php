<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Characteristic;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class ImportProducts extends Command
{
    protected $signature = 'import:products {file}';
    protected $description = 'Import products and their characteristics from an XLSX file';

    public function handle()
    {
        $filePath = $this->argument('file');
        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            Log::error("File not found: $filePath");
            return;
        }

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $headers = array_shift($rows);

        $this->info('Headers: ' . implode(', ', $headers));
        Log::info('Headers: ' . implode(', ', $headers));

        // Логируем индекс заголовка "ХАРАКТЕРИСТИКИ"
        $characteristicIndex = array_search('ХАРАКТЕРИСТИКИ', $headers);
        $this->info('Characteristic index: ' . $characteristicIndex);
        Log::info('Characteristic index: ' . $characteristicIndex);

        foreach ($rows as $row) {
            $record = array_combine($headers, $row);

            if (!isset($record['НАИМЕНОВАНИЕ']) || !isset($record['КАРТИНКА']) || !isset($record['ОПИСАНИЕ'])) {
                $this->error("Missing required keys in record: " . json_encode($record));
                Log::error("Missing required keys in record: " . json_encode($record));
                continue;
            }

            $productData = [
                'name' => $record['НАИМЕНОВАНИЕ'],
                'image' => $record['КАРТИНКА'],
                'description' => $record['ОПИСАНИЕ'],
            ];

            // Создаем продукт
            $product = Product::create($productData);

            $this->info('Product created: ' . $product->name);
            Log::info('Product created: ' . $product->name);

            // Обработка характеристик
            $characteristics = [];
            for ($i = $characteristicIndex; $i < count($headers); $i++) {
                if (str_starts_with($headers[$i], 'ХАРАКТЕРИСТИКИ') && !empty($record[$headers[$i]])) {
                    $characteristics[] = $record[$headers[$i]];
                }
            }

            // Логируем извлеченные характеристики
            $this->info('Extracted characteristics: ' . implode(', ', $characteristics));
            Log::info('Extracted characteristics: ' . implode(', ', $characteristics));

            // Добавляем характеристики к продукту
            $this->addCharacteristicsToProduct($product, $characteristics);
        }

        $this->info('Products and characteristics imported successfully.');
        Log::info('Products and characteristics imported successfully.');
    }

    protected function addCharacteristicsToProduct(Product $product, $characteristics)
    {
        $characteristicPairs = [];
        $currentKey = null;

        foreach ($characteristics as $characteristic) {
            if (empty($characteristic)) {
                continue;
            }

            // Сначала ключ, затем значение характеристики
            if ($currentKey === null) {
                $currentKey = $characteristic;
            } else {
                // Сопоставляем ключ и значение
                $characteristicPairs[$currentKey] = $characteristic;
                $currentKey = null;
            }
        }

        // Логируем созданные пары ключ-значение
        $this->info('Characteristic pairs: ' . json_encode($characteristicPairs));
        Log::info('Characteristic pairs: ' . json_encode($characteristicPairs));

        foreach ($characteristicPairs as $key => $value) {
            // Находим или создаем характеристику
            $characteristic = Characteristic::firstOrCreate(['name' => $key]);

            // Проверяем, существует ли уже такая связь
            if (!$product->characteristics->contains($characteristic->id)) {
                // Создаем запись в таблице product_characteristics
                $product->characteristics()->attach($characteristic->id, ['value' => $value]);

                $this->info('Added characteristic to product: ' . $characteristic->name . ' => ' . $value);
                Log::info('Added characteristic to product: ' . $characteristic->name . ' => ' . $value);
            } else {
                $this->info('Characteristic already exists for product: ' . $characteristic->name);
                Log::info('Characteristic already exists for product: ' . $characteristic->name);
            }
        }
    }
}
