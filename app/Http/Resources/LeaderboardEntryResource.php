<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'rank' => $this->rank,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user_name,
            ],
            'score' => $this->score,
            'metadata' => is_string($this->metadata) ? json_decode($this->metadata, true) : $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
