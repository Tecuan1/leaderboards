<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'name' => fake()->word(),
            'max_score' => null,
        ];
    }

    public function withMaxScore(int $score): static
    {
        return $this->state(fn (array $attributes) => [
            'max_score' => $score,
        ]);
    }
}
