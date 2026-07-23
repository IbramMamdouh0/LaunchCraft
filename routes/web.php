<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['status' => 'success', 'message' => 'LaunchCraft API is running.']));
