<?php

use App\Models\Game;
use App\Models\Score;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('submits a score', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);
    $game = Game::factory()->create(['slug' => 'snake']);

    $response = $this->postJson("/api/games/{$game->slug}/scores", [
        'score' => 1500,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.score', 1500)
        ->assertJsonPath('data.game_id', $game->id);

    $this->assertDatabaseHas('scores', [
        'user_id' => $user->id,
        'game_id' => $game->id,
        'score' => 1500,
    ]);
});

it('submits a score with metadata', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);
    $game = Game::factory()->create(['slug' => 'snake']);

    $metadata = ['time_played' => 120, 'level' => 5];

    $response = $this->postJson("/api/games/{$game->slug}/scores", [
        'score' => 2000,
        'metadata' => $metadata,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.metadata.level', 5);

    $this->assertDatabaseHas('scores', [
        'user_id' => $user->id,
        'game_id' => $game->id,
        'score' => 2000,
        'metadata' => json_encode($metadata),
    ]);
});

it('rejects score above max_score', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);
    $game = Game::factory()->create(['slug' => 'snake', 'max_score' => 9999]);

    $response = $this->postJson("/api/games/{$game->slug}/scores", [
        'score' => 10000,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['score']);
});

it('rejects non-positive score', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);
    $game = Game::factory()->create(['slug' => 'snake']);

    $response = $this->postJson("/api/games/{$game->slug}/scores", [
        'score' => 0,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['score']);
});

it('rejects unauthenticated score submission', function () {
    $game = Game::factory()->create(['slug' => 'snake']);

    $response = $this->postJson("/api/games/{$game->slug}/scores", [
        'score' => 1000,
    ]);

    $response->assertUnauthorized();
});

it('rejects score for nonexistent game', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson('/api/games/nonexistent/scores', [
        'score' => 1000,
    ]);

    $response->assertNotFound();
});

it('returns personal best score', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);
    $game = Game::factory()->create(['slug' => 'snake']);

    Score::factory()->create(['user_id' => $user->id, 'game_id' => $game->id, 'score' => 500]);
    Score::factory()->create(['user_id' => $user->id, 'game_id' => $game->id, 'score' => 1200]);
    Score::factory()->create(['user_id' => $user->id, 'game_id' => $game->id, 'score' => 900]);

    $response = $this->getJson("/api/games/{$game->slug}/scores/my-best");

    $response->assertOk()
        ->assertJsonPath('data.score', 1200);
});

it('returns 404 for personal best if no scores', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);
    $game = Game::factory()->create(['slug' => 'snake']);

    $response = $this->getJson("/api/games/{$game->slug}/scores/my-best");

    $response->assertNotFound();
});
