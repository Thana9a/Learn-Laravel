<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiscountApiController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public Token Issuing for Mobile/Flutter clients
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Protected API Routes (Sanctum SPA Session Cookie or Mobile Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
        ]);
    })->name('api.user');

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Live Product search/autocomplete
    Route::get('/products/search', [ProductApiController::class, 'search'])->name('api.products.search');

    // Real-time promo code validation
    Route::post('/discounts/validate', [DiscountApiController::class, 'validateCode'])->name('api.discounts.validate');
});
