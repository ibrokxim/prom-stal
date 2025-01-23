<?php

namespace App\Models;

use App\Traits\HasSeo;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasSeo;
    protected $fillable = ['name', 'image', 'description', 'slug','meta_title', 'meta_description', 'code'];
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function characteristics()
    {
        return $this->belongsToMany(Characteristic::class, 'product_characteristics')
            ->withPivot('value');
    }
    public function getSlugAttribute()
    {
        return Str::slug($this->name);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }

}
