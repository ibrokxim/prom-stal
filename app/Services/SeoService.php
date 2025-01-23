<?php

namespace App\Services;

class SeoService
{
    public function updateSeo($model, array $seoData)
    {
        $model->seo()->updateOrCreate(
            ['seoable_id' => $model->id],
            [
                'meta_title' => $seoData['meta_title'] ?? null,
                'meta_description' => $seoData['meta_description'] ?? null,
                'meta_keywords' => $seoData['meta_keywords'] ?? null,
                'h1' => $seoData['h1'] ?? null,
                'canonical_url' => $seoData['canonical_url'] ?? null,
                'robots' => $seoData['robots'] ?? null,
            ]
        );
    }

    /**
     * Массовое обновление SEO
     */
    public function bulkUpdateSeo($models, array $seoData)
    {
        foreach ($models as $model) {
            $this->updateSeo($model, $seoData);
        }
    }
}
