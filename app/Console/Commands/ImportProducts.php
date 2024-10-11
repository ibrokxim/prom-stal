<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportProducts extends Command
{
    protected $signature = 'import:products {file}';
    protected $description = 'Import products and their categories from an XLSX file';

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

        $categoryIndex = array_search('КАТЕГОРИИ', $headers);

        if ($categoryIndex === false) {
            $this->error("No 'КАТЕГОРИИ' column found in headers.");
            Log::error("No 'КАТЕГОРИИ' column found in headers.");
            return;
        }

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

            // Обрабатываем категории
            $categories = [];
            for ($i = $categoryIndex; $i < count($headers); $i++) {
                if (str_starts_with($headers[$i], 'КАТЕГОРИИ') && !empty($row[$i])) {
                    $categories[] = $row[$i];
                }
            }

            $category = $this->createCategoryHierarchy($categories);

            if ($category) {
                // Связываем продукт с категорией
                $product->categories()->attach($category->id);

                $this->info('Product linked to category: ' . $category->name);
                Log::info('Product linked to category: ' . $category->name);
            } else {
                $this->error("Category not found or created");
                Log::error("Category not found or created");
            }
        }

        $this->info('Products and categories imported successfully.');
        Log::info('Products and categories imported successfully.');
    }

    protected function createCategoryHierarchy($categories)
    {
        $parentCategory = null;

        foreach ($categories as $name) {
            if (empty($name)) {
                $this->error('Empty category name encountered. Skipping...');
                continue;
            }

            $this->info("Creating category: $name");
            Log::info("Creating category: $name");

            $category = Category::firstOrCreate([
                'name' => $name,
                'parent_id' => $parentCategory ? $parentCategory->id : null,
            ]);

            if (!$category) {
                $this->error("Failed to create category: $name");
                Log::error("Failed to create category: $name");
                continue;
            }

            $this->info('Created category: ' . $category->name . ' with parent_id: ' . ($category->parent_id ?? 'null'));
            Log::info('Created category: ' . $category->name . ' with parent_id: ' . ($category->parent_id ?? 'null'));

            $parentCategory = $category;
        }

        return $parentCategory;
    }
}
