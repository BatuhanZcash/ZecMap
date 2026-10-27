<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsDailyStat extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'stat_date',
        'page_views',
        'sessions',
        'business_views',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stat_date' => 'date',
            'page_views' => 'integer',
            'sessions' => 'integer',
            'business_views' => 'integer',
        ];
    }
}
