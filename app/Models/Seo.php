<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Seo extends Model
{
    use HasFactory;

    protected $table = 'seo';
    protected $fillable = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'h1',
        'canonical_url',
        'robots',
        'seoable_id',
        'seoable_type'];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

}

