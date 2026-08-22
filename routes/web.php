<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosMenuController;
use App\Http\Controllers\PosOrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home / POS Barista Order Screen
Route::get('/', [PosMenuController::class, 'index'])->name('home');
Route::get('/pos', [PosMenuController::class, 'index'])->name('pos.index');
Route::get('/pos/menu', [PosMenuController::class, 'index'])->name('pos.menu.index');
Route::post('/pos/orders', [OrderController::class, 'store'])->name('pos.orders.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Reports feature ────────────────────────────────────────────────────────
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
});

// Product catalog routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

// Order routes
Route::resource('orders', OrderController::class)->only(['index', 'show', 'store', 'destroy']);

require __DIR__.'/auth.php';
