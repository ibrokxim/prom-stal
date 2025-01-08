<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    use HasFactory;

    protected $table = 'seo';
    protected $fillable = ['meta_title', 'meta_description', 'header_seo', 'main_seo', 'code']; // Разрешенные для массового заполнения поля
}

