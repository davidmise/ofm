<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TruckController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\SimCardController;
use App\Http\Controllers\Api\GpsDeviceController;
use App\Http\Controllers\Api\CommandController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;

// Public authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('trucks', TruckController::class);
    Route::apiResource('drivers', DriverController::class);
    Route::apiResource('sim-cards', SimCardController::class);
    Route::apiResource('gps-devices', GpsDeviceController::class);
    Route::apiResource('commands', CommandController::class);
    Route::apiResource('sim-assignments', \App\Http\Controllers\Api\SimAssignmentController::class);
    Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
    Route::get('/analytics/dashboard', [\App\Http\Controllers\Api\AnalyticsController::class, 'dashboard']);
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);

    // Public API (Protected by Sanctum for now, could be separate middleware)
    Route::prefix('public')->group(function () {
        Route::get('/trucks', [\App\Http\Controllers\Api\PublicApiController::class, 'getTrucks']);
    });

    // Reports
    Route::get('/reports/trucks', [\App\Http\Controllers\Api\ReportsController::class, 'exportTrucks']);
});
