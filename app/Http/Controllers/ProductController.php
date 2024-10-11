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
        $products = Product::all();

        $product = $products->first(function ($product) use ($slug) {
            return Str::slug($product->name) === $slug;
        });

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $characteristics = $product->characteristics;

        return response()->json([
            'product' => $product,
            'characteristics' => $characteristics,
        ]);
    }
}
