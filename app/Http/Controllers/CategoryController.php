<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\PaginateTrait;
use Illuminate\Support\Facades\Cache;
class CategoryController extends Controller
{
    use PaginateTrait;
    public function getAllCategories()
    {
        $tree = Cache::remember('all_categories', 60, function () {
            $tree = [];
            $categories = Category::whereNull('parent_id')->with('subcategories')->lazy();

            foreach ($categories as $category) {
                $tree[] = $this->buildCategoryTree($category, false);
            }
            return $tree;
        });
        return response()->json($tree);
    }

    private function buildCategoryTree($category, $includeProducts = false,$productsPage = 3, $pageSize = 20)
    {
        $tree = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'parent_id' => $category->parent_id,
            'image' => $category->picture !== null ? ENV('APP_URL') . $category->picture : null,
            'subcategories' => []
        ];

        if ($includeProducts) {
            $products = $category->products()->paginate($pageSize, ['*'], 'products_page', $productsPage);
            foreach ($products as $product) {
                $tree['products'][] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->image,
                ];
            }
        }

        $subcategories = $category->subcategories()->with('subcategories')->get();

        foreach ($subcategories as $subcategory) {
            $tree['subcategories'][] = $this->buildCategoryTree($subcategory, $includeProducts);
        }
        return $tree;
    }


    public function showCategoryBySlug($slug, Request $request)
    {
        //ini_set('memory_limit', '2048M');
        $categories = Category::all();
        $category = $categories->first(function ($category) use ($slug) {
            return Str::slug($category->name) === $slug;
        });

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $subcategories = $category->subcategories()->with('products')->get();

        $filters = $request->input('filters', []);
        $page = $request->input('page', 1); // Получаем текущую страницу из запроса

        $query = $category->products()->with('characteristics');

        foreach ($filters as $filterName => $filterValues) {
            $query->whereHas('characteristics', function ($query) use ($filterName, $filterValues) {
                $query->where('name', $filterName)
                    ->whereIn('value', $filterValues);
            });
        }

        $products = $query->paginate(20, ['*'], 'page', $page); // Передаем текущую страницу в paginate

        return response()->json([
            'category' => $this->buildCategoryTree($category, true, $page, 20),
            'subcategories' => $subcategories->map(function ($subcategory) use ($page) {
                return $this->buildCategoryTree($subcategory, true, $page, 20);
            }),
            'pagination' => $this->paginate($products),
            'characteristics' => $this->getCharacteristics($products->items()),
        ]);
    }

    private function getCharacteristics($products)
    {
        $characteristics = [];

        foreach ($products as $product) {
            foreach ($product->characteristics as $characteristic) {
                $characteristicName = $characteristic->name;
                $characteristicValue = $characteristic->pivot->value;

                if (!isset($characteristics[$characteristicName])) {
                    $characteristics[$characteristicName] = [];
                }

                if (!in_array($characteristicValue, $characteristics[$characteristicName])) {
                    $characteristics[$characteristicName][] = $characteristicValue;
                }
            }
        }

        $formattedCharacteristics = [];
        foreach ($characteristics as $name => $values) {
            $formattedCharacteristics[] = [
                'name' => $name,
                'values' => $values,
            ];
        }

        return $formattedCharacteristics;
    }
}
