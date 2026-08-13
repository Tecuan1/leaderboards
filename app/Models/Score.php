<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $game_id
 * @property int $score
 * @property array|null $metadata
 * @property Carbon|null $created_at
 */
#[Fillable(['user_id', 'game_id', 'score', 'metadata'])]
class Score extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'metadata' => 'array',
        ];
    }

    public static function bestForUserInGame(User|int $user, Game|int $game, ?\DateTimeInterface $since = null): ?int
    {
        $userId = $user instanceof User ? $user->id : $user;
        $gameId = $game instanceof Game ? $game->id : $game;

        return static::query()
            ->where('user_id', $userId)
            ->where('game_id', $gameId)
            ->when($since, fn ($query) => $query->where('created_at', '>=', $since))
            ->max('score');
    }
}
