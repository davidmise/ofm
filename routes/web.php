<?php

use Illuminate\Support\Facades\Route;

// Catch-all route for Vue Router SPA
// This ensures that refreshing on any Vue route works correctly
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
