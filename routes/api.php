<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FeedbackController;

Route::post('/feedback', [FeedbackController::class, 'submitForm']);
Route::post('/request', [RequestController::class, 'sendRequestToTelegram']);
Route::post('/cfeedback', [FeedbackController::class, 'submitFormWithComment']);

Route::get('/category', [CategoryController::class, 'getAllCategories']);
Route::get('/category/{slug}', [CategoryController::class, 'showCategoryBySlug']);

Route::get('/products/{slug}', [ProductController::class, 'showProductBySlug']);
Route::get('products', [ProductController::class, 'getAllProducts']);

Route::get('/search', [ProductController::class, 'searchProduct']);


