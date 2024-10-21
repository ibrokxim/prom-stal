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
        \Log::info('Searching for product by slug: ' . $slug);

        $product = Product::where('slug', $slug)->first();

        if ($product) {
            \Log::info('Product found: ' . $product->id);
        } else {
            \Log::warning('Product not found for slug: ' . $slug);
            return response()->json(['error' => 'Product not found'], 404);
        }

        $characteristics = $product->characteristics;

        \Log::info('Product characteristics: ' . json_encode($characteristics));

        return response()->json([
            'product' => $product,
            'characteristics' => $characteristics,
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
