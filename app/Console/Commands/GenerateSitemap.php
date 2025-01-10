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
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        // Создаем основной индекс сайтмапов
        $sitemapIndex = SitemapIndex::create();

        // Генерация основного сайтмапа
        $mainSitemap = Sitemap::create();

        // Добавляем главную страницу
        $mainSitemap->add(
            Url::create('/')
                ->setLastModificationDate(now())
                ->setChangeFrequency('daily')
                ->setPriority(1.0)
        );

        // Добавляем категории
        $categories = Category::all();
        foreach ($categories as $category) {
            $mainSitemap->add(
                Url::create(route('category.show', $category->slug))
                    ->setLastModificationDate($category->updated_at)
                    ->setChangeFrequency('weekly')
                    ->setPriority(0.8)
            );
        }

        // Сохраняем основной сайтмап
        $mainSitemap->writeToFile(public_path('sitemap-main.xml'));
        $sitemapIndex->add(Url::create(url('sitemap-main.xml')));

        // Генерация сайтмапов для товаров
        $this->generateProductSitemaps($sitemapIndex);

        // Сохраняем индексный файл
        $sitemapIndex->writeToFile(public_path('sitemap-index.xml'));

        $this->info('Sitemap generated successfully!');
    }

    /**
     * Генерация сайтмапов для товаров
     */
    private function generateProductSitemaps(SitemapIndex $sitemapIndex)
    {
        $chunkSize = 1000; // Количество товаров в одном файлe
        $fileIndex = 1;

        Product::chunk($chunkSize, function ($products) use (&$fileIndex, $sitemapIndex) {
            $sitemap = Sitemap::create();

            foreach ($products as $product) {
                $sitemap->add(
                    Url::create(route('product.show', $product->slug))
                        ->setLastModificationDate($product->updated_at)
                        ->setChangeFrequency('weekly')
                        ->setPriority(0.6)
                );
            }

            // Сохраняем файл для текущей порции товаров
            $fileName = "sitemap-products-{$fileIndex}.xml";
            $sitemap->writeToFile(public_path($fileName));
            $sitemapIndex->add(Url::create(url($fileName)));

            $this->info("Generated {$fileName}");

            $fileIndex++;
        });
    }
}
