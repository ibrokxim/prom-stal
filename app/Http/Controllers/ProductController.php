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



    // admin

    public function productIndex()
    {
        $products = Product::query()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function productCreate()
    {
        return view('admin.products.create');
    }

    public function productEdit($id)
    {
        $products = Product::findOrFail($id);
        return view('admin.products.edit', compact('products'));
    }

    public function productDestroy($id)
    {
        $products = Product::findOrFail($id);
        $products->delete();

        return redirect()->route('admin.products.index')->with('success', 'SEO запись удалена!');
    }

    public function productUpdate(Request $request, $id)
    {
        // Находим SEO запись по ID
        $products = Product::findOrFail($id);

        // Валидация данных
        $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'header_seo' => 'nullable|string|max:255',
            'main_seo' => 'nullable|string',
            'code' => 'required|string|max:255|unique:seo,code,' . $products->id,
        ]);

        // Обновление записи
        $products->update($request->all());

        // Перенаправление с сообщением об успехе
        return redirect()->route('admin.products.index')->with('success', 'SEO запись обновлена!');
    }

    public function productStore(Request $request)
    {
        $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'header_seo' => 'nullable|string|max:255',
            'main_seo' => 'nullable|string',
            'code' => 'required|string|unique:seo,code|max:255',
        ]);

        Product::create($request->all());

        return redirect()->route('admin.products.index')->with('success', 'SEO запись добавлена!');
    }

}
