<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MobileWebsiteController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

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
});
