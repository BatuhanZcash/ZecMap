<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AddressSuggestController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 3) {
            return response()->json(['suggestions' => []]);
        }

        $country = strtoupper(trim((string) $request->query('country', '')));

        $response = Http::timeout(6)
            ->withHeaders([
                'User-Agent' => config('app.name', 'ZecMap').' Address Autocomplete',
            ])
            ->get('https://photon.komoot.io/api/', [
                'q' => $query,
                'limit' => 10,
                'lang' => 'en',
            ]);

        if (! $response->successful()) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = [];

        foreach ($response->json('features', []) as $feature) {
            $properties = $feature['properties'] ?? [];
            $coordinates = $feature['geometry']['coordinates'] ?? null;

            if (! is_array($coordinates) || count($coordinates) < 2) {
                continue;
            }

            $countryCode = strtoupper((string) ($properties['countrycode'] ?? ''));

            if ($country !== '' && $countryCode !== '' && $countryCode !== $country) {
                continue;
            }

            $suggestions[] = [
                'label' => $this->formatLabel($properties),
                'lat' => (float) $coordinates[1],
                'lng' => (float) $coordinates[0],
                'city' => $properties['city']
                    ?? $properties['town']
                    ?? $properties['village']
                    ?? null,
                'country_code' => $countryCode !== '' ? $countryCode : null,
            ];

            if (count($suggestions) >= 8) {
                break;
            }
        }

        return response()->json(['suggestions' => $suggestions]);
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    private function formatLabel(array $properties): string
    {
        $streetLine = trim(implode(' ', array_filter([
            $properties['housenumber'] ?? null,
            $properties['street'] ?? null,
        ])));

        if ($streetLine === '' && ! empty($properties['name'])) {
            $streetLine = (string) $properties['name'];
        }

        $locality = $properties['city']
            ?? $properties['town']
            ?? $properties['village']
            ?? $properties['county']
            ?? null;

        $regionLine = trim(implode(', ', array_filter([
            $properties['postcode'] ?? null,
            $locality,
            $properties['state'] ?? null,
            $properties['country'] ?? null,
        ])));

        $label = trim(implode(', ', array_filter([$streetLine, $regionLine])));

        if ($label !== '') {
            return $label;
        }

        return (string) ($properties['name'] ?? $properties['country'] ?? 'Unknown location');
    }
}
