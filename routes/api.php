<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\PlayerProfileController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\LocationController;

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

    // Team management
    Route::apiResource('teams', TeamController::class);
    Route::get('teams/{team}/members', [TeamController::class, 'members']);
    Route::post('teams/{team}/members', [TeamController::class, 'addMember']);
    Route::delete('teams/{team}/members/{member}', [TeamController::class, 'removeMember']);

    // Match management
    Route::get('matches', [MatchController::class, 'index']);
    Route::post('matches', [MatchController::class, 'store']);
    Route::get('matches/{match}', [MatchController::class, 'show']);
    Route::post('matches/{match}/join', [MatchController::class, 'join']);
    Route::post('matches/{match}/leave', [MatchController::class, 'leave']);
    // Locations (create requires auth)
    Route::post('locations', [LocationController::class, 'store']);
});

// Public player profile (no auth required)
Route::get('players/{id}', [PlayerProfileController::class, 'showPublic']);

// Public locations search
Route::get('locations', [LocationController::class, 'index']);

