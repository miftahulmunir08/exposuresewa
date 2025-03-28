<?php

use App\Http\Controllers as CT;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Master\CategoryController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Stock\StockController;
use App\Http\Controllers\Transaction\TransactionCartController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Utility\UtilityController;
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

Route::group(['prefix' => 'utiltiy', 'middleware' => 'auth'], function () {
});

Route::group(['prefix' => 'stock', 'middleware' => 'auth'], function () {
    Route::get('/', [CT\Stock\StockController::class, 'index'])->name('stock');
});

Route::group(['prefix' => 'transaction', 'middleware' => 'auth'], function () {
    Route::get('/', [TransactionController::class, 'index'])->name('transaction');
    Route::get('/detail', [TransactionController::class, 'detail'])->name('transaction.detail');
});



Route::group(['prefix' => 'api', 'middleware' => 'auth:sanctum'], function () {
    Route::apiResource('customers', CT\Master\CustomerController::class);
    Route::apiResource('categories', CT\Master\CategoryController::class);
    Route::apiResource('products', CT\Master\ProductController::class);
    Route::apiResource('stocks', CT\Stock\StockController::class);
    Route::apiResource('transactions', CT\Transaction\TransactionController::class);
    Route::apiResource('transactions-cart', CT\Transaction\TransactionCartController::class);


    Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])->name('data.dashboard');
    Route::get('/customer/data', [CustomerController::class, 'getData'])->name('data.customer');
    Route::get('/category/data', [CategoryController::class, 'getData'])->name('data.category');
    Route::get('/product/data', [ProductController::class, 'getData'])->name('data.product');
    Route::get('/stock/data', [StockController::class, 'getData'])->name('data.stock');
    Route::get('/transaction/data', [TransactionController::class, 'getData'])->name('data.transaction');
    Route::get('/transaction-cart/data', [TransactionCartController::class, 'getData'])->name('data.transaction-cart');


    Route::get('/category/all', [CategoryController::class, 'getAll'])->name('data.category.all');
    Route::get('/product/all', [ProductController::class, 'getAll'])->name('data.product.all');
    Route::get('/customer/all', [CustomerController::class, 'getAll'])->name('data.customer.all');
    Route::get('/utility/allstatus', [UtilityController::class, 'getAllStatusStock'])->name('data.utility.all_status');
});
