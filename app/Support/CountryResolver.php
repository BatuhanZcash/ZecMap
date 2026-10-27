<?php

namespace App\Support;

/**
 * Resolves a latitude/longitude to an ISO alpha-2 country code using the
 * bundled country boundaries (Natural Earth 110m). No network calls.
 */
class CountryResolver
{
    /** @var array<int, array{cc: string, rings: array<int, array<int, array{0: float, 1: float}>>}>|null */
    private ?array $boundaries = null;

    /** @var array<string, array{name: string, flag: string, lat: float, lng: float}>|null */
    private ?array $meta = null;

    public function meta(): array
    {
        if ($this->meta === null) {
            $path = public_path('assets/vendor/country-meta.json');
            $this->meta = is_file($path)
                ? (json_decode((string) file_get_contents($path), true) ?: [])
                : [];
        }

        return $this->meta;
    }

    private function boundaries(): array
    {
        if ($this->boundaries === null) {
            $path = public_path('assets/vendor/country-boundaries.json');
            $this->boundaries = is_file($path)
                ? (json_decode((string) file_get_contents($path), true) ?: [])
                : [];
        }

        return $this->boundaries;
    }

    /**
     * Point in country via ray casting. Falls back to the nearest country
     * centroid within ~4 degrees for coastal/island points the low-res
     * polygons miss. Returns null when nothing is close enough.
     */
    public function resolve(?float $lat, ?float $lng): ?string
    {
        if ($lat === null || $lng === null) {
            return null;
        }

        foreach ($this->boundaries() as $country) {
            foreach ($country['rings'] as $ring) {
                if ($this->pointInRing($lng, $lat, $ring)) {
                    return $country['cc'];
                }
            }
        }

        // Coastal / small-island fallback: assign to the country whose actual
        // border is closest, within ~0.6 degrees. Uses distance to the polygon
        // edges (not the centroid), so a seaside city goes to its own country,
        // never to a nearer neighbour's centroid.
        $cosLat = cos(deg2rad($lat));
        $best = null;
        $bestDist = 0.6; // degrees
        foreach ($this->boundaries() as $country) {
            foreach ($country['rings'] as $ring) {
                $d = $this->pointToRingDistance($lng, $lat, $ring, $cosLat, $bestDist);
                if ($d < $bestDist) {
                    $bestDist = $d;
                    $best = $country['cc'];
                }
            }
        }

        return $best;
    }

    /**
     * Minimum distance (in degrees, lng scaled by cos(lat)) from a point to a
     * ring's edges. Skips rings whose bounding box is already farther than the
     * current best, to stay fast.
     *
     * @param  array<int, array{0: float, 1: float}>  $ring
     */
    private function pointToRingDistance(float $lng, float $lat, array $ring, float $cosLat, float $cap): float
    {
        $min = INF;
        $n = count($ring);
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $ax = ($ring[$j][0] - $lng) * $cosLat;
            $ay = $ring[$j][1] - $lat;
            $bx = ($ring[$i][0] - $lng) * $cosLat;
            $by = $ring[$i][1] - $lat;
            // distance from origin (the point) to segment a-b
            $dx = $bx - $ax;
            $dy = $by - $ay;
            $len2 = $dx * $dx + $dy * $dy;
            if ($len2 <= 0.0) {
                $d = $ax * $ax + $ay * $ay;
            } else {
                $t = -($ax * $dx + $ay * $dy) / $len2;
                $t = max(0.0, min(1.0, $t));
                $px = $ax + $t * $dx;
                $py = $ay + $t * $dy;
                $d = $px * $px + $py * $py;
            }
            if ($d < $min) {
                $min = $d;
            }
        }

        return sqrt($min);
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $ring
     */
    private function pointInRing(float $lng, float $lat, array $ring): bool
    {
        $inside = false;
        $n = count($ring);
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $ring[$i][0];
            $yi = $ring[$i][1];
            $xj = $ring[$j][0];
            $yj = $ring[$j][1];
            if ((($yi > $lat) !== ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi)) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }
}
