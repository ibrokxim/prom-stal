<?php
//
//namespace App\Console\Commands;
//
//use App\Models\Product;
//use App\Models\Category;
//use App\Models\Characteristic;
//use Illuminate\Console\Command;
//use PhpOffice\PhpSpreadsheet\IOFactory;
//
//class ImportProducts extends Command
//{
//    protected $signature = 'import:products {file}';
//    protected $description = 'Import products from an XLSX file';
//
//    public function handle()
//    {
//        $filePath = $this->argument('file');
//
//        if (!file_exists($filePath)) {
//            $this->error("File not found: $filePath");
//            return;
//        }
//
//        $spreadsheet = IOFactory::load($filePath);
//        $worksheet = $spreadsheet->getActiveSheet();
//        $rows = $worksheet->toArray();
//
//        $headers = array_shift($rows);
//
//        $this->info('Headers: ' . implode(', ', $headers));
//
//        foreach ($rows as $row) {
//            $record = array_combine($headers, $row);
//
//            if (!isset($record['НАИМЕНОВАНИЕ']) || !isset($record['КАРТИНКА']) || !isset($record['ОПИСАНИЕ'])) {
//                $this->error("Missing required keys in record: " . json_encode($record));
//                continue;
//            }
//
//            $product = Product::create([
//                'name' => $record['НАИМЕНОВАНИЕ'],
//                'image' => $record['КАРТИНКА'],
//                'description' => $record['ОПИСАНИЕ'],
//            ]);
//
//            // Создаем категории
//            $categories = [];
//            for ($i = array_search('КАТЕГОРИИ', $headers); $i < count($headers); $i++) {
//                if (str_starts_with($headers[$i], 'КАТЕГОРИИ') && !empty($record[$headers[$i]])) {
//                    $categories[] = $record[$headers[$i]];
//                }
//            }
//
//            $lastCategory = $this->createCategory($categories);
//
//            if (!$lastCategory) {
//                $this->error('Failed to create category. Skipping product.');
//                continue;
//            }
//
//            $product->categories()->attach($lastCategory->id);
//
//            // Создаем характеристики
//            $characteristics = [];
//            for ($i = array_search('ХАРАКТЕРИСТИКИ', $headers); $i < count($headers); $i++) {
//                if (str_starts_with($headers[$i], 'ХАРАКТЕРИСТИКИ') && !empty($record[$headers[$i]])) {
//                    $characteristics[] = $record[$headers[$i]];
//                }
//            }
//
//            $this->createCharacteristics($product, $characteristics);
//        }
//
//        $this->info('Products imported successfully.');
//    }
//
//    protected function createCategory($categories, $parentId = null)
//    {
//        $parentCategory = null;
//
//        foreach ($categories as $name) {
//            // Проверим, есть ли название категории
//            if (empty($name)) {
//                $this->error('Empty category name encountered. Skipping...');
//                continue;
//            }
//
//            // Логируем создание категории
//            $this->info("Creating category: $name");
//
//            $category = Category::firstOrCreate([
//                'name' => $name,
//                'parent_id' => $parentCategory ? $parentCategory->id : null,
//            ]);
//
//            $this->info('Created category: ' . $category->name . ' with parent_id: ' . ($category->parent_id ?? 'null'));
//
//            $parentCategory = $category;
//        }
//
//        return $parentCategory;
//    }
//
//    protected function createCharacteristics($product, $characteristics)
//    {
//        $characteristicPairs = [];
//        $currentKey = null;
//
//        foreach ($characteristics as $characteristic) {
//            if (empty($characteristic)) {
//                continue;
//            }
//
//            if ($currentKey === null) {
//                $currentKey = $characteristic;
//            } else {
//                $characteristicPairs[$currentKey] = $characteristic;
//                $currentKey = null;
//            }
//        }
//
//        foreach ($characteristicPairs as $key => $value) {
//            $characteristic = Characteristic::firstOrCreate(['name' => $key]);
//            $product->characteristics()->attach($characteristic->id, ['value' => $value]);
//        }
//    }
//}


namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportProducts extends Command
{
    protected $signature = 'import:products {file}';
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

            $productData = [
                'name' => $record['НАИМЕНОВАНИЕ'],
                'image' => $record['КАРТИНКА'],
                'description' => $record['ОПИСАНИЕ'],
            ];

            // Создаем характеристики
            $characteristics = [];
            for ($i = array_search('ХАРАКТЕРИСТИКИ', $headers); $i < count($headers); $i++) {
                if (str_starts_with($headers[$i], 'ХАРАКТЕРИСТИКИ') && !empty($record[$headers[$i]])) {
                    $characteristics[] = $record[$headers[$i]];
                }
            }

            $this->addCharacteristicsToProductData($productData, $characteristics);

            $product = Product::create($productData);

            $this->info('Product created: ' . $product->name);
        }

        $this->info('Products and characteristics imported successfully.');
    }

    protected function addCharacteristicsToProductData(&$productData, $characteristics)
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

        $i = 1;
        foreach ($characteristicPairs as $key => $value) {
            $productData['characteristic_' . $i] = $key . ': ' . $value;
            $i++;
        }
    }
}
