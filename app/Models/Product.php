<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = ['name', 'image', 'description',
        'characteristic_1', 'characteristic_2', 'characteristic_3',
        'characteristic_4', 'characteristic_5', 'characteristic_6',
        'characteristic_7', 'characteristic_8', 'characteristic_9',
        'characteristic_10', 'characteristic_11', 'characteristic_12',
        'characteristic_13', 'characteristic_14', 'characteristic_15',
        'characteristic_16', 'characteristic_17', 'characteristic_18',
        'characteristic_19', 'characteristic_20', 'characteristic_21',  ];

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
}
