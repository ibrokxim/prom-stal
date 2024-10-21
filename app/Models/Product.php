<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = ['name', 'image', 'description', 'slug',];

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
