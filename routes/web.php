<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/import/stores', [FeedbackController::class, 'importFromCSV']);

//Route::middleware('auth')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('seo', [AdminController::class, 'seoIndex'])->name('admin.seo.index');
        Route::get('seo/create', [AdminController::class, 'seoCreate'])->name('admin.seo.create');
        Route::post('seo', [AdminController::class, 'seoStore'])->name('admin.seo.store');
        Route::get('seo/{id}/edit', [AdminController::class, 'seoEdit'])->name('admin.seo.edit');
        Route::put('seo/{id}', [AdminController::class, 'seoUpdate'])->name('admin.seo.update');
        Route::delete('seo/{id}', [AdminController::class, 'seoDestroy'])->name('admin.seo.destroy');

        Route::get('product', [ProductController::class, 'productIndex'])->name('admin.products.index');
        Route::get('product/create', [ProductController::class, 'productCreate'])->name('admin.products.create');
        Route::post('product', [ProductController::class, 'productStore'])->name('admin.products.store');
        Route::get('product/{id}/edit', [ProductController::class, 'productEdit'])->name('admin.products.edit');
        Route::put('product/{id}', [ProductController::class, 'productUpdate'])->name('admin.products.update');
        Route::delete('product/{id}', [ProductController::class, 'productDestroy'])->name('admin.products.destroy');
        Route::get('product/{slug}', [CategoryController::class, 'show'])->name('product.show');


        Route::get('category', [CategoryController::class, 'categoryIndex'])->name('admin.categories.index');
        Route::get('category/create', [CategoryController::class, 'categoryCreate'])->name('admin.categories.create');
        Route::post('category', [CategoryController::class, 'categoryStore'])->name('admin.categories.store');
        Route::get('category/{id}/edit', [CategoryController::class, 'categoryEdit'])->name('admin.categories.edit');
        Route::put('category/{id}', [CategoryController::class, 'categoryUpdate'])->name('admin.categories.update');
        Route::delete('category/{id}', [CategoryController::class, 'categoryDestroy'])->name('admin.categories.destroy');
        Route::get('category/{slug}', [CategoryController::class, 'show'])->name('category.show');

//    Route::resource('category', CategoryController::class);
        });
//    });


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
