<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'parent_id'];

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


    public function toTree()
    {
        $tree = [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
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
                    'image' => $product->image,
                ];
            }
        }
        return $tree;
    }
}
