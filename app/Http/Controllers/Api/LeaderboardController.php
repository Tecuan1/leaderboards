<?php

namespace App\Http\Controllers\Api;

use App\Actions\Leaderboard\GetGameLeaderboard;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LeaderboardRequest;
use App\Http\Resources\LeaderboardEntryResource;
use App\Models\Game;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LeaderboardController extends Controller
{
    public function show(LeaderboardRequest $request, Game $game, GetGameLeaderboard $action): AnonymousResourceCollection
    {
        $paginator = $action->execute(
            $game,
            $request->input('period', 'all'),
            (int) $request->input('per_page', 20)
        );

        return LeaderboardEntryResource::collection($paginator);
    }
}
