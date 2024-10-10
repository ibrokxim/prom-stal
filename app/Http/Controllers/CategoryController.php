<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function getAllCategories()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['subcategories' => function ($query) {
                $query->with('subcategories.products')->with('products');
            }])
            ->with('products')
            ->get();
        $tree = [];

        foreach ($categories as $category) {
            $tree[] = $category->toTree();
        }

        return response()->json($tree);
    }
}
