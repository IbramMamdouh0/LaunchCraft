<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'online',
        'message' => 'LaunchCraft Backend API is running successfully',
        'version' => '1.0.0'
    ]);
});

Route::get('/ping', function () {
    return response()->json(['status' => 'ok', 'message' => 'pong']);
});
