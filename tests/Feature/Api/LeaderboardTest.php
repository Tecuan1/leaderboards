<?php

use App\Models\Game;
use App\Models\Score;
use App\Models\User;

it('returns leaderboard sorted by highest score', function () {
    $game = Game::factory()->create(['slug' => 'snake']);

    $user1 = User::factory()->create(['name' => 'Alice']);
    $user2 = User::factory()->create(['name' => 'Bob']);
    $user3 = User::factory()->create(['name' => 'Charlie']);

    Score::factory()->create(['user_id' => $user1->id, 'game_id' => $game->id, 'score' => 1000]);
    Score::factory()->create(['user_id' => $user2->id, 'game_id' => $game->id, 'score' => 3000]);
    Score::factory()->create(['user_id' => $user3->id, 'game_id' => $game->id, 'score' => 2000]);

    $response = $this->getJson("/api/games/{$game->slug}/leaderboard");

    $response->assertOk()
        ->assertJsonPath('data.0.score', 3000)
        ->assertJsonPath('data.0.user.name', 'Bob')
        ->assertJsonPath('data.0.rank', 1)
        ->assertJsonPath('data.1.score', 2000)
        ->assertJsonPath('data.1.user.name', 'Charlie')
        ->assertJsonPath('data.1.rank', 2)
        ->assertJsonPath('data.2.score', 1000)
        ->assertJsonPath('data.2.user.name', 'Alice')
        ->assertJsonPath('data.2.rank', 3);
});

it('shows only best score per user', function () {
    $game = Game::factory()->create(['slug' => 'snake']);
    $user = User::factory()->create(['name' => 'Alice']);

    Score::factory()->create(['user_id' => $user->id, 'game_id' => $game->id, 'score' => 1000]);
    Score::factory()->create(['user_id' => $user->id, 'game_id' => $game->id, 'score' => 3000]); // Best
    Score::factory()->create(['user_id' => $user->id, 'game_id' => $game->id, 'score' => 2000]);

    $response = $this->getJson("/api/games/{$game->slug}/leaderboard");

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.score', 3000);
});

it('filters by weekly period', function () {
    $game = Game::factory()->create(['slug' => 'snake']);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Score::factory()->create([
        'user_id' => $user1->id,
        'game_id' => $game->id,
        'score' => 5000,
        'created_at' => now()->subDays(10),
    ]);

    Score::factory()->create([
        'user_id' => $user2->id,
        'game_id' => $game->id,
        'score' => 3000,
        'created_at' => now()->subHours(2),
    ]);

    $response = $this->getJson("/api/games/{$game->slug}/leaderboard?period=weekly");

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.score', 3000);
});

it('paginates results', function () {
    $game = Game::factory()->create(['slug' => 'snake']);

    for ($i = 0; $i < 25; $i++) {
        $user = User::factory()->create();
        Score::factory()->create(['user_id' => $user->id, 'game_id' => $game->id, 'score' => 1000 + $i]);
    }

    $response = $this->getJson("/api/games/{$game->slug}/leaderboard?per_page=10");

    $response->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.total', 25)
        ->assertJsonPath('meta.per_page', 10);
});

it('caps per_page at 100', function () {
    $game = Game::factory()->create(['slug' => 'snake']);

    $response = $this->getJson("/api/games/{$game->slug}/leaderboard?per_page=150");

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['per_page']);
});

it('includes metadata in leaderboard response', function () {
    $game = Game::factory()->create(['slug' => 'snake']);
    $user = User::factory()->create();
    Score::factory()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'score' => 5000,
        'metadata' => ['level' => 10],
    ]);

    $response = $this->getJson("/api/games/{$game->slug}/leaderboard");

    $response->assertOk()
        ->assertJsonPath('data.0.metadata.level', 10);
});

it('returns 404 for nonexistent game leaderboard', function () {
    $response = $this->getJson('/api/games/nonexistent/leaderboard');
    $response->assertNotFound();
});
