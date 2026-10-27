<?php

if (! function_exists('versioned_asset')) {
    /**
     * Public asset URL with cache-busting version (file modification time).
     */
    function versioned_asset(string $path): string
    {
        $normalized = ltrim($path, '/');
        $fullPath = public_path($normalized);
        $url = asset($normalized);

        if (is_file($fullPath)) {
            return $url.'?v='.filemtime($fullPath);
        }

        return $url;
    }
}

if (! function_exists('absolute_url')) {
    /**
     * Absolute URL for social crawlers (WhatsApp, Facebook, X, etc.).
     */
    function absolute_url(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return url('/'.ltrim($url, '/'));
    }
}

if (! function_exists('seo_meta')) {
    /**
     * Open Graph / Twitter Card meta payload for the current page.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, string|null>
     */
    function seo_meta(array $overrides = []): array
    {
        $siteName = (string) config('app.name', 'ZecMap');
        $defaultDescription = (string) __('seo.default_description');

        $title = (string) ($overrides['title'] ?? $siteName);
        $description = trim(strip_tags((string) ($overrides['description'] ?? $defaultDescription)));
        $description = \Illuminate\Support\Str::limit($description, 200, '…');

        $image = $overrides['image'] ?? config('seo.og_image');
        $image = absolute_url(is_string($image) ? $image : null);

        $url = absolute_url((string) ($overrides['url'] ?? request()->url()));

        $locale = match (app()->getLocale()) {
            'tr' => 'tr_TR',
            default => 'en_US',
        };

        $alternateLocale = $locale === 'tr_TR' ? 'en_US' : 'tr_TR';

        return [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'url' => $url,
            'type' => (string) ($overrides['type'] ?? 'website'),
            'site_name' => $siteName,
            'locale' => $locale,
            'alternate_locale' => $alternateLocale,
            'twitter_card' => (string) ($overrides['twitter_card'] ?? 'summary_large_image'),
            'twitter_site' => config('seo.twitter_site') ?: null,
            'twitter_creator' => config('seo.twitter_creator') ?: null,
            'image_width' => (string) config('seo.og_image_width', 1200),
            'image_height' => (string) config('seo.og_image_height', 630),
        ];
    }
}
