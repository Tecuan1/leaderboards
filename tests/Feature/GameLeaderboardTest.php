<?php

use App\Models\Game;
use App\Models\Score;
use App\Models\User;

it('renders the game leaderboard page', function () {
    $game = Game::factory()->create(['slug' => 'snake', 'name' => 'Snake']);

    $response = $this->withoutVite()->get(route('games.leaderboard', $game));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('games/leaderboard')
            ->where('game.name', 'Snake')
            ->where('game.slug', 'snake')
            ->has('leaderboards')
        );
});

it('returns 404 for a nonexistent game leaderboard page', function () {
    $response = $this->get(route('games.leaderboard', 'nonexistent'));

    $response->assertNotFound();
});

it('includes all four period leaderboards', function () {
    $game = Game::factory()->create(['slug' => 'snake']);
    $user = User::factory()->create();
    Score::factory()->create(['user_id' => $user->id, 'game_id' => $game->id, 'score' => 9999]);

    $response = $this->withoutVite()->get(route('games.leaderboard', $game));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('leaderboards.today')
            ->has('leaderboards.weekly')
            ->has('leaderboards.monthly')
            ->has('leaderboards.all')
        );
});

it('shows up to 10 players per period on the leaderboard page', function () {
    $game = Game::factory()->create(['slug' => 'snake']);

    for ($i = 1; $i <= 15; $i++) {
        $user = User::factory()->create();
        Score::factory()->create(['user_id' => $user->id, 'game_id' => $game->id, 'score' => $i * 100]);
    }

    $response = $this->withoutVite()->get(route('games.leaderboard', $game));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('leaderboards.all', 10)
        );
});

it('today leaderboard only includes scores from today', function () {
    $game = Game::factory()->create(['slug' => 'snake']);
    $userToday = User::factory()->create();
    $userOld = User::factory()->create();

    Score::factory()->create(['user_id' => $userToday->id, 'game_id' => $game->id, 'score' => 5000, 'created_at' => now()]);
    Score::factory()->create(['user_id' => $userOld->id, 'game_id' => $game->id, 'score' => 9000, 'created_at' => now()->subDay()]);

    $response = $this->withoutVite()->get(route('games.leaderboard', $game));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('leaderboards.today', 1)
            ->where('leaderboards.today.0.score', 5000)
        );
});

it('weekly leaderboard includes only scores from this calendar week', function () {
    $game = Game::factory()->create(['slug' => 'snake']);
    $userThisWeek = User::factory()->create();
    $userLastWeek = User::factory()->create();

    Score::factory()->create(['user_id' => $userThisWeek->id, 'game_id' => $game->id, 'score' => 4000, 'created_at' => now()->startOfWeek()->addDay()]);
    Score::factory()->create(['user_id' => $userLastWeek->id, 'game_id' => $game->id, 'score' => 8000, 'created_at' => now()->startOfWeek()->subDay()]);

    $response = $this->withoutVite()->get(route('games.leaderboard', $game));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('leaderboards.weekly', 1)
            ->where('leaderboards.weekly.0.score', 4000)
        );
});

it('monthly leaderboard includes only scores from this calendar month', function () {
    $game = Game::factory()->create(['slug' => 'snake']);
    $userThisMonth = User::factory()->create();
    $userLastMonth = User::factory()->create();

    Score::factory()->create(['user_id' => $userThisMonth->id, 'game_id' => $game->id, 'score' => 3000, 'created_at' => now()->startOfMonth()->addDay()]);
    Score::factory()->create(['user_id' => $userLastMonth->id, 'game_id' => $game->id, 'score' => 7000, 'created_at' => now()->startOfMonth()->subDay()]);

    $response = $this->withoutVite()->get(route('games.leaderboard', $game));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('leaderboards.monthly', 1)
            ->where('leaderboards.monthly.0.score', 3000)
        );
});
