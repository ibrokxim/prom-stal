<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\PaginateTrait;

class ProductController extends Controller
{
    use PaginateTrait;
    public function searchProduct(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where(function($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%");
        })
            ->select('id', 'name', 'image')
            ->paginate(20);

        $productsData = collect($products->items())->map(function ($product) {
            return [
                'id' => $product['id'],
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'image' => $product['image'],
            ];
        });
        return [
            'data' => $productsData,
            'pagination' => $this->paginate($products),
        ];
    }

    public function showProductBySlug($slug)
    {

        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $characteristics = $product->characteristics->unique('id');
        $categories = $product->categories->pluck('id')->toArray();
        $similarProducts = Product::whereHas('categories', function ($query) use ($categories) {
            $query->whereIn('category_id', $categories);
        })
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->image,
                'description' => $product->description,
                'characteristics' => $characteristics
            ],
            'similar_products' => $similarProducts
        ]);
    }

    public function getAllProducts()
    {
        $products = Product::paginate(20);
        $products->getCollection()->transform(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => Str::slug($product->name),
                'image' => $product->image,
            ];
        });

        return response()->json([
            'products' => $products->items(),
            'pagination' => $this->paginate($products),
        ]);
    }
}
