<?php

use App\Http\Controllers as CT;
use App\Http\Controllers\Master\CategoryController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\ProductController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;


Route::get('/', [CT\Auth\AuthController::class, 'index'])->name('login');

Route::prefix('auth')->group(function () {
    Route::post('/check_login', [CT\Auth\AuthController::class, 'check_login'])->name('auth.check_login');
});


Route::group(['prefix' => 'dashboard', 'middleware' => 'auth'], function () {
    Route::get('/', [CT\Dashboard\DashboardController::class, 'index'])->name('dashboard');
});

Route::group(['prefix' => 'master', 'middleware' => 'auth'], function () {
    Route::get('/category', [CT\Master\CategoryController::class, 'index'])->name('master.category');
    Route::get('/customer', [CT\Master\CustomerController::class, 'index'])->name('master.customer');
    Route::get('/product', [CT\Master\ProductController::class, 'index'])->name('master.product');
});

Route::group(['prefix' => 'stock', 'middleware' => 'auth'], function () {
    Route::get('/', [CT\Stock\StockController::class, 'index'])->name('stock');
});



Route::group(['prefix' => 'api', 'middleware' => 'auth:sanctum'], function () {
    Route::apiResource('customers', CT\Master\CustomerController::class);
    Route::apiResource('categories', CT\Master\CategoryController::class);
    Route::apiResource('products', CT\Master\ProductController::class);


    Route::get('/customer/data', [CustomerController::class, 'getData'])->name('data.customer');
    Route::get('/category/data', [CategoryController::class, 'getData'])->name('data.category');
    Route::get('/product/data', [ProductController::class, 'getData'])->name('data.product');
    Route::get('/category/all', [CategoryController::class, 'getAll'])->name('data.category.all');
});
