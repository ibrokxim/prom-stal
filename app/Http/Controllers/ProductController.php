<?php

namespace App\Http\Controllers;

use App\Models\Product;
class ProductController extends Controller
{
    public function getProductBySlug($slug)
    {
        $product = Product::where('name', $slug)->first();
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($product);
    }
}
