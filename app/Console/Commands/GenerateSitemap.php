<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\Category;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap';

    public function handle()
    {
        $this->info('Generating sitemap...');

        // Создаем SitemapIndex
        $sitemapIndex = SitemapIndex::create();

        // Генерация sitemap для главной страницы и категорий
        $mainSitemap = Sitemap::create();

        // Главная страница
        $mainSitemap->add(Url::create('/')
            ->setLastModificationDate(now())
            ->setChangeFrequency('daily')
            ->setPriority(1.0));

        // Категории
        $categories = Category::all();
        foreach ($categories as $category) {
            $mainSitemap->add(Url::create(route('category.show', $category->slug))
                ->setLastModificationDate($category->updated_at)
                ->setChangeFrequency('weekly')
                ->setPriority(0.8));
        }

        // Сохраняем файл для главной страницы и категорий
        $mainSitemapPath = public_path('sitemap-main.xml');
        $mainSitemap->writeToFile($mainSitemapPath);
        $sitemapIndex->add("sitemap-main.xml");

        // Генерация sitemap для товаров (пакетами по 1000)
        $products = Product::query();
        $products->chunk(1000, function ($chunk, $page) use ($sitemapIndex) {
            $sitemap = Sitemap::create();

            foreach ($chunk as $product) {
                $sitemap->add(Url::create(route('product.show', $product->slug))
                    ->setLastModificationDate($product->updated_at)
                    ->setChangeFrequency('weekly')
                    ->setPriority(0.6));
            }

            $sitemapPath = public_path("sitemap-products-{$page}.xml");
            $sitemap->writeToFile($sitemapPath);

            // Добавляем файл в SitemapIndex
            $sitemapIndex->add("sitemap-products-{$page}.xml");
        });

        // Сохраняем SitemapIndex
        $sitemapIndexPath = public_path('sitemap-index.xml');
        $sitemapIndex->writeToFile($sitemapIndexPath);

        $this->info('Sitemap generated successfully!');
    }
}
