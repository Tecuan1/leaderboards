<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        Game::firstOrCreate(
            ['slug' => 'snake'],
            ['name' => 'Snake', 'max_score' => null]
        );
    }
}
