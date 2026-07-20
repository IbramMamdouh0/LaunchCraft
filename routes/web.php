<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'LaunchCraft API is up and running',
        'timestamp' => now()->toDateTimeString()
    ]);
});
