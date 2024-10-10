<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\FeedbackController;

Route::post('/feedback', [FeedbackController::class, 'submitForm']);
Route::post('/request', [RequestController::class, 'sendRequestToTelegram']);
Route::post('/cfeedback', [FeedbackController::class, 'submitFormWithComment']);

Route::get('/product/{slug}', [ProductController::class, 'getProductBySlug']);
