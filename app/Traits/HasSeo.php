<?php

namespace App\Traits;

use App\Models\Seo;
trait HasSeo
{
    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }
}
