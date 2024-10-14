<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function getAllCategories()
    {
        $tree = Cache::remember('all_categories', 60, function () {
            $tree = [];
            $categories = Category::whereNull('parent_id')->lazy();

            foreach ($categories as $category) {
                $tree[] = $this->buildCategoryTree($category);
            }

            return $tree;
        });

        return response()->json($tree);
    }

    private function buildCategoryTree($category, $products  = null)
    {
        $tree = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'parent_id' => $category->parent_id,
            'image' => $category->picture !== null ? ENV('APP_URL') . $category->picture : null,
            'subcategories' => []
        ];

        if ($products !== null) {
            foreach ($products as $product) {
                $tree['products'][] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->image,
                ];
            }
        }

        $subcategories = $category->subcategories()->with(['subcategories' => function ($query) {
            $query->whereHas('products')->with('products');
        }])->get();

        //$subcategories = $category->subcategories()->with('subcategories')->lazy();

        foreach ($subcategories as $subcategory) {
            $tree['subcategories'][] = $this->buildCategoryTree($subcategory);
        }

        return $tree;
    }


    public function showCategoryBySlug($slug)
    {
       $categories = Category::all();
       $category = $categories->first(function ($category) use ($slug) {
           return Str::slug($category->name) === $slug;
       });
           if (!$category) {
              return response()->json(['error' => 'Category not found'], 404);

       }

        $subcategories = Cache::remember('subcategories_by_category_' . $category->id, 60, function () use ($category) {
            return $category->subcategories()->with('products')->get();
        });

        $products = Cache::remember('products_by_category_' . $category->id, 60, function () use ($category) {
            return $category->products()->with('characteristics')->get();
        });

        return response()->json([
            'category' => $this->buildCategoryTree($category, $products),
            'subcategories' => $subcategories->map(function ($subcategory) {
                return $this->buildCategoryTree($subcategory);
            }),
        ]);
    }
}
