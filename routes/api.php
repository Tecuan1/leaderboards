<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\ScoreController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/games/{game}/scores', [ScoreController::class, 'store']);
    Route::get('/games/{game}/scores/my-best', [ScoreController::class, 'myBest']);
});

Route::get('/games/{game}/leaderboard', [LeaderboardController::class, 'show']);
