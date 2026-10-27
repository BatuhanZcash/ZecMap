<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZecMoonSpawn extends Model
{
    protected $table = 'zecmoon_spawns';

    protected $fillable = [
        'window_index',
        'slot',
        'seq_no',
        'capture_no',
        'lat',
        'lng',
        'business_id',
        'spawn_at',
        'expires_at',
        'captured_by',
        'captured_at',
        'captured_ip',
    ];

    protected function casts(): array
    {
        return [
            'spawn_at' => 'datetime',
            'expires_at' => 'datetime',
            'captured_at' => 'datetime',
            'lat' => 'float',
            'lng' => 'float',
        ];
    }

    public function capturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captured_by');
    }

    public function isActive(): bool
    {
        // Zamanlar veritabanında UTC olarak saklanır; uygulama saat diliminden
        // (APP_TIMEZONE) bağımsız olarak ham değerlerle UTC karşılaştırması yapılır.
        $nowUtc = \Carbon\CarbonImmutable::now('UTC')->format('Y-m-d H:i:s');

        return $this->captured_by === null
            && (string) $this->getRawOriginal('spawn_at') <= $nowUtc
            && (string) $this->getRawOriginal('expires_at') > $nowUtc;
    }
}
