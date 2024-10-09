<?php
namespace Database\Seeders;

use League\Csv\Reader;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $csv = Reader::createFromPath(database_path('seeders/csv/cat1.csv'), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            $categoryNames = array_filter($record, function ($key) {
                return strpos($key, 'КАТЕГОРИИ') !== false;
            }, ARRAY_FILTER_USE_KEY);

            $parentCategory = null;

            foreach ($categoryNames as $categoryName) {
                $category = Category::firstOrCreate([
                    'name_plural' => $categoryName,
                    'slug' => Str::slug($categoryName),
                    'parent_id' => $parentCategory ? $parentCategory->id : null,
                ]);
                $parentCategory = $category;
            }
        }
    }
}
