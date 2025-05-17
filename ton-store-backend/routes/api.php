<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\GuestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::post('/auth/telegram', [AuthController::class, 'telegramLogin']);

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);

    // Protected Admin Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/user', [AdminAuthController::class, 'user']);
    });
});

// Guest Routes (Public)
Route::prefix('products')->group(function () {
    Route::get('/stars', [GuestController::class, 'stars']);
    Route::get('/numbers', [GuestController::class, 'numbers']);
    Route::get('/usernames', [GuestController::class, 'usernames']);
    Route::get('/premiums', [GuestController::class, 'premiums']);
    Route::get('/{id}', [GuestController::class, 'show']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Product Routes
    Route::apiResource('products', ProductController::class);

    // Order Routes
    Route::apiResource('orders', OrderController::class);
});

// Legacy Asset routes
Route::get('/assets', [AssetController::class, 'index']);
Route::get('/assets/{asset}', [AssetController::class, 'show']);
Route::post('/assets', [AssetController::class, 'store']);
Route::post('/assets/{asset}/bid', [AssetController::class, 'placeBid']);

// User wallet routes
Route::get('/wallet/{address}/balance', function (Request $request, $address) {
    $tonService = app(\App\Services\TonService::class);
    try {
        $balance = $tonService->getAssetBalance($address);
        return response()->json(['balance' => $balance]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});