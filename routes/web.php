<?php

use App\Http\Controllers\GameLeaderboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/games/{game}', GameLeaderboardController::class)->name('games.leaderboard');
