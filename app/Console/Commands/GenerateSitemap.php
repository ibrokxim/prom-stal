<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
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
    protected $description = 'Generate and save sitemap.xml file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // Главная страница
        $sitemap->add(Url::create('/')
            ->setLastModificationDate(now())
            ->setChangeFrequency('daily')
            ->setPriority(1.0));

        // Категории
        $categories = Category::all();
        foreach ($categories as $category) {
            $sitemap->add(Url::create(url('/category/' . $category->slug))
                ->setLastModificationDate($category->updated_at)
                ->setChangeFrequency('weekly')
                ->setPriority(0.8));

        }

        // Товары
        $products = Product::all();
        foreach ($products as $product) {
            $sitemap->add(Url::create(url('/product/' . $product->slug))
                ->setLastModificationDate($product->updated_at)
                ->setChangeFrequency('weekly')
                ->setPriority(0.6));

        }

        // Сохранение в файл public/sitemap.xml
        $sitemapPath = public_path('sitemap.xml');
        $sitemap->writeToFile($sitemapPath);

        $this->info("Sitemap generated successfully at: {$sitemapPath}");
    }
}
