<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MobileWebsiteController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['message' => 'pong']));

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/profile', fn (Request $request) => response()->json(['status' => 'success', 'data' => $request->user()]));

    Route::get('/websites', [WebsiteController::class, 'index']);
    Route::post('/websites', [WebsiteController::class, 'store']);
    Route::get('/websites/{website}', [WebsiteController::class, 'show']);
    Route::put('/websites/{website}', [WebsiteController::class, 'update']);
    Route::delete('/websites/{website}', [WebsiteController::class, 'destroy']);
    Route::put('/websites/{website}/publish', [WebsiteController::class, 'publish']);

    Route::prefix('websites/{website}/media')->group(function () {
        Route::get('/', [MediaController::class, 'index']);
        Route::post('/', [MediaController::class, 'store']);
        Route::delete('/{medium}', [MediaController::class, 'destroy']);
    });

    Route::get('/mobile/website', [MobileWebsiteController::class, 'show']);

    Route::get('/media', fn () => response()->json(['status' => 'success', 'data' => []]));
    Route::get('/themes', fn () => response()->json(['status' => 'success', 'data' => []]));

    Route::prefix('builder')->group(function () {
        Route::get('/{any?}', fn () => response()->json(['status' => 'success', 'message' => 'Builder endpoint active']));
    });
});
