<?php

namespace App\Support;

/**
 * Builds ZIP-321 Zcash payment URIs and detects address type.
 * ZIP-321: zcash:<address>?amount=<decimal>&memo=<base64url>
 * Memo is only valid on addresses that carry one (shielded / unified).
 */
class Zip321
{
    public static function addressType(string $address): string
    {
        $a = trim($address);
        if (preg_match('/^(t1|t3)/', $a))        return 'transparent';
        if (str_starts_with($a, 'zs'))           return 'sapling';   // shielded
        if (str_starts_with($a, 'u1'))           return 'unified';
        return 'unknown';
    }

    public static function supportsMemo(string $address): bool
    {
        return in_array(self::addressType($address), ['sapling', 'unified'], true);
    }

    /**
     * Build a ZIP-321 URI.
     * $amountZec: decimal string or null for "any amount".
     * $memoText: plain text; ignored on addresses that cannot carry a memo.
     */
    public static function uri(string $address, ?string $amountZec = null, ?string $memoText = null): string
    {
        $uri = 'zcash:' . trim($address);
        $params = [];

        if ($amountZec !== null && $amountZec !== '') {
            $params[] = 'amount=' . self::formatAmount($amountZec);
        }

        if ($memoText !== null && $memoText !== '' && self::supportsMemo($address)) {
            $params[] = 'memo=' . self::base64url($memoText);
        }

        return $params ? $uri . '?' . implode('&', $params) : $uri;
    }

    public static function base64url(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    // Up to 8 decimals, no thousands separators, trailing zeros trimmed.
    protected static function formatAmount(string $amount): string
    {
        $n = number_format((float) $amount, 8, '.', '');
        return rtrim(rtrim($n, '0'), '.');
    }
}
