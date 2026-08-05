<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreScoreRequest;
use App\Http\Resources\ScoreResource;
use App\Models\Game;
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

        return new ScoreResource($score);
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
