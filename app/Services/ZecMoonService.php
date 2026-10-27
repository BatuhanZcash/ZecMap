<?php

namespace App\Services;

use App\Models\Business;
use App\Models\User;
use App\Models\ZecMoonSpawn;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ZecMoonService
{
    /** Spawn penceresi (dalga süresi): 5 dakika. */
    public const WINDOW_SECONDS = 300;

    /** Her dalgada kaç ZecMoon düşer. */
    public const SLOTS_PER_WINDOW = 5;

    /** Slotlar arası taban aralık (sn) — düşüşler pencereye eşit yayılır. */
    private const SLOT_SPACING = 60;

    /** Slot başına gizli rastgele sapma üst sınırı (sn). */
    private const MAX_SLOT_JITTER = 35;

    /** Matematik sorusuna cevap süresi (sn). */
    public const ANSWER_SECONDS = 5;

    /** İnsan tepki alt sınırı (ms): bu süreden hızlı cevap = bot. */
    public const MIN_REACTION_MS = 900;


    public function now(): CarbonImmutable
    {
        return CarbonImmutable::now('UTC');
    }

    public function windowIndex(?CarbonImmutable $at = null): int
    {
        $at = $at ?? $this->now();

        return intdiv($at->getTimestamp(), self::WINDOW_SECONDS);
    }

    /**
     * Pencere içindeki spawn anı: deterministik ama APP_KEY ile gizli —
     * dışarıdan hesaplanamaz, sunucu yeniden başlasa da değişmez.
     */
    public function spawnOffsetSeconds(int $windowIndex, int $slot = 0): int
    {
        $hash = hash_hmac('sha256', 'zecmoon-window-'.$windowIndex.'-'.$slot, (string) config('app.key'));

        return ($slot * self::SLOT_SPACING)
            + (int) (hexdec(substr($hash, 0, 12)) % (self::MAX_SLOT_JITTER + 1));
    }

    /**
     * Gerekliyse bu pencerenin spawn kaydını üretir (cron gerekmez; ilk istek üretir).
     * Yarış durumunda unique(window_index) tek üreticiyi garanti eder.
     */
    public function ensureSpawn(): void
    {
        $now = $this->now();
        $window = $this->windowIndex($now);
        $windowStart = CarbonImmutable::createFromTimestampUTC($window * self::WINDOW_SECONDS);

        $existingSlots = ZecMoonSpawn::query()
            ->where('window_index', $window)
            ->pluck('slot')
            ->all();

        for ($slot = 0; $slot < self::SLOTS_PER_WINDOW; $slot++) {
            if (in_array($slot, $existingSlots, true)) {
                continue;
            }

            $spawnAt = $windowStart->addSeconds($this->spawnOffsetSeconds($window, $slot));

            if ($now->lt($spawnAt)) {
                continue; // Bu slotun anı henüz gelmedi.
            }

            [$lat, $lng, $businessId] = $this->pickLocation($window, $slot);

            try {
                DB::transaction(function () use ($window, $slot, $lat, $lng, $businessId, $spawnAt, $windowStart) {
                    $nextSeq = (int) ZecMoonSpawn::query()->lockForUpdate()->max('seq_no') + 1;

                    ZecMoonSpawn::query()->create([
                        'window_index' => $window,
                        'slot' => $slot,
                        'seq_no' => $nextSeq,
                        'lat' => $lat,
                        'lng' => $lng,
                        'business_id' => $businessId,
                        'spawn_at' => $spawnAt,
                        'expires_at' => $windowStart->addSeconds(self::WINDOW_SECONDS),
                    ]);
                });
            } catch (QueryException $e) {
                // Eşzamanlı istek bu slotu bizden önce üretti; sorun değil.
            }
        }
    }

    /**
     * Konum: rastgele bir aktif işletmenin çevresi (hafif sapmayla).
     * Seçim pencere indeksine HMAC ile bağlı — tekrar üretilebilir ama tahmin edilemez.
     *
     * @return array{0: float, 1: float, 2: int|null}
     */
    /**
     * Konum: her slot farklı bir dünya bölgesinden rastgele bir işletmenin çevresi.
     * Böylece bir dalganın 5 düşüşü 5 ayrı kıtaya/bölgeye yayılır.
     * Seçim pencere+slot indeksine HMAC ile bağlı — tekrar üretilebilir ama tahmin edilemez.
     *
     * @return array{0: float, 1: float, 2: int|null}
     */
    private function pickLocation(int $windowIndex, int $slot = 0): array
    {
        // [latMin, latMax, lngMin, lngMax] — Kuzey Amerika, Güney Amerika,
        // Avrupa, Afrika/Orta Doğu, Asya-Pasifik
        $regions = [
            [15.0, 72.0, -170.0, -52.0],
            [-56.0, 15.0, -82.0, -34.0],
            [36.0, 71.0, -11.0, 45.0],
            [-35.0, 36.0, -18.0, 62.0],
            [-47.0, 60.0, 62.0, 180.0],
        ];

        $hash = hash_hmac('sha256', 'zecmoon-loc-'.$windowIndex.'-'.$slot, (string) config('app.key'));

        // Bölge rotasyonu: aynı penceredeki 5 slot 5 farklı bölgeye düşer,
        // pencereden pencereye başlangıç bölgesi kayar.
        $region = $regions[($windowIndex + $slot) % count($regions)];

        $query = fn () => Business::query()
            ->where('status', Business::STATUS_ACTIVE)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereBetween('lat', [$region[0], $region[1]])
            ->whereBetween('lng', [$region[2], $region[3]]);

        $count = $query()->count();

        if ($count === 0) {
            // Bu bölgede işletme yoksa küresel havuza düş.
            $query = fn () => Business::query()
                ->where('status', Business::STATUS_ACTIVE)
                ->whereNotNull('lat')
                ->whereNotNull('lng');
            $count = $query()->count();
        }

        if ($count === 0) {
            return [41.015, 28.979, null];
        }

        $offset = (int) (hexdec(substr($hash, 0, 12)) % $count);

        /** @var Business|null $business */
        $business = $query()
            ->orderBy('id')
            ->offset($offset)
            ->first(['id', 'lat', 'lng']);

        if ($business === null) {
            return [41.015, 28.979, null];
        }

        $jitterLat = ((hexdec(substr($hash, 12, 8)) % 2001) - 1000) / 1000 * 0.12;
        $jitterLng = ((hexdec(substr($hash, 20, 8)) % 2001) - 1000) / 1000 * 0.12;

        $lat = max(-84.0, min(84.0, (float) $business->lat + $jitterLat));
        $lng = (float) $business->lng + $jitterLng;
        if ($lng > 180) {
            $lng -= 360;
        } elseif ($lng < -180) {
            $lng += 360;
        }

        return [$lat, $lng, $business->id];
    }

    public function activeSpawns()
    {
        $this->ensureSpawn();
        $now = $this->now();

        return ZecMoonSpawn::query()
            ->whereNull('captured_by')
            ->where('spawn_at', '<=', $now)
            ->where('expires_at', '>', $now)
            ->orderBy('spawn_at')
            ->get();
    }

    public function activeSpawn(): ?ZecMoonSpawn
    {
        return $this->activeSpawns()->first();
    }

    /**
     * Yakalama denemesi başlat: matematik sorusu + imzalı, süreli bilet.
     *
     * @return array{a: int, b: int, token: string, seconds: int}
     */
    public function beginAttempt(User $user, ZecMoonSpawn $spawn): array
    {
        $a = random_int(11, 49);
        $b = random_int(2, 9);

        $token = Crypt::encryptString(json_encode([
            'spawn' => $spawn->id,
            'user' => $user->id,
            'sum' => $a + $b,
            'iat' => (int) round(microtime(true) * 1000),
            'exp' => $this->now()->addSeconds(self::ANSWER_SECONDS + 2)->getTimestamp(),
        ]));

        return ['a' => $a, 'b' => $b, 'token' => $token, 'seconds' => self::ANSWER_SECONDS];
    }

    /**
     * Cevabı doğrula ve ATOMİK olarak tek kazananı yaz.
     *
     * @return array{ok: bool, reason?: string, spawn?: ZecMoonSpawn}
     */
    public function capture(User $user, string $token, int $answer, ?string $ip = null): array
    {
        try {
            $payload = json_decode(Crypt::decryptString($token), true);
        } catch (\Throwable $e) {
            return ['ok' => false, 'reason' => 'invalid'];
        }

        if (! is_array($payload) || (int) ($payload['user'] ?? 0) !== $user->id) {
            return ['ok' => false, 'reason' => 'invalid'];
        }

        if ($this->now()->getTimestamp() > (int) ($payload['exp'] ?? 0)) {
            return ['ok' => false, 'reason' => 'timeout'];
        }

        if ((int) ($payload['sum'] ?? -1) !== $answer) {
            return ['ok' => false, 'reason' => 'wrong'];
        }

        // Bot koruması: insan gözle görüp okuyup yazamayacak kadar hızlı cevap reddedilir.
        $elapsedMs = (int) round(microtime(true) * 1000) - (int) ($payload['iat'] ?? 0);
        if ($elapsedMs < self::MIN_REACTION_MS) {
            return ['ok' => false, 'reason' => 'toofast'];
        }

        $spawnId = (int) ($payload['spawn'] ?? 0);
        $now = $this->now();

        $won = ZecMoonSpawn::query()
            ->whereKey($spawnId)
            ->whereNull('captured_by')
            ->where('expires_at', '>', $now)
            ->update([
                'captured_by' => $user->id,
                'captured_at' => $now,
                'captured_ip' => $ip,
            ]);

        if ($won !== 1) {
            return ['ok' => false, 'reason' => 'taken'];
        }

        // Yakalama sırası: kaçıncı YAKALANAN olduğunu atomik olarak ata.
        for ($try = 0; $try < 3; $try++) {
            try {
                DB::transaction(function () use ($spawnId) {
                    $next = (int) ZecMoonSpawn::query()
                        ->lockForUpdate()
                        ->max('capture_no') + 1;

                    ZecMoonSpawn::query()
                        ->whereKey($spawnId)
                        ->update(['capture_no' => $next]);
                });
                break;
            } catch (QueryException $e) {
                // Eşzamanlı yakalamada unique çakışması — tekrar dene.
            }
        }

        return ['ok' => true, 'spawn' => ZecMoonSpawn::query()->find($spawnId)];
    }

    public function weekStart(): CarbonImmutable
    {
        return $this->now()->startOfWeek(CarbonImmutable::MONDAY);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function leaderboard(bool $allTime = false, int $limit = 25): array
    {
        $query = ZecMoonSpawn::query()
            ->join('users', 'users.id', '=', 'zecmoon_spawns.captured_by')
            ->whereNotNull('captured_by')
            ->where('users.is_banned', 0);

        if (! $allTime) {
            $query->where('captured_at', '>=', $this->weekStart());
        }

        return $query
            ->groupBy('users.id', 'users.username', 'users.name', 'users.avatar_url')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->orderBy(DB::raw('MIN(zecmoon_spawns.captured_at)'))
            ->limit($limit)
            ->get([
                'users.id',
                'users.username',
                'users.name',
                'users.avatar_url',
                DB::raw('COUNT(*) as captures'),
                DB::raw('MIN(zecmoon_spawns.captured_at) as first_capture_at'),
            ])
            ->map(fn ($row, $index) => [
                'rank' => $index + 1,
                'username' => $row->username ?: $row->name,
                'avatar_url' => $row->avatar_url,
                'captures' => (int) $row->captures,
            ])
            ->all();
    }

    public function weeklyCapturedCount(): int
    {
        return ZecMoonSpawn::query()
            ->whereNotNull('captured_by')
            ->where('captured_at', '>=', $this->weekStart())
            ->count();
    }

    public function userStats(User $user): array
    {
        $base = ZecMoonSpawn::query()->where('captured_by', $user->id);

        return [
            'total' => (clone $base)->count(),
            'week' => (clone $base)->where('captured_at', '>=', $this->weekStart())->count(),
        ];
    }
}
