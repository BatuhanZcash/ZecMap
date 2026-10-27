<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\ZecMoonSpawn;
use App\Services\ZecMoonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZecMoonController extends Controller
{
    public function __construct(private readonly ZecMoonService $zecmoon)
    {
    }

    public function index(): View
    {
        $pins = Business::query()
            ->where('status', Business::STATUS_ACTIVE)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->select(['id', 'lat', 'lng', 'payment_provider'])
            ->get()
            ->map(fn (Business $business) => [
                'lat' => (float) $business->lat,
                'lng' => (float) $business->lng,
                'p' => $business->payment_provider ?? Business::PAYMENT_ZCASH,
            ])
            ->values();

        $shareSeq = null;
        $c = (string) request()->query('c', '');
        if (ctype_digit($c) && (int) $c > 0 && strlen($c) <= 9) {
            $shareSeq = (int) $c;
        }

        return view('zecmoon', [
            'mapPins' => $pins,
            'shareSeq' => $shareSeq,
            'isGuest' => ! auth()->check(),
            'userStats' => auth()->check()
                ? $this->zecmoon->userStats(auth()->user())
                : ['total' => 0, 'week' => 0],
        ]);
    }

    public function state(): JsonResponse
    {
        if (auth()->user()?->is_banned) {
            return response()->json(['active' => false, 'count' => 0, 'spawns' => [], 'weekly_captured' => 0, 'banned' => true]);
        }
        $spawns = $this->zecmoon->activeSpawns();

        return response()->json([
            'active' => $spawns->isNotEmpty(),
            'count' => $spawns->count(),
            'spawns' => $spawns->map(fn ($spawn) => [
                'id' => $spawn->id,
                'seq_no' => $spawn->seq_no,
                'lat' => $spawn->lat,
                'lng' => $spawn->lng,
            ])->values(),
            'weekly_captured' => $this->zecmoon->weeklyCapturedCount(),
        ]);
    }

    public function attempt(Request $request): JsonResponse
    {
        if ($request->user()->is_banned) {
            return response()->json(['ok' => false, 'reason' => 'banned'], 403);
        }
        $validated = $request->validate([
            'spawn_id' => ['required', 'integer'],
        ]);

        $spawn = ZecMoonSpawn::query()->find($validated['spawn_id']);

        if ($spawn === null || ! $spawn->isActive()) {
            return response()->json(['ok' => false, 'reason' => 'taken'], 409);
        }

        $challenge = $this->zecmoon->beginAttempt($request->user(), $spawn);

        return response()->json(['ok' => true] + $challenge);
    }

    public function capture(Request $request): JsonResponse
    {
        if ($request->user()->is_banned) {
            return response()->json(['ok' => false, 'reason' => 'banned'], 403);
        }
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'answer' => ['required', 'integer'],
        ]);

        $result = $this->zecmoon->capture($request->user(), $validated['token'], (int) $validated['answer'], $request->ip());

        if (! $result['ok']) {
            return response()->json(['ok' => false, 'reason' => $result['reason']], 422);
        }

        /** @var ZecMoonSpawn $spawn */
        $spawn = $result['spawn'];
        $stats = $this->zecmoon->userStats($request->user());

        return response()->json([
            'ok' => true,
            'seq_no' => $spawn->capture_no ?? $spawn->seq_no,
            'week_captures' => $stats['week'],
            'total_captures' => $stats['total'],
        ]);
    }

    public function card(string $seq)
    {
        abort_unless(ctype_digit($seq) && (int) $seq > 0 && strlen($seq) <= 9, 404);

        $base = public_path('assets/images/zecmoon-card-base.png');
        $font = public_path('assets/fonts/Poppins-Bold.ttf');

        if (! function_exists('imagecreatefrompng') || ! is_file($base) || ! is_file($font)) {
            return redirect(asset('assets/images/zecmoon-og.png'));
        }

        $img = imagecreatefrompng($base);
        $text = '#'.number_format((int) $seq, 0, '', '');

        // Metni ayrılan alana sığdır (x: 454-1105, sayı bandı y ~300-460)
        $maxWidth = 640;
        $size = 118;
        do {
            $box = imagettfbbox($size, 0, $font, $text);
            $width = abs($box[2] - $box[0]);
            if ($width <= $maxWidth) {
                break;
            }
            $size -= 8;
        } while ($size > 40);

        $gold = imagecolorallocate($img, 244, 183, 40);
        $x = 452;
        $y = 300 + (int) (($size + 118) / 2) + 6; // bandın içinde dikey ortalama

        imagettftext($img, $size, 0, $x, $y, $gold, $font, $text);

        return response()->stream(function () use ($img) {
            imagepng($img);
            imagedestroy($img);
        }, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    public function leaderboard(Request $request): JsonResponse
    {
        $allTime = $request->boolean('all_time');

        return response()->json([
            'rows' => $this->zecmoon->leaderboard($allTime),
            'week_resets_at' => $this->zecmoon->weekStart()->addWeek()->toIso8601String(),
            'prizes' => [25, 15, 10],
        ]);
    }
}
