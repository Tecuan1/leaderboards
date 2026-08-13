<?php

use App\Models\Game;
use App\Models\Score;
use App\Models\User;

it('renders the home page', function () {
    $response = $this->withoutVite()->get(route('home'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page->component('home'));
});

it('passes games with top players to the home page', function () {
    $game = Game::factory()->create(['name' => 'Snake', 'slug' => 'snake']);
    $user1 = User::factory()->create(['name' => 'Alice']);
    $user2 = User::factory()->create(['name' => 'Bob']);
    $user3 = User::factory()->create(['name' => 'Charlie']);

    Score::factory()->create(['user_id' => $user1->id, 'game_id' => $game->id, 'score' => 1000]);
    Score::factory()->create(['user_id' => $user2->id, 'game_id' => $game->id, 'score' => 3000]);
    Score::factory()->create(['user_id' => $user3->id, 'game_id' => $game->id, 'score' => 2000]);

    $response = $this->withoutVite()->get(route('home'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('home')
            ->has('games', 1)
            ->where('games.0.name', 'Snake')
            ->where('games.0.slug', 'snake')
            ->has('games.0.top_players', 3)
            ->where('games.0.top_players.0.user_name', 'Bob')
            ->where('games.0.top_players.0.score', 3000)
            ->where('games.0.top_players.0.rank', 1)
            ->where('games.0.top_players.1.user_name', 'Charlie')
            ->where('games.0.top_players.2.user_name', 'Alice')
        );
});

it('shows at most 3 top players per game on home page', function () {
    $game = Game::factory()->create(['slug' => 'snake']);

    for ($i = 1; $i <= 5; $i++) {
        $user = User::factory()->create();
        Score::factory()->create(['user_id' => $user->id, 'game_id' => $game->id, 'score' => $i * 1000]);
    }

    $response = $this->withoutVite()->get(route('home'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('games.0.top_players', 3)
        );
});

it('passes an empty top_players list when a game has no scores', function () {
    Game::factory()->create(['slug' => 'tetris']);

    $response = $this->withoutVite()->get(route('home'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('games.0.top_players', 0)
        );
});
