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
                $query->with('subcategories.products')->with('products');
            }])
            ->with('products')
            ->chunk(100, function ($categories) use (&$tree) {
                foreach ($categories as $category) {
                    $tree[] = $category->toTree();
                }
            });
        return response()->json($tree);
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
