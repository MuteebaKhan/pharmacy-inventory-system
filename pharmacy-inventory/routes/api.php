<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Category CRUD routes
Route::apiResource('categories', CategoryController::class);

// Medicine CRUD routes
Route::apiResource('medicines', MedicineController::class);

// Medicine statistics route
Route::get('/medicines-statistics', [MedicineController::class, 'statistics']);

// Product CRUD routes (protected by Sanctum authentication)
// Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::get('/products-statistics', [ProductController::class, 'statistics']);
    Route::patch('/products/{id}/quantity', [ProductController::class, 'updateQuantity']);

    Route::get('/low-stock', [ProductController::class, 'lowStock']);
// });

// Sale CRUD routes
Route::apiResource('sales', SaleController::class);

// Purchase CRUD routes
Route::apiResource('purchases', PurchaseController::class);
