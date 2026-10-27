<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VisitorCountryResolver
{
    private const INVALID_CODES = ['XX', 'T1', 'A1', 'A2'];

    public function resolve(Request $request): ?string
    {
        $fromHeader = $this->fromHeaders($request);

        if ($fromHeader !== null) {
            return $fromHeader;
        }

        $ip = $this->clientIp($request);

        if ($ip === null) {
            return null;
        }

        if ($this->isPrivateIp($ip)) {
            $fallback = config('analytics.private_ip_country');

            return is_string($fallback) && strlen($fallback) === 2
                ? strtoupper($fallback)
                : null;
        }

        return Cache::remember(
            'analytics:geoip:'.hash('sha256', $ip),
            now()->addDays(7),
            fn () => $this->lookupCountry($ip),
        );
    }

    public function countryName(string $countryCode): string
    {
        $code = strtoupper(trim($countryCode));

        if ($code === '' || in_array($code, self::INVALID_CODES, true)) {
            return $code;
        }

        if (extension_loaded('intl')) {
            $name = \Locale::getDisplayRegion('-'.$code, app()->getLocale());

            if (is_string($name) && $name !== '' && $name !== $code) {
                return $name;
            }
        }

        return $code;
    }

    private function fromHeaders(Request $request): ?string
    {
        $candidates = [
            $request->header('CF-IPCountry'),
            $request->header('X-Country-Code'),
            $request->header('X-AppEngine-Country'),
            $request->server('HTTP_CF_IPCOUNTRY'),
        ];

        foreach ($candidates as $country) {
            if (! is_string($country)) {
                continue;
            }

            $normalized = strtoupper(trim($country));

            if (strlen($normalized) === 2 && ! in_array($normalized, self::INVALID_CODES, true)) {
                return $normalized;
            }
        }

        return null;
    }

    private function clientIp(Request $request): ?string
    {
        $ip = $request->ip();

        return is_string($ip) && $ip !== '' ? $ip : null;
    }

    private function isPrivateIp(string $ip): bool
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        ) === false;
    }

    private function lookupCountry(string $ip): ?string
    {
        $fromDatabase = $this->lookupFromMaxMindDatabase($ip);

        if ($fromDatabase !== null) {
            return $fromDatabase;
        }

        return $this->lookupFromIpApi($ip);
    }

    private function lookupFromMaxMindDatabase(string $ip): ?string
    {
        $databasePath = config('analytics.geoip_database');

        if (! is_string($databasePath) || ! is_readable($databasePath)) {
            return null;
        }

        if (! class_exists(\GeoIp2\Database\Reader::class)) {
            return null;
        }

        try {
            $reader = new \GeoIp2\Database\Reader($databasePath);
            $record = $reader->country($ip);
            $code = strtoupper((string) $record->country->isoCode);

            return strlen($code) === 2 && ! in_array($code, self::INVALID_CODES, true)
                ? $code
                : null;
        } catch (\Throwable $exception) {
            Log::debug('GeoIP database lookup failed.', [
                'ip' => $ip,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function lookupFromIpApi(string $ip): ?string
    {
        try {
            $response = Http::timeout(2)
                ->acceptJson()
                ->get('http://ip-api.com/json/'.$ip, [
                    'fields' => 'status,countryCode',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            if (! is_array($data) || ($data['status'] ?? '') !== 'success') {
                return null;
            }

            $code = strtoupper((string) ($data['countryCode'] ?? ''));

            return strlen($code) === 2 && ! in_array($code, self::INVALID_CODES, true)
                ? $code
                : null;
        } catch (\Throwable $exception) {
            Log::debug('ip-api lookup failed.', [
                'ip' => $ip,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }
}
