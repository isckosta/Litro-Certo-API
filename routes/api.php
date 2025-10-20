<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\StationController;
use Illuminate\Support\Facades\Route;

// API V1 Routes
Route::prefix('v1')->group(function () {

    // Public routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    // Admin routes (public for health check)
    Route::prefix('admin')->group(function () {
        Route::get('health', [AdminController::class, 'health']);
    });

    // Stations routes (public)
    Route::prefix('stations')->group(function () {
        Route::get('nearby', [StationController::class, 'nearby']);
        Route::get('{id}', [StationController::class, 'show']);
        Route::get('{id}/prices', [StationController::class, 'prices']);
    });

    // Protected routes (require authentication)
    Route::middleware('auth:api')->group(function () {

        // Auth routes
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
        });

        // Add more protected routes here
    });
});
