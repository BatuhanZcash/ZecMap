<?php

namespace App\Support;

final class TranslationHelper
{
    /**
     * @param  array<string, mixed>  $array
     * @return array<string, string>
     */
    public static function flatten(array $array, string $prefix = ''): array
    {
        $lines = [];

        foreach ($array as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $lines += self::flatten($value, $fullKey);

                continue;
            }

            if (is_string($value) || is_numeric($value)) {
                $lines[$fullKey] = (string) $value;
            }
        }

        return $lines;
    }
}
