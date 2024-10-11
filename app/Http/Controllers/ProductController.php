<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use PaginateTrait;
    public function getProductBySlug($slug)
    {
        $product = Product::where('name', $slug)->first();
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

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
                'slug' => Str::slug('name'),
                'image' => $product['image'],
            ];
        });
        return [
            'data' => $productsData,
            'pagination' => $this->paginate($products),
        ];

    }
}
