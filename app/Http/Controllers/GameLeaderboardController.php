<?php

namespace App\Http\Controllers;

use App\Actions\Leaderboard\GetGameLeaderboard;
use App\Models\Game;
use Inertia\Inertia;
use Inertia\Response;

class GameLeaderboardController extends Controller
{
    public function __invoke(Game $game, GetGameLeaderboard $action): Response
    {
        $periods = ['today', 'weekly', 'monthly', 'all'];

        $leaderboards = collect($periods)->mapWithKeys(function (string $period) use ($game, $action) {
            $entries = $action->execute($game, $period, 10)->getCollection()->map(fn ($entry) => [
                'rank' => $entry->rank,
                'user_name' => $entry->user_name,
                'score' => $entry->score,
            ])->values();

            return [$period => $entries];
        });

        return Inertia::render('games/leaderboard', [
            'game' => [
                'id' => $game->id,
                'name' => $game->name,
                'slug' => $game->slug,
            ],
            'leaderboards' => $leaderboards,
        ]);
    }
}
