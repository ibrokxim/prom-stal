<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCharacteristic extends Model
{
    protected $table = 'product_characteristics';
    protected $fillable = ['product_id', 'characteristic_id', 'value'];
    public $timestamps = false;
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function characteristic()
    {
        return $this->belongsTo(Characteristic::class);
    }
}
