<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportCategories extends Command
{
    protected $signature = 'import:categories {file}';
    protected $description = 'Import categories from an XLSX file';

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

        $categoryIndex = array_search('КАТЕГОРИИ', $headers);

        if ($categoryIndex === false) {
            $this->error("No 'КАТЕГОРИИ' column found in headers.");
            return;
        }

        foreach ($rows as $row) {
            $categories = [];
            for ($i = $categoryIndex; $i < count($headers); $i++) {
                if (str_starts_with($headers[$i], 'КАТЕГОРИИ') && !empty($row[$i])) {
                    $categories[] = $row[$i];
                }
            }

            $this->createCategoryHierarchy($categories);
        }

        $this->info('Categories imported successfully.');
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

            $category = Category::firstOrCreate([
                'name' => $name,
                'parent_id' => $parentCategory ? $parentCategory->id : null,
            ]);

            if (!$category) {
                $this->error("Failed to create category: $name");
                continue;
            }

            $this->info('Created category: ' . $category->name . ' with parent_id: ' . ($category->parent_id ?? 'null'));

            $parentCategory = $category;
        }
    }
}
