<?php

namespace App\Models;

use App\Traits\HasSeo;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasSeo;
    protected $fillable = ['name', 'parent_id','meta_title', 'meta_description', 'code'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    public function getSlugAttribute()
    {
        return Str::slug($this->name);
    }

    public function toTree()
    {
        $tree = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'image' => $this->picture !== null ? ENV('APP_URL') . $this->picture : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'subcategories' => [],
            'products' => [],
        ];


        if ($this->relationLoaded('subcategories')) {
            foreach ($this->subcategories as $subcategory) {
                $tree['subcategories'][] = $subcategory->toTree();
            }
        }

        if ($this->relationLoaded('products')) {
            foreach ($this->products as $product) {
                $tree['products'][] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => Str::slug($product->name),
                    'image' => $product->image,
                    'characteristics' => $product->characteristics->map(function ($char) {
                        return [
                            'name' => $char->name,
                            'value' => $char->pivot->value,
                        ];
                    }),
                ];
            }
        }
        return $tree;
    }
}
