<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\MobileWebsiteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json([
    'status' => 'success',
    'message' => 'Pong! Server is active.',
]));

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

    Route::prefix('mobile')->group(function () {
        Route::apiResource('projects', ProjectController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('menu-items', MenuItemController::class);

        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{id}', [OrderController::class, 'show']);
        Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus']);
    });
});
