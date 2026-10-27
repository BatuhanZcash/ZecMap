<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Str;

class BusinessJsonImporter
{
    /** @var array<string, true> */
    private array $reservedSlugs = [];

    /**
     * @return array{created: int, skipped: int, failed: int, errors: list<string>}
     */
    public function import(array $items, ?int $importId = null): array
    {
        $created = 0;
        $skipped = 0;
        $failed = 0;
        $errors = [];
        $this->reservedSlugs = [];

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                $failed++;
                $errors[] = 'Satır '.($index + 1).': geçersiz kayıt formatı.';

                continue;
            }

            $mapped = $this->mapItem($item);

            if ($mapped === null) {
                $failed++;
                $errors[] = 'Satır '.($index + 1).': işletme adı zorunludur.';

                continue;
            }

            if ($this->isDuplicate($mapped)) {
                $skipped++;

                continue;
            }

            if ($importId !== null) {
                $mapped['business_json_import_id'] = $importId;
            }

            Business::query()->create($mapped);
            $created++;
        }

        return compact('created', 'skipped', 'failed', 'errors');
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>|null
     */
    private function mapItem(array $item): ?array
    {
        $name = trim((string) ($item['name'] ?? ''));

        if ($name === '') {
            return null;
        }

        $street = trim((string) ($item['address'] ?? ''));
        $city = trim((string) ($item['city'] ?? ''));
        $state = trim((string) ($item['state'] ?? ''));
        $zip = trim((string) ($item['zip'] ?? ''));
        $source = trim((string) ($item['source'] ?? ''));

        $cityLine = trim(implode(', ', array_filter([
            $city,
            trim($state.' '.$zip),
        ])));

        $lat = $this->toFloat($item['latitude'] ?? $item['lat'] ?? null);
        $lng = $this->toFloat($item['longitude'] ?? $item['lng'] ?? null);

        $slug = $this->generateUniqueSlug($name, $street, $lat, $lng);

        $description = $source !== '' ? 'Source: '.$source : null;

        return [
            'slug' => $slug,
            'name' => $name,
            'address' => $street !== '' ? $street : null,
            'city' => $cityLine !== '' ? $cityLine : null,
            'country_code' => $this->resolveCountryCode($state),
            'lat' => $lat,
            'lng' => $lng,
            'payment_provider' => $this->resolvePaymentProvider((string) ($item['category'] ?? '')),
            'description' => $description,
            'status' => Business::STATUS_ACTIVE,
            'verified' => false,
        ];
    }

    /**
     * @param  array<string, mixed>  $mapped
     */
    private function isDuplicate(array $mapped): bool
    {
        $query = Business::query()->where('name', $mapped['name']);

        if ($mapped['lat'] !== null && $mapped['lng'] !== null) {
            $query->where('lat', $mapped['lat'])->where('lng', $mapped['lng']);

            return $query->exists();
        }

        if ($mapped['address'] !== null) {
            $query->where('address', $mapped['address']);
        }

        if ($mapped['city'] !== null) {
            $query->where('city', $mapped['city']);
        }

        return $query->exists();
    }

    private function resolvePaymentProvider(string $category): string
    {
        return match (Str::lower(trim($category))) {
            'zcash', 'zec' => Business::PAYMENT_ZCASH,
            default => Business::PAYMENT_FLEXA,
        };
    }

    private function resolveCountryCode(string $state): ?string
    {
        if (strlen($state) === 2 && ctype_alpha($state)) {
            return 'US';
        }

        return null;
    }

    private function toFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return round((float) $value, 6);
    }

    private function generateUniqueSlug(string $name, string $street, ?float $lat, ?float $lng): string
    {
        $parts = [Str::slug($name)];

        if ($street !== '') {
            $parts[] = Str::slug($street);
        } elseif ($lat !== null && $lng !== null) {
            $parts[] = str_replace('.', '-', (string) $lat.'-'.$lng);
        }

        $base = Str::slug(implode('-', array_filter($parts)));

        if ($base === '') {
            $base = 'business';
        }

        $slug = $base;
        $counter = 2;

        while (
            isset($this->reservedSlugs[$slug])
            || Business::query()->where('slug', $slug)->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        $this->reservedSlugs[$slug] = true;

        return $slug;
    }
}
