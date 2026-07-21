<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-mongo', function () {
    try {
       
        DB::connection('mongodb')->getMongoClient()->listDatabases();
        return "Connected to MongoDB successfully!";
    } catch (\Exception $e) {
        return "MongoDB connection error: " . $e->getMessage();
    }
});
