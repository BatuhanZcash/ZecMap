@php
    $meta = seo_meta($seoOverrides ?? []);
@endphp

<meta name="description" content="{{ $meta['description'] }}">
<link rel="canonical" href="{{ $meta['url'] }}">

{{-- Open Graph (Facebook, WhatsApp, LinkedIn, iMessage) --}}
<meta property="og:type" content="{{ $meta['type'] }}">
<meta property="og:url" content="{{ $meta['url'] }}">
<meta property="og:title" content="{{ $meta['title'] }}">
<meta property="og:description" content="{{ $meta['description'] }}">
<meta property="og:site_name" content="{{ $meta['site_name'] }}">
<meta property="og:locale" content="{{ $meta['locale'] }}">
<meta property="og:locale:alternate" content="{{ $meta['alternate_locale'] }}">
@if ($meta['image'])
    <meta property="og:image" content="{{ $meta['image'] }}">
    <meta property="og:image:secure_url" content="{{ $meta['image'] }}">
    <meta property="og:image:width" content="{{ $meta['image_width'] }}">
    <meta property="og:image:height" content="{{ $meta['image_height'] }}">
    <meta property="og:image:alt" content="{{ $meta['title'] }}">
@endif

{{-- Twitter / X --}}
<meta name="twitter:card" content="{{ $meta['twitter_card'] }}">
<meta name="twitter:url" content="{{ $meta['url'] }}">
<meta name="twitter:title" content="{{ $meta['title'] }}">
<meta name="twitter:description" content="{{ $meta['description'] }}">
@if ($meta['image'])
    <meta name="twitter:image" content="{{ $meta['image'] }}">
    <meta name="twitter:image:alt" content="{{ $meta['title'] }}">
@endif
@if ($meta['twitter_site'])
    <meta name="twitter:site" content="{{ $meta['twitter_site'] }}">
@endif
@if ($meta['twitter_creator'])
    <meta name="twitter:creator" content="{{ $meta['twitter_creator'] }}">
@endif
