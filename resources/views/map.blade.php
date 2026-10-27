<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.theme-init')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} — Map</title>

    @include('layouts.partials.social-meta', [
        'seoOverrides' => [
            'title' => config('app.name', 'ZecMap') . ' — ' . __('nav.map'),
            'description' => __('seo.map'),
        ],
    ])

    @include('layouts.partials.app-styles')
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css">
    <style type="text/css"
        data-vite-dev-id="/Applications/XAMPP/xamppfiles/htdocs/zecmap/apps/web/src/components/map/MapView.css">
        /* ── Map view theme tokens (light = :root, dark = html.dark) ─────────── */

        :root {
            --zm-map-panel-bg: rgba(255, 255, 255, 0.9);
            --zm-map-panel-border: rgba(0, 5, 28, 0.1);
            --zm-map-panel-shadow: rgba(0, 5, 28, 0.15);
            --zm-map-text: #00051c;
            --zm-map-muted: #5f626f;
            --zm-map-input-placeholder: rgba(95, 98, 111, 0.5);
            --zm-map-overlay: rgba(249, 247, 242, 0.6);
            --zm-map-close-bg: rgba(249, 247, 242, 0.4);
            --zm-map-close-hover-bg: rgba(249, 247, 242, 0.65);
            --zm-map-close-border: rgba(0, 5, 28, 0.1);
            --zm-map-placeholder-bg: rgba(243, 184, 38, 0.1);
            --zm-map-secondary-bg: #efede8;
            --zm-map-secondary-hover-bg: rgba(0, 5, 28, 0.06);
            --zm-map-search-clear-bg: rgba(0, 5, 28, 0.05);
            --zm-map-search-clear-hover-bg: rgba(0, 5, 28, 0.1);
            --zm-map-dropdown-bg: rgba(255, 255, 255, 0.95);
            --zm-map-dropdown-border: rgba(0, 5, 28, 0.1);
            --zm-map-dropdown-divider: rgba(0, 5, 28, 0.05);
            --zm-map-dropdown-hover-bg: rgba(0, 5, 28, 0.04);
            --zm-map-focus-ring: rgba(243, 184, 38, 0.1);
            --zm-map-focus-border: rgba(243, 184, 38, 0.4);
        }

        html.dark {
            --zm-map-panel-bg: rgba(7, 16, 49, 0.92);
            --zm-map-panel-border: rgba(255, 255, 255, 0.1);
            --zm-map-panel-shadow: rgba(0, 0, 0, 0.45);
            --zm-map-text: #ffffff;
            --zm-map-muted: #cccdd1;
            --zm-map-input-placeholder: rgba(204, 205, 209, 0.5);
            --zm-map-overlay: rgba(0, 5, 28, 0.6);
            --zm-map-close-bg: rgba(0, 5, 28, 0.4);
            --zm-map-close-hover-bg: rgba(0, 5, 28, 0.6);
            --zm-map-close-border: rgba(255, 255, 255, 0.1);
            --zm-map-placeholder-bg: rgba(243, 184, 38, 0.1);
            --zm-map-secondary-bg: rgba(255, 255, 255, 0.08);
            --zm-map-secondary-hover-bg: rgba(255, 255, 255, 0.1);
            --zm-map-search-clear-bg: rgba(255, 255, 255, 0.05);
            --zm-map-search-clear-hover-bg: rgba(255, 255, 255, 0.1);
            --zm-map-dropdown-bg: rgba(7, 16, 49, 0.95);
            --zm-map-dropdown-border: rgba(255, 255, 255, 0.1);
            --zm-map-dropdown-divider: rgba(255, 255, 255, 0.05);
            --zm-map-dropdown-hover-bg: rgba(255, 255, 255, 0.05);
            --zm-map-focus-ring: rgba(243, 184, 38, 0.1);
            --zm-map-focus-border: rgba(243, 184, 38, 0.4);
        }

        /* ── Root shell ───────────────────────────────────────────────────────── */

        .map-view {
            position: fixed;
            inset: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
        }

        .map-view-canvas {
            width: 100%;
            height: 100%;
        }

        /* ── Map controls ─────────────────────────────────────────────────────── */

        .map-view .maplibregl-ctrl-top-right .maplibregl-ctrl-group:nth-child(1) {
            margin-top: 80px;
        }

        .map-view .maplibregl-ctrl-top-right .maplibregl-ctrl-group:nth-child(2) {
            margin-top: 180px;
        }

        /* ── Cluster marker ───────────────────────────────────────────────────── */

        .map-cluster-marker {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            border: 2px solid rgba(243, 184, 38, 0.6);
            background: #f3b826;
            color: #01061c;
            font-size: 0.75rem;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 10px 15px -3px rgba(243, 184, 38, 0.3);
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }

        .maplibregl-marker {
            z-index: 2;
        }

        .map-cluster-marker:hover {
            transform: scale(1.1);
        }

        /* ── Pin marker ───────────────────────────────────────────────────────── */

        .map-pin-marker {
            width: 28px;
            height: 36px;
            cursor: pointer;
            pointer-events: none;
            filter: drop-shadow(0 10px 8px rgba(0, 0, 0, 0.25));
            transition: transform 0.2s ease;
            -webkit-user-select: none;
            user-select: none;
        }

        .map-pin-marker:hover {
            transform: scale(1.25);
        }

        .map-pin-marker-button {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            min-width: 44px;
            min-height: 44px;
            padding: 0;
            border: none;
            background: transparent;
            cursor: pointer;
            line-height: 0;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }

        .map-pin-marker-button:hover .map-pin-marker {
            transform: scale(1.25);
        }

        /* ── Desktop side panel ───────────────────────────────────────────────── */

        .map-side-panel {
            display: none;
        }

        .map-side-panel.map-side-panel--open {
            display: flex;
            position: fixed;
            z-index: 1001;
            animation: map-slide-in-left 400ms cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        @media (max-width: 767px) {
            .map-side-panel.map-side-panel--open {
                left: 0;
                right: 0;
                bottom: 5rem;
                top: auto;
                width: 100%;
                max-height: min(58dvh, 440px);
                padding: 0 1rem;
            }
        }

        @media (min-width: 768px) {
            .map-side-panel.map-side-panel--open {
                top: 200px;
                left: 0;
                bottom: 0;
                width: 22rem;
                height: fit-content;
                max-height: none;
                padding: 0 10px;
            }
        }

        @keyframes map-slide-in-left {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .map-side-panel-card {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: fit-content;
            overflow: hidden;
            border-radius: 10px;
            background: var(--zm-map-panel-bg);
            border: 1px solid var(--zm-map-panel-border);
            border-left: none;
            box-shadow: 8px 0 32px -8px var(--zm-map-panel-shadow);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transition:
                background-color 0.3s ease,
                border-color 0.3s ease;
        }

        .map-side-panel-media {
            position: relative;
            flex: 0 0 auto;
            height: 9rem;
            max-height: 13rem;
            width: 100%;
            overflow: hidden;
            background: var(--zm-map-placeholder-bg);
        }

        .map-side-panel-image {
            position: absolute;
            inset: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }

        .map-side-panel-image[hidden] {
            display: none !important;
        }

        .map-side-panel-placeholder {
            position: absolute;
            inset: 0;
            z-index: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            background: var(--zm-map-placeholder-bg);
        }

        .map-side-panel-placeholder[hidden] {
            display: none !important;
        }

        .map-side-panel-placeholder-icon {
            color: rgba(243, 184, 38, 0.45);
        }

        .map-side-panel-overlay {
            position: absolute;
            inset: 0;
            z-index: 1;
            background: linear-gradient(to top, var(--zm-map-overlay), transparent 55%);
            pointer-events: none;
        }

        .map-side-panel-close {
            position: absolute;
            z-index: 2;
            top: 0.875rem;
            right: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            padding: 0;
            border: 1px solid var(--zm-map-close-border);
            border-radius: 9999px;
            background: var(--zm-map-close-bg);
            color: var(--zm-map-text);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            cursor: pointer;
            transition:
                background-color 0.2s ease,
                color 0.2s ease;
        }

        .map-side-panel-close:hover {
            background: var(--zm-map-close-hover-bg);
        }

        .map-side-panel-body {
            flex: 1 1 auto;
            min-height: 0;
            padding: 1.25rem;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .map-side-panel-body::-webkit-scrollbar {
            display: none;
        }

        .map-side-panel-category {
            color: #f3b826;
            font-size: 14px;
            font-weight: 400;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 13px;
        }

        .map-side-panel-category::before {
            content: '';
            margin-right: 8px;
            height: 2px;
            width: 24px;
            background-color: #f3b826;
            display: flex;
        }

        .map-side-panel-title {
            font-family: var(--font-headline);
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 0.625rem;
            color: var(--zm-map-text);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .map-side-panel-loading {
            font-size: 0.8125rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            color: var(--zm-map-muted);
        }

        .map-side-panel-card--loading .map-side-panel-quick-actions,
        .map-side-panel-card--loading .map-side-panel-contacts {
            opacity: 0.45;
            pointer-events: none;
        }

        .map-side-panel-description {
            font-size: 0.8125rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            color: var(--zm-map-muted);
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .map-side-panel-contacts {
            margin: 0 0 0.75rem;
            padding: 0 0 0.75rem;
            border-bottom: 1px solid var(--zm-map-panel-border);
        }

        .map-side-panel-contacts-heading {
            font-size: 0.625rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--zm-map-muted);
            margin: 0 0 0.375rem;
        }

        .map-side-panel-contacts-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .map-side-panel-contact-item {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.125rem;
            min-width: 0;
        }

        .map-side-panel-contact-meta {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            color: var(--zm-map-muted);
        }

        .map-side-panel-contact-type {
            font-size: 0.625rem;
            font-weight: 500;
            line-height: 1.2;
        }

        .map-side-panel-contact-value {
            margin: 0;
            padding: 0 0 0 1.125rem;
            font-size: 0.75rem;
            line-height: 1.35;
            color: var(--zm-map-text);
            word-break: break-word;
        }

        .map-side-panel-contact-link {
            display: block;
            color: #f3b826;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .map-side-panel-contact-link:hover {
            color: #ffd54f;
            text-decoration: underline;
        }

        .map-side-panel-quick-actions {
            display: grid;
            grid-template-columns: repeat(var(--quick-cols, 4), minmax(0, 1fr));
            align-items: start;
            gap: 0.5rem 0.25rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--zm-map-panel-border);
        }

        .map-side-panel-quick-actions--cols-3 {
            gap: 0.5rem 0.35rem;
        }

        .map-side-panel-quick-actions--cols-4 .map-side-panel-quick-action {
            gap: 0.4rem;
        }

        .map-side-panel-quick-actions--cols-4 .map-side-panel-quick-action-icon {
            width: 2.375rem;
            height: 2.375rem;
        }

        .map-side-panel-quick-actions--cols-5 {
            gap: 0.35rem 0.125rem;
        }

        .map-side-panel-quick-actions--cols-5 .map-side-panel-quick-action {
            gap: 0.3rem;
        }

        .map-side-panel-quick-actions--cols-5 .map-side-panel-quick-action-icon {
            width: 2rem;
            height: 2rem;
        }

        .map-side-panel-quick-actions--cols-5 .map-side-panel-quick-action-count {
            font-size: 0.75rem;
        }

        .map-side-panel-quick-actions--cols-5 .map-side-panel-quick-action-label {
            font-size: 0.5625rem;
            letter-spacing: -0.02em;
        }

        .map-side-panel-quick-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-self: stretch;
            min-width: 0;
            width: 100%;
            gap: 0.45rem;
            padding: 0;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: opacity 0.2s ease;
        }

        .map-side-panel-quick-action--link {
            text-decoration: none;
            color: inherit;
        }

        .map-side-panel-quick-action[aria-disabled='true'] {
            cursor: default;
            opacity: 0.85;
        }

        .map-side-panel-quick-action--active .map-side-panel-quick-action-icon {
            border-color: rgba(243, 184, 38, 0.65);
            color: #f3b826;
            background: rgba(243, 184, 38, 0.1);
        }

        .map-side-panel-quick-action--active .map-side-panel-quick-action-icon svg {
            fill: #f3b826;
        }

        .map-side-panel-comments-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 0.875rem;
        }

        .map-side-panel-back {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0;
            border: none;
            background: transparent;
            font-size: 0.75rem;
            font-weight: 600;
            color: #f3b826;
            cursor: pointer;
            transition: opacity 0.2s ease;
        }

        .map-side-panel-back:hover {
            opacity: 0.8;
        }

        .map-side-panel-comments-close {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            padding: 0;
            border: 1px solid var(--zm-map-close-border);
            border-radius: 9999px;
            background: var(--zm-map-close-bg);
            color: var(--zm-map-text);
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .map-side-panel-comments-close:hover {
            background: var(--zm-map-close-hover-bg);
        }

        .map-side-panel-comments {
            padding-top: 0.125rem;
        }

        .map-reviews-section {
            padding-top: 0;
        }

        /* Embedded reviews — inherit map panel tokens */
        .map-reviews-section.biz-reviews-section--embedded {
            --zm-biz-text: var(--zm-map-text);
            --zm-biz-subtitle: var(--zm-map-muted);
            --zm-biz-muted: var(--zm-map-muted);
            --zm-biz-panel-bg: var(--zm-map-panel-bg);
            --zm-biz-panel-border: var(--zm-map-panel-border);
            --zm-biz-filter-border: var(--zm-map-panel-border);
            --zm-biz-filter-hover-bg: rgba(255, 255, 255, 0.04);
            --zm-biz-link-bg: rgba(243, 184, 38, 0.1);
            --zm-biz-link-hover-bg: rgba(243, 184, 38, 0.18);
            --zm-biz-review-own-bg: rgba(243, 184, 38, 0.08);
            --zm-biz-skeleton-bg: rgba(255, 255, 255, 0.06);
            --zm-biz-error: #f87171;
        }

        html.dark .map-reviews-section.biz-reviews-section--embedded {
            --zm-biz-link-bg: #040a25;
            --zm-biz-link-hover-bg: #0a1435;
            --zm-biz-review-own-bg: #040a25;
        }

        .map-side-panel-card--comments .map-side-panel-media {
            display: none;
        }

        .map-side-panel-quick-action:not([aria-disabled='true']):hover .map-side-panel-quick-action-icon,
        .map-side-panel-quick-action--link:hover .map-side-panel-quick-action-icon {
            border-color: rgba(243, 184, 38, 0.55);
            color: #f3b826;
        }

        .map-side-panel-quick-action--link:focus-visible {
            outline: 2px solid rgba(243, 184, 38, 0.65);
            outline-offset: 2px;
            border-radius: 0.5rem;
        }

        .map-side-panel-quick-action-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.625rem;
            height: 2.625rem;
            border-radius: 9999px;
            border: 1px solid var(--zm-map-panel-border);
            color: var(--zm-map-text);
            background: transparent;
            transition:
                border-color 0.2s ease,
                color 0.2s ease,
                background-color 0.2s ease;
        }

        .map-side-panel-quick-action-count {
            font-size: 0.875rem;
            font-weight: 700;
            line-height: 1;
        }

        .map-side-panel-quick-action-label {
            font-size: 0.6875rem;
            font-weight: 500;
            color: var(--zm-map-text);
            text-align: center;
            line-height: 1.2;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .map-side-panel-actions {
            display: flex;
            flex-direction: column;
            gap: 0.625rem;
        }

        .map-side-panel-primary-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-family: var(--font-headline);
            font-size: 0.8125rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-align: center;
            text-decoration: none;
            color: #01061c;
            background: linear-gradient(90deg, #f3b826 0%, #f3cc83 100%);
            cursor: pointer;
            transition:
                filter 0.2s ease,
                transform 0.2s ease;
        }

        .map-side-panel-primary-button:hover {
            filter: brightness(1.08);
        }

        .map-side-panel-primary-button:active {
            transform: scale(0.97);
        }

        .map-side-panel-secondary-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-family: var(--font-headline);
            font-size: 0.8125rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-align: center;
            text-decoration: none;
            color: var(--zm-map-text);
            background: var(--zm-map-secondary-bg);
            transition:
                background-color 0.2s ease,
                transform 0.2s ease;
        }

        .map-side-panel-secondary-button:hover {
            background: var(--zm-map-secondary-hover-bg);
        }

        .map-side-panel-secondary-button:active {
            transform: scale(0.97);
        }

        /* ── Search box ───────────────────────────────────────────────────────── */

        .map-search {
            top: 200px;
            left: 0;
            height: fit-content;
            max-height: none;
            display: flex;
            position: fixed;
            z-index: 1001;
            pointer-events: none;
            animation: map-slide-in-left 400ms cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        .map-search-inner {
            position: relative;
            pointer-events: auto;
        }

        @media (min-width: 768px) {


            .map-search--panel-open {
                left: calc(22rem + 1.5rem);
                width: 20rem;
            }
        }

        .map-search-field {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            border-radius: 5px;
            background: var(--zm-map-panel-bg);
            border: 1px solid var(--zm-map-panel-border);
            box-shadow: 0 25px 50px -12px var(--zm-map-panel-shadow);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background-color 0.3s ease;
            width: 352px;
            height: 64px;
        }

        @media (min-width: 768px) {
            .map-search-field {
                padding-block: 1.125rem;
            }
        }

        .map-search-field:focus-within {
            border-color: var(--zm-map-focus-border);
            box-shadow:
                0 25px 50px -12px var(--zm-map-panel-shadow),
                0 0 0 4px var(--zm-map-focus-ring);
        }

        .map-search-icon {
            flex-shrink: 0;
            color: #f3b826;
        }

        .map-search-input {
            flex: 1;
            min-width: 0;
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.9375rem;
            font-weight: 500;
            color: var(--zm-map-text);
        }

        .map-search-input::placeholder {
            color: var(--zm-map-input-placeholder);
        }

        .map-search-clear {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.375rem;
            border: none;
            border-radius: 9999px;
            background: var(--zm-map-search-clear-bg);
            color: var(--zm-map-muted);
            cursor: pointer;
            transition:
                background-color 0.2s ease,
                color 0.2s ease;
        }

        .map-search-clear:hover {
            background: var(--zm-map-search-clear-hover-bg);
            color: var(--zm-map-text);
        }

        .map-search-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            width: 100%;
            max-height: min(360px, 50vh);
            overflow-y: auto;
            border-radius: 1rem;
            background: var(--zm-map-dropdown-bg);
            border: 1px solid var(--zm-map-dropdown-border);
            box-shadow: 0 25px 50px -12px var(--zm-map-panel-shadow);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .map-search-dropdown[hidden],
        .map-search-empty[hidden],
        .map-search-clear[hidden] {
            display: none !important;
        }

        .map-search-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.875rem 1.25rem;
            border: none;
            border-bottom: 1px solid var(--zm-map-dropdown-divider);
            background: transparent;
            text-align: left;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .map-search-option:last-child {
            border-bottom: none;
        }

        .map-search-option:hover {
            background: var(--zm-map-dropdown-hover-bg);
        }

        .map-search-option-icon {
            flex-shrink: 0;
            color: #f3b826;
        }

        .map-search-option-content {
            min-width: 0;
        }

        .map-search-option-name {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--zm-map-text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .map-search-option-address {
            margin-top: 0.125rem;
            font-size: 0.75rem;
            color: var(--zm-map-muted);
            opacity: 0.7;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .map-search-empty {
            position: absolute;
            top: calc(100% + 0.5rem);
            width: 100%;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            background: var(--zm-map-dropdown-bg);
            border: 1px solid var(--zm-map-dropdown-border);
            box-shadow: 0 25px 50px -12px var(--zm-map-panel-shadow);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .map-search-empty-text {
            font-size: 0.9375rem;
            text-align: center;
            color: var(--zm-map-muted);
            opacity: 0.7;
        }

        /* ── Mobile layout (desktop ≥768px unchanged) ───────────────────────── */

        @media (max-width: 767px) {

            .map-view .maplibregl-ctrl-top-right .maplibregl-ctrl-group:nth-child(1),
            .map-view .maplibregl-ctrl-top-right .maplibregl-ctrl-group:nth-child(2) {
                margin-top: 0;
            }

            .map-view .maplibregl-ctrl-bottom-right {
                right: 0.625rem;
                bottom: calc(5.25rem + env(safe-area-inset-bottom, 0px));
            }

            .map-search {
                top: calc(env(safe-area-inset-top, 0px) + 4.75rem);
                left: 50%;
                right: auto;
                bottom: auto;
                width: min(calc(100% - 1.25rem), 420px);
                height: auto;
                max-height: none;
                padding: 0;
                transform: translateX(-50%);
                animation: none;
            }

            .map-search--panel-open {
                display: none;
            }

            .map-search-inner {
                width: 100%;
            }

            .map-search-field {
                width: 100%;
                height: 52px;
                padding: 0.75rem 1rem;
                box-sizing: border-box;
            }

            .map-search-input {
                font-size: 16px;
            }

            .map-search-dropdown,
            .map-search-empty {
                max-height: min(240px, 38vh);
            }

            .map-side-panel.map-side-panel--open {
                left: 0;
                right: 0;
                top: auto;
                bottom: 80px;
                width: 100%;
                max-width: 100%;
                max-height: min(58dvh, 440px);
                padding: 0 0.75rem;
                box-sizing: border-box;
                animation: map-slide-in-up 400ms cubic-bezier(0.34, 1.56, 0.64, 1) both;
                z-index: 1003;
            }

            @keyframes map-slide-in-up {
                from {
                    transform: translateY(100%);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .map-side-panel-card {
                width: 100%;
                max-height: 100%;
                border-radius: 16px 16px 0 0;
                border-left: 1px solid var(--zm-map-panel-border);
                box-shadow: 0 -12px 40px -12px var(--zm-map-panel-shadow);
                display: flex;
                flex-direction: column;
                max-height: 440px;
            }

            .map-side-panel-media {
                height: 5.5rem;
                max-height: 7.5rem;
                flex-shrink: 0;
            }

            .map-side-panel-body {
                flex: 1 1 auto;
                min-height: 0;
                max-height: none;
                padding: 0.875rem 1rem 1rem;
                -webkit-overflow-scrolling: touch;
                overscroll-behavior: contain;
                max-height: calc(100dvh - 200px);
            }

            .map-side-panel-description {
                -webkit-line-clamp: 2;
                line-clamp: 2;
                margin-bottom: 0.75rem;
            }

            .map-side-panel-title {
                font-size: 1.125rem;
            }

            .map-side-panel-quick-actions {
                gap: 0.35rem 0.2rem;
            }

            .map-side-panel-quick-action-icon {
                width: 2.375rem;
                height: 2.375rem;
            }

            .map-side-panel-actions {
                flex-direction: row;
                gap: 0.5rem;
                flex-shrink: 0;
            }

            .map-side-panel-primary-button,
            .map-side-panel-secondary-button {
                flex: 1 1 0;
                width: auto;
                min-width: 0;
                min-height: 44px;
                padding: 0.65rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>

</head>

<body>
    <div id="root">
        <div class="app-shell app-shell--map">
            @include('layouts.header')

            <main class="app-main app-main--map">
                <div class="map-view">
                    <div id="zecmap-map" class="map-canvas" aria-label="Interactive map">

                    </div>
                    <div class="map-search">
                        <div class="map-search-inner">
                            <div class="map-search-field">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-search map-search-icon" aria-hidden="true">
                                    <path d="m21 21-4.34-4.34"></path>
                                    <circle cx="11" cy="11" r="8"></circle>
                                </svg>
                                <input placeholder="Search businesses..." class="map-search-input" type="search"
                                    autocomplete="off" id="map-search-input">
                                <button type="button" class="map-search-clear" id="map-search-clear" aria-label="Clear"
                                    hidden>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"
                                        aria-hidden="true">
                                        <path d="M18 6 6 18"></path>
                                        <path d="m6 6 12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div id="map-search-dropdown" class="map-search-dropdown" hidden></div>
                            <div id="map-search-empty" class="map-search-empty" hidden>
                                <p class="map-search-empty-text">No businesses found.</p>
                            </div>
                        </div>
                    </div>
                    <aside id="map-side-panel" class="map-side-panel" aria-hidden="true" aria-label="Business details">
                        <div class="map-side-panel-card">
                            <div class="map-side-panel-media">
                                <img alt="" class="map-side-panel-image" id="map-side-panel-image" hidden>
                                <div class="map-side-panel-placeholder" id="map-side-panel-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="map-side-panel-placeholder-icon" aria-hidden="true">
                                        <path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z">

                                        </path>
                                        <path d="M3 9l2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9">

                                        </path>
                                        <path d="M12 3v6">

                                        </path>
                                    </svg>
                                </div>
                                <div class="map-side-panel-overlay">

                                </div>
                                <button type="button" class="map-side-panel-close" id="map-side-panel-close"
                                    aria-label="Close">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"
                                        aria-hidden="true">
                                        <path d="M18 6 6 18">

                                        </path>
                                        <path d="m6 6 12 12">

                                        </path>
                                    </svg>
                                </button>
                            </div>
                            <div class="map-side-panel-body">
                                <span class="map-side-panel-category" id="map-side-panel-category">

                                </span>
                                <h2 class="map-side-panel-title" id="map-side-panel-title">

                                </h2>
                                <p class="map-side-panel-loading" id="map-side-panel-loading" hidden>Loading…</p>
                                <p class="map-side-panel-description" id="map-side-panel-description" hidden>

                                </p>
                                <div class="map-side-panel-quick-actions map-side-panel-quick-actions--cols-3"
                                    style="--quick-cols: 3;">
                                    <button type="button" class="map-side-panel-quick-action" id="map-side-panel-save"
                                        aria-disabled="true" aria-pressed="false"
                                        aria-label="{{ __('map.save') }}">
                                        <span class="map-side-panel-quick-action-icon" id="map-side-panel-save-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-bookmark" aria-hidden="true">
                                                <path
                                                    d="M17 3a2 2 0 0 1 2 2v15a1 1 0 0 1-1.496.868l-4.512-2.578a2 2 0 0 0-1.984 0l-4.512 2.578A1 1 0 0 1 5 20V5a2 2 0 0 1 2-2z">
                                                </path>
                                            </svg>
                                        </span>
                                        <span class="map-side-panel-quick-action-label">{{ __('map.save') }}</span>
                                    </button>
                                    <button type="button" class="map-side-panel-quick-action" id="map-side-panel-share"
                                        data-business-share data-share-url="{{ url()->current() }}"
                                        data-share-title="" data-share-text="" aria-disabled="true">
                                        <span class="map-side-panel-quick-action-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-share2 lucide-share-2" aria-hidden="true">
                                                <circle cx="18" cy="5" r="3">

                                                </circle>
                                                <circle cx="6" cy="12" r="3">

                                                </circle>
                                                <circle cx="18" cy="19" r="3">

                                                </circle>
                                                <line x1="8.59" x2="15.42" y1="13.51" y2="17.49">
                                                </line>
                                                <line x1="15.41" x2="8.59" y1="6.51" y2="10.49">
                                                </line>
                                            </svg>
                                        </span>
                                        <span class="map-side-panel-quick-action-label">{{ __('business.share') }}</span>
                                    </button>
                                    <button type="button" class="map-side-panel-quick-action">
                                        <span
                                            class="map-side-panel-quick-action-icon map-side-panel-quick-action-count">0</span>
                                        <span class="map-side-panel-quick-action-label">Comments</span>
                                    </button>
                                </div>
                                <section class="map-side-panel-contacts" aria-label="Contact"
                                    id="map-side-panel-contacts" hidden>
                                    <h3 class="map-side-panel-contacts-heading">Contact</h3>
                                    <ul class="map-side-panel-contacts-list" id="map-side-panel-contacts-list">

                                    </ul>
                                </section>
                                <div class="map-side-panel-actions">
                                    <a href="#" class="map-side-panel-primary-button"
                                        id="map-side-panel-details">View
                                        Details</a>
                                    <a href="#" target="_blank" rel="noopener noreferrer"
                                        class="map-side-panel-secondary-button" id="map-side-panel-directions">Get
                                        Directions</a>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </main>

            @include('layouts.partials.mobile-bottom-nav')
        </div>

        <div class="toast-stack">

        </div>
    </div>

    @include('business-detail.partials.share-sheet')

    <script>
        window.ZECMAP_MAP = {
            styleUrlLight: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
            styleUrlDark: 'https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json',
            pinUrl: @json(asset('assets/images/map-pin.svg')),
            pinColors: {
                zcash: '#FFC109',
                flexa: '#9333EA',
            },
            pinSize: 1.65,
            searchUrl: @json(route('map.search')),
            businessUrlTemplate: @json(route('map.business', ['business' => '__ID__'])),
            center: @json($mapCenter),
            zoom: {{ $mapZoom }},
            pins: @json($mapPins),
            focusBusinessId: @json($focusBusinessId),
        };
        window.ZECMAP_FAVORITES = {
            authenticated: @json(auth()->check()),
            slugsUrl: @json(route('saved.slugs')),
            toggleUrlTemplate: @json(route('saved.toggle', ['business' => '__SLUG__'])),
            loginUrl: @json(route('auth')),
            savedText: @json(__('saved.savedToast')),
            removedText: @json(__('saved.removedToast')),
            loginRequiredText: @json(__('saved.loginRequired')),
        };
    </script>
    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
    <script src="https://unpkg.com/supercluster@8.0.1/dist/supercluster.min.js"></script>
    <script src="{{ asset('assets/js/home.js') }}" defer></script>
    <script src="{{ asset('assets/js/business-favorites.js') }}" defer></script>
    <script src="{{ asset('assets/js/business-share.js') }}" defer></script>
    <script src="{{ asset('assets/js/map.js') }}" defer></script>
</body>

</html>
