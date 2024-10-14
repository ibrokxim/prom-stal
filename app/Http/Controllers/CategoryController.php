<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function getAllCategories()
    {
        $tree = [];
        $categories = Category::whereNull('parent_id')
            ->with(['subcategories' => function ($query) {
                $query->with('subcategories'); // Загружаем подкатегории первого уровня
            }])
            ->chunk(100, function ($categories) use (&$tree) {
                foreach ($categories as $category) {
                    $tree[] = $this->buildCategoryTree($category);
                }
            });

        return response()->json($tree);
    }

    private function buildCategoryTree($category)
    {
        $tree = [
            'id' => $category->id,
            'parent_id' => $category->parent_id,
            'image' => $category->picture !== null ? ENV('APP_URL') . $category->picture : null,
            'name' => $category->name,
            'slug' => $category->slug,
            'subcategories' => []
        ];

        foreach ($category->subcategories as $subcategory) {
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

        $subcategories = $category->subcategories()->with([
            'products.characteristics'
        ])->get();

        return response()->json([
            'category' => $category->toTree(),
            'subcategories' => $subcategories->map(function ($subcategory) {
            return $subcategory->toTree();
        }),
        ]);
    }
}
