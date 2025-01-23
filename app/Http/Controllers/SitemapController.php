<?php

namespace App\Http\Controllers;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\Category;

class SitemapController extends Controller
{
    private $mainDomain = 'https://apromstal.kz';

    /**
     * Генерация Sitemap Index.
     */
    public function index()
    {
        $sitemapIndex = SitemapIndex::create()
            ->add(route('sitemap.categories'))
            ->add(route('sitemap.products', ['page' => 0]))
            ->add(route('sitemap.products', ['page' => 1]))
            ->add(route('sitemap.products', ['page' => 2]));

        return $sitemapIndex->toResponse(request());
    }

    /**
     * Генерация sitemap для категорий.
     */
    public function categories()
    {
        $sitemap = Sitemap::create();

        // Добавляем категории
        $categories = Category::all();
        foreach ($categories as $category) {
            $sitemap->add(Url::create($this->mainDomain . route('category.show', $category->slug, false))
                ->setLastModificationDate($category->updated_at)
                ->setChangeFrequency('weekly')
                ->setPriority(0.8));
        }

        return $sitemap->toResponse(request());
    }

    /**
     * Генерация sitemap для товаров с пагинацией.
     */
    public function products(int $page)
    {
        $sitemap = Sitemap::create();

        // Пагинация товаров
        $productsPerPage = 100; // Количество товаров на страницу
        $products = Product::skip($page * $productsPerPage)
            ->take($productsPerPage)
            ->get();

        // Добавляем товары
        foreach ($products as $product) {
            $sitemap->add(Url::create($this->mainDomain . route('product.show', $product->slug, false))
                ->setLastModificationDate($product->updated_at)
                ->setChangeFrequency('weekly')
                ->setPriority(0.6));
        }

        return $sitemap->toResponse(request());
    }
}
