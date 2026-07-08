<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlayerProfileController;
use Illuminate\Support\Facades\Log;

Route::get('/log-test', function () {
    Log::info('Route reached');
    return 'OK';
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Player Profile Routes
    Route::post('profile', [PlayerProfileController::class, 'create']);
    Route::get('profile', [PlayerProfileController::class, 'show']);
    Route::put('profile', [PlayerProfileController::class, 'update']);
});

// Public player profile (no auth required)
Route::get('players/{id}', [PlayerProfileController::class, 'showPublic']);

