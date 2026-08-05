<?php

namespace App\Actions\Leaderboard;

use App\Models\Game;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GetGameLeaderboard
{
    public function execute(Game $game, string $period, int $perPage): LengthAwarePaginator
    {
        $query = DB::table('scores')
            ->select('user_id', DB::raw('MAX(score) as score'))
            ->where('game_id', $game->id)
            ->groupBy('user_id');

        if ($period === 'weekly') {
            $query->where('created_at', '>=', now()->subDays(7));
        }

        // Subquery to get the latest metadata and created_at for the max score
        $leaderboardQuery = DB::table('scores as s1')
            ->joinSub($query, 'best_scores', function ($join) {
                $join->on('s1.user_id', '=', 'best_scores.user_id')
                    ->on('s1.score', '=', 'best_scores.score');
            })
            ->join('users', 's1.user_id', '=', 'users.id')
            ->select('s1.user_id', 'users.name as user_name', 's1.score', 's1.metadata', 's1.created_at')
            ->where('s1.game_id', $game->id);

        if ($period === 'weekly') {
            $leaderboardQuery->where('s1.created_at', '>=', now()->subDays(7));
        }

        $finalQuery = DB::table(DB::raw("({$leaderboardQuery->toSql()}) as lb"))
            ->mergeBindings($leaderboardQuery)
            ->select('lb.user_id', 'lb.user_name', 'lb.score', DB::raw('MAX(lb.metadata) as metadata'), DB::raw('MAX(lb.created_at) as created_at'))
            ->groupBy('lb.user_id', 'lb.user_name', 'lb.score')
            ->orderByDesc('lb.score');

        $paginator = $finalQuery->paginate($perPage);

        $startingRank = ($paginator->currentPage() - 1) * $paginator->perPage() + 1;

        $paginator->getCollection()->transform(function ($item, $key) use (&$startingRank) {
            $item->rank = $startingRank++;

            return $item;
        });

        return $paginator;
    }
}
