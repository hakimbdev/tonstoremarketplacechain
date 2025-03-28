<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;

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

// Asset routes
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