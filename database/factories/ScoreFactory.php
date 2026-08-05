<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Score>
 */
class ScoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'game_id' => Game::factory(),
            'score' => fake()->numberBetween(1, 10000),
            'metadata' => null,
            'created_at' => now(),
        ];
    }
}
