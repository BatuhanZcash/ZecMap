<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsDimensionStat extends Model
{
    public const DIMENSION_DEVICE = 'device';

    public const DIMENSION_COUNTRY = 'country';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'stat_date',
        'dimension',
        'dimension_key',
        'hits',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stat_date' => 'date',
            'hits' => 'integer',
        ];
    }
}
