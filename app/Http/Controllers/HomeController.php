<?php

namespace App\Http\Controllers;

use App\Actions\Leaderboard\GetGameLeaderboard;
use App\Models\Game;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(GetGameLeaderboard $action): Response
    {
        $games = Game::all()->map(function (Game $game) use ($action) {
            $topThree = $action->execute($game, 'all', 3);

            return [
                'id' => $game->id,
                'name' => $game->name,
                'slug' => $game->slug,
                'top_players' => $topThree->getCollection()->map(fn ($entry) => [
                    'rank' => $entry->rank,
                    'user_name' => $entry->user_name,
                    'score' => $entry->score,
                ])->values(),
            ];
        });

        return Inertia::render('home', [
            'games' => $games,
        ]);
    }
}
