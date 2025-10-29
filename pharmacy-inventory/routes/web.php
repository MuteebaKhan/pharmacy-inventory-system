<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardProductController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Dashboard product CRUD routes (web)
Route::post('/dashboard/products', [DashboardProductController::class, 'store'])->name('dashboard.products.store');
Route::put('/dashboard/products/{id}', [DashboardProductController::class, 'update'])->name('dashboard.products.update');
Route::delete('/dashboard/products/{id}', [DashboardProductController::class, 'destroy'])->name('dashboard.products.destroy');
