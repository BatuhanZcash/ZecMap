<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property array{model: \App\Models\Community, scores: array<string, int>, rank: int, period: string} $resource
 */
class CommunityLeaderboardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $community = $this->resource['model'];
        $scores = $this->resource['scores'];
        $period = $this->resource['period'];

        return [
            'id' => $community->id,
            'slug' => $community->slug,
            'name' => $community->name,
            'description' => $community->description,
            'flag_emoji' => $community->flag_emoji,
            'rank' => $this->resource['rank'],
            'period_score' => $scores[$period],
            'week_score' => $scores['weekly'],
            'month_score' => $scores['monthly'],
            'year_score' => $scores['yearly'],
            'total_score' => $scores['all'],
        ];
    }
}
