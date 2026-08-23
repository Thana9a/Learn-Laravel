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

// Home / POS Barista Order Screen - requires order.create permission
Route::middleware(['auth', 'permission:order.create'])->group(function () {
    Route::get('/', [PosMenuController::class, 'index'])->name('home');
    Route::get('/pos', [PosMenuController::class, 'index'])->name('pos.index');
    Route::get('/pos/menu', [PosMenuController::class, 'index'])->name('pos.menu.index');
    Route::post('/pos/orders', [OrderController::class, 'store'])->name('pos.orders.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Reports feature ────────────────────────────────────────────────────────
    Route::middleware('permission:report.view')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    });
    
    Route::post('/reports', [ReportController::class, 'store'])
        ->middleware('permission:report.export')
        ->name('reports.store');

    // ── Staff/User Management ──────────────────────────────────────────────────
    // TODO: Add user management routes when implemented
    // Route::middleware('permission:user.manage')->group(function () {
    //     Route::resource('staff', StaffController::class);
    // });

    // ── Settings ───────────────────────────────────────────────────────────────
    // TODO: Add settings routes when implemented
    // Route::middleware('permission:settings.manage')->group(function () {
    //     Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    //     Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    // });
});

// Product catalog routes - protected by permissions
Route::middleware(['auth', 'permission:product.view'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
});

Route::middleware(['auth', 'permission:product.create'])->group(function () {
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
});

Route::middleware(['auth', 'permission:product.update'])->group(function () {
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
});

Route::middleware(['auth', 'permission:product.delete'])->group(function () {
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

// Order routes - protected by permissions
Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])
        ->middleware('permission:order.view')
        ->name('orders.index');
    
    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->middleware('permission:order.view')
        ->name('orders.show');
    
    Route::post('/orders', [OrderController::class, 'store'])
        ->middleware('permission:order.create')
        ->name('orders.store');
    
    // Order void/refund endpoints (for future implementation)
    // Route::post('/orders/{order}/void', [OrderController::class, 'void'])
    //     ->middleware('permission:order.void')
    //     ->name('orders.void');
    //
    // Route::post('/orders/{order}/refund', [OrderController::class, 'refund'])
    //     ->middleware('permission:order.refund')
    //     ->name('orders.refund');
    
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])
        ->middleware('permission:order.void')
        ->name('orders.destroy');
});

require __DIR__.'/auth.php';
