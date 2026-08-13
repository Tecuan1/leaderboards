<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreScoreRequest;
use App\Http\Resources\ScoreResource;
use App\Models\Game;
use App\Models\Score;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function store(StoreScoreRequest $request, Game $game): ScoreResource
    {
        $score = $game->scores()->create([
            'user_id' => $request->user()->id,
            'score' => $request->score,
            'metadata' => $request->metadata,
        ]);

        return (new ScoreResource($score))->additional([
            'best_weekly' => Score::bestForUserInGame($request->user(), $game, now()->startOfWeek()),
            'best_monthly' => Score::bestForUserInGame($request->user(), $game, now()->startOfMonth()),
            'best_ever' => Score::bestForUserInGame($request->user(), $game),
        ]);
    }

    public function myBest(Request $request, Game $game): ScoreResource|JsonResponse
    {
        $bestScore = $game->scores()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('score')
            ->first();

        if (! $bestScore) {
            return response()->json(['message' => 'No scores found.'], 404);
        }

        return new ScoreResource($bestScore);
    }
}
