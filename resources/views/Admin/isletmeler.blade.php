<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — İşletmeler</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    <style type="text/css">
        :root {
            color-scheme: dark;
            --bg: #0b0c14;
            --panel: #141529;
            --border: rgba(255, 255, 255, 0.08);
            --text: #e5e7eb;
            --muted: #9ca3af;
            --accent: #FFC107;
            --danger: #f87171;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
        }

        #root {
            min-height: 100vh;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .admin-root {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-top {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            background: rgba(20, 21, 41, 0.95);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 10;
            flex-wrap: wrap;
        }

        .brand {
            font-weight: 700;
            letter-spacing: 0.04em;
            flex-shrink: 0;
        }

        /* Nav: yatay scroll on small screens, flex-wrap on wider */
        .admin-nav {
            display: flex;
            gap: 0.25rem;
            flex: 1;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            margin: 0 -0.5rem;
            padding: 0 0.5rem;
        }

        .admin-nav::-webkit-scrollbar {
            display: none;
        }

        .nav-link {
            padding: 0.6rem 0.85rem;
            border-radius: 0.5rem;
            color: var(--muted);
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            font-size: 0.92rem;
        }

        .nav-link.active {
            color: var(--accent);
            background: rgba(243, 183, 36, 0.12);
        }

        .admin-main {
            flex: 1;
            padding: 1rem;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .admin-top {
                padding: 0.75rem 1.25rem;
                gap: 1rem;
            }

            .admin-main {
                padding: 1.5rem;
            }
        }

        .admin-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            gap: 1rem;
        }

        .admin-shell.narrow {
            max-width: 420px;
            margin: 0 auto;
        }

        .stack {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .gap-lg {
            gap: 1.25rem;
        }

        .row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1rem;
        }

        @media (min-width: 768px) {
            .card {
                padding: 1.25rem 1.5rem;
            }
        }

        .chart-card h2 {
            margin-top: 0;
        }

        .stat {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.25rem 0 0;
        }

        .muted {
            color: var(--muted);
            margin: 0;
        }

        .error {
            color: var(--danger);
            margin: 0;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            font-size: 0.9rem;
        }

        .field input,
        .field textarea,
        .input,
        .select {
            border-radius: 0.65rem;
            border: 1px solid var(--border);
            background: #0f1018;
            color: var(--text);
            padding: 0.65rem 0.75rem;
            font: inherit;
        }

        .btn {
            border-radius: 0.65rem;
            border: 1px solid var(--border);
            background: #1b1c2a;
            color: var(--text);
            padding: 0.55rem 0.9rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn.primary {
            background: linear-gradient(135deg, #FFC107, #FF9800);
            color: #111827;
            border: none;
        }

        .btn.ghost {
            background: transparent;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Tablo container — responsive scroll. Bütün <table> elemanlarını otomatik
   * sarmaladığında soldan/sağdan kayma şipşak kayıyor; mobil ekranlarda
   * tablo daralmıyor, scroll-x veriyor. */
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        @media (min-width: 768px) {
            .table {
                display: table;
                white-space: normal;
            }
        }

        .table th,
        .table td {
            text-align: left;
            padding: 0.65rem 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            color: var(--muted);
            font-weight: 600;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* Buttons: touch-friendly heights */
        .btn {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }


        h1,
        h2 {
            margin: 0 0 0.5rem;
        }

        h1 {
            font-size: 1.5rem;
        }

        h2 {
            font-size: 1.1rem;
        }

        .business-drawer {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 40;
        }

        .business-drawer.is-open {
            display: flex;
        }

        .business-drawer-backdrop {
            flex: 1;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            border: none;
            cursor: pointer;
        }

        .business-drawer-panel {
            width: 100%;
            max-width: 540px;
            background: var(--bg);
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            box-shadow: rgba(0, 0, 0, 0.6) -30px 0 60px;
        }

        .form-control {
            width: 100%;
            margin-top: 4px;
            background: rgb(15, 16, 24);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 0.55rem 0.75rem;
            color: var(--text);
            font-size: 0.9rem;
            font: inherit;
        }

        textarea.form-control {
            font-family: inherit;
            resize: vertical;
        }

        .alert-success {
            padding: 0.75rem 1rem;
            border-radius: 0.65rem;
            background: rgba(74, 222, 128, 0.12);
            border: 1px solid rgba(74, 222, 128, 0.25);
            color: rgb(74, 222, 128);
            font-size: 0.9rem;
        }

        .alert-error {
            padding: 0.75rem 1rem;
            border-radius: 0.65rem;
            background: rgba(248, 113, 113, 0.12);
            border: 1px solid rgba(248, 113, 113, 0.25);
            color: rgb(248, 113, 113);
            font-size: 0.9rem;
        }

        .btn.danger {
            color: var(--danger);
            border-color: rgba(248, 113, 113, 0.35);
        }

        .business-import-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .business-import-modal.is-open {
            display: flex;
        }

        .business-import-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(4px);
            border: none;
            cursor: pointer;
        }

        .business-import-dialog {
            position: relative;
            width: 100%;
            max-width: 520px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            box-shadow: rgba(0, 0, 0, 0.45) 0 24px 48px;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        .business-import-dialog h2 {
            margin: 0 0 0.35rem;
            font-size: 1.15rem;
        }

        .business-import-file {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .business-import-actions {
            margin-top: 1.25rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .business-import-error {
            margin-top: 0.75rem;
            padding: 0.65rem 0.75rem;
            border-radius: 0.65rem;
            background: rgba(248, 113, 113, 0.12);
            border: 1px solid rgba(248, 113, 113, 0.25);
            color: rgb(248, 113, 113);
            font-size: 0.85rem;
            display: none;
        }

        .business-import-error.is-visible {
            display: block;
        }

        .business-import-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: rgba(11, 12, 20, 0.92);
            backdrop-filter: blur(8px);
        }

        .business-import-overlay.is-visible {
            display: flex;
        }

        .business-import-overlay-card {
            width: 100%;
            max-width: 400px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: rgba(0, 0, 0, 0.45) 0 24px 48px;
        }

        .business-import-spinner {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            border: 3px solid rgba(255, 255, 255, 0.12);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: business-import-spin 0.85s linear infinite;
        }

        @keyframes business-import-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .business-import-overlay-title {
            margin: 0 0 0.35rem;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .business-import-overlay-text {
            margin: 0 0 1rem;
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.5;
        }

        .business-import-progress {
            height: 6px;
            border-radius: 99px;
            background: rgba(255, 255, 255, 0.08);
            overflow: hidden;
        }

        .business-import-progress-bar {
            height: 100%;
            width: 35%;
            border-radius: inherit;
            background: linear-gradient(90deg, #FFC107, #FF9800);
            animation: business-import-progress 1.6s ease-in-out infinite;
        }

        @keyframes business-import-progress {
            0% {
                transform: translateX(-120%);
            }

            100% {
                transform: translateX(320%);
            }
        }

        .admin-filters {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .admin-filters-input {
            background: rgb(15, 16, 24);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 0.55rem 0.75rem;
            color: var(--text);
            font-size: 0.9rem;
            flex: 1 1 200px;
            min-width: 200px;
            font: inherit;
            outline: none;
        }

        .admin-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 0.25rem 0;
        }

        .admin-pagination-info {
            font-size: 0.85rem;
            color: var(--muted);
        }

        .admin-pagination-nav {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .admin-pagination-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            min-height: 36px;
            padding: 0 0.65rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border);
            background: #1b1c2a;
            color: var(--text);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .admin-pagination-link:hover {
            border-color: rgba(255, 193, 7, 0.35);
            color: var(--accent);
        }

        .admin-pagination-link.is-active {
            background: rgba(243, 183, 36, 0.15);
            border-color: rgba(243, 183, 36, 0.35);
            color: var(--accent);
            pointer-events: none;
        }

        .admin-pagination-link.is-disabled {
            opacity: 0.45;
            pointer-events: none;
        }

        .admin-pagination-ellipsis {
            color: var(--muted);
            padding: 0 0.25rem;
            font-size: 0.85rem;
        }

        .admin-header-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            align-items: center;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css">
</head>

<body cz-shortcut-listen="true">
    <div id="root">
        <div class="admin-root">
            @include('Admin.layouts.header')
            <main class="admin-main">
                <div class="stack gap-lg">
                    @if (session('success'))
                        <div class="alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert-error">
                            <ul style="margin: 0; padding-left: 1.1rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row-between">
                        <div>
                            <h1 class="text-2xl font-bold" style="margin: 0px;">İşletmeler</h1>
                            <p class="muted" style="margin-top: 4px; font-size: 0.85rem;">İşletme yönetimi.</p>
                        </div>
                        <div class="admin-header-actions">
                            <a href="{{ route('admin.isletmeler.import-history') }}" class="btn ghost">İçe Aktarma
                                Geçmişi</a>
                            <button type="button" class="btn primary" data-open-import-business>İçe Aktar</button>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.isletmeler') }}" class="admin-filters">
                        <input name="search" placeholder="İşletme ara…" type="search" value="{{ $search }}"
                            class="admin-filters-input">
                        <select name="category" class="select" style="min-width: 180px;">
                            <option value="all" @selected($category === 'all')>Tüm kategoriler</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) $category === (string) $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status" class="select" style="min-width: 160px;">
                            <option value="all" @selected($status === 'all')>Tüm durumlar</option>
                            <option value="active" @selected($status === 'active')>Aktif</option>
                            <option value="inactive" @selected($status === 'inactive')>Pasif</option>
                            <option value="closed" @selected($status === 'closed')>Kapalı</option>
                        </select>
                        <button type="submit" class="btn primary">Filtrele</button>
                        @if ($search !== '' || $status !== 'all' || $category !== 'all')
                            <a href="{{ route('admin.isletmeler') }}" class="btn ghost">Temizle</a>
                        @endif
                        <span class="muted" style="font-size: 0.85rem; margin-left: auto;">
                            {{ number_format($totalCount) }} kayıt
                        </span>
                    </form>

                    <div class="card" style="padding: 0px; overflow: hidden;">
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead style="background: rgba(255, 255, 255, 0.03);">
                                    <tr>
                                        <th style="padding: 0.75rem 1rem;">İşletme</th>
                                        <th style="padding: 0.75rem 1rem;">Kategori</th>
                                        <th style="padding: 0.75rem 1rem;">Durum</th>
                                        <th style="padding: 0.75rem 1rem;">Sıra</th>
                                        <th style="padding: 0.75rem 1rem;">Badge</th>
                                        <th style="padding: 0.75rem 1rem;">Görüntülenme</th>
                                        <th style="padding: 0.75rem 1rem;">Onaylı</th>
                                        <th style="padding: 0.75rem 1rem;">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($businesses as $business)
                                        <tr style="border-top: 1px solid var(--border);">
                                            <td style="padding: 0.75rem 1rem;">
                                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                    @if ($business->logoDisplayUrl())
                                                        <img alt="{{ $business->name }}"
                                                            src="{{ $business->logoDisplayUrl() }}"
                                                            style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover; background: rgb(15, 16, 24);">
                                                    @else
                                                        <span
                                                            style="width: 32px; height: 32px; border-radius: 6px; flex-shrink: 0; background: rgba(255, 193, 7, 0.15); color: var(--accent); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">{{ mb_substr($business->name, 0, 1) }}</span>
                                                    @endif
                                                    <div>
                                                        <div style="font-weight: 600;">{{ $business->name }}</div>
                                                        @if ($business->city)
                                                            <div class="muted" style="font-size: 0.75rem;">
                                                                {{ $business->city }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="padding: 0.75rem 1rem; color: var(--muted);">
                                                {{ $business->category?->name ?? '—' }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem;">
                                                <span
                                                    style="display: inline-block; padding: 0.15rem 0.55rem; border-radius: 99px; font-size: 0.7rem; font-weight: 700; {{ $business->statusBadgeStyle() }}">{{ $business->status }}</span>
                                            </td>
                                            <td style="padding: 0.75rem 1rem; color: var(--muted); font-weight: 600;">
                                                {{ (int) $business->sort_order > 0 ? $business->sort_order : '—' }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem;">
                                                @php $browseBadge = $business->browseBadge(); @endphp
                                                @if ($browseBadge)
                                                    <span
                                                        style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 6px; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; background: rgba(243, 183, 36, 0.15); color: var(--accent);">
                                                        {{ $browseBadge['label'] }}
                                                    </span>
                                                @else
                                                    <span class="muted">—</span>
                                                @endif
                                            </td>
                                            <td style="padding: 0.75rem 1rem; color: var(--muted);">
                                                {{ number_format($business->view_count) }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem;">
                                                <button type="button" disabled
                                                    title="{{ $business->verified ? 'Onaylı' : 'Onaysız' }}"
                                                    style="width: 36px; height: 20px; border-radius: 10px; border: none; cursor: default; background: {{ $business->verified ? 'rgb(74, 222, 128)' : 'rgba(255, 255, 255, 0.15)' }}; position: relative;">
                                                    <span
                                                        style="position: absolute; top: 2px; left: {{ $business->verified ? '18px' : '2px' }}; width: 16px; height: 16px; border-radius: 50%; background: rgb(255, 255, 255);">
                                                    </span>
                                                </button>
                                            </td>
                                            <td style="padding: 0.75rem 1rem;">
                                                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                                                    <button type="button" class="btn ghost btn-edit-business"
                                                        data-id="{{ $business->id }}"
                                                        style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">Düzenle</button>
                                                    <form method="POST"
                                                        action="{{ route('admin.isletmeler.destroy', $business) }}"
                                                        data-delete-business>
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="redirect_search"
                                                            value="{{ $search }}">
                                                        <input type="hidden" name="redirect_status"
                                                            value="{{ $status }}">
                                                        <input type="hidden" name="redirect_category"
                                                            value="{{ $category }}">
                                                        <input type="hidden" name="redirect_page"
                                                            value="{{ $businesses->currentPage() }}">
                                                        <button type="submit" class="btn ghost danger"
                                                            style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">Sil</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8"
                                                style="padding: 2rem 1rem; text-align: center; color: var(--muted);">
                                                İşletme bulunamadı.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if ($businesses->hasPages())
                        @php
                            $currentPage = $businesses->currentPage();
                            $lastPage = $businesses->lastPage();
                            $pageStart = max(1, $currentPage - 2);
                            $pageEnd = min($lastPage, $currentPage + 2);
                        @endphp
                        <div class="admin-pagination">
                            <span class="admin-pagination-info">
                                @if ($businesses->total() > 0)
                                    {{ $businesses->firstItem() }}–{{ $businesses->lastItem() }} /
                                    {{ number_format($businesses->total()) }} kayıt
                                @else
                                    0 kayıt
                                @endif
                            </span>
                            <nav class="admin-pagination-nav" aria-label="Sayfalandırma">
                                <a href="{{ $businesses->previousPageUrl() ?? '#' }}"
                                    class="admin-pagination-link @if ($businesses->onFirstPage()) is-disabled @endif"
                                    @if ($businesses->onFirstPage()) aria-disabled="true" @endif>Önceki</a>

                                @if ($pageStart > 1)
                                    <a href="{{ $businesses->url(1) }}" class="admin-pagination-link">1</a>
                                    @if ($pageStart > 2)
                                        <span class="admin-pagination-ellipsis">…</span>
                                    @endif
                                @endif

                                @for ($page = $pageStart; $page <= $pageEnd; $page++)
                                    <a href="{{ $businesses->url($page) }}"
                                        class="admin-pagination-link @if ($page === $currentPage) is-active @endif"
                                        @if ($page === $currentPage) aria-current="page" @endif>{{ $page }}</a>
                                @endfor

                                @if ($pageEnd < $lastPage)
                                    @if ($pageEnd < $lastPage - 1)
                                        <span class="admin-pagination-ellipsis">…</span>
                                    @endif
                                    <a href="{{ $businesses->url($lastPage) }}"
                                        class="admin-pagination-link">{{ $lastPage }}</a>
                                @endif

                                <a href="{{ $businesses->nextPageUrl() ?? '#' }}"
                                    class="admin-pagination-link @if (!$businesses->hasMorePages()) is-disabled @endif"
                                    @if (!$businesses->hasMorePages()) aria-disabled="true" @endif>Sonraki</a>
                            </nav>
                        </div>
                    @elseif ($businesses->total() > 0)
                        <div class="admin-pagination">
                            <span class="admin-pagination-info">
                                {{ $businesses->firstItem() }}–{{ $businesses->lastItem() }} /
                                {{ number_format($businesses->total()) }} kayıt
                            </span>
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <div id="business-edit-drawer" class="business-drawer" aria-hidden="true">
        <button type="button" class="business-drawer-backdrop" data-close-drawer aria-label="Kapat"></button>
        <aside class="business-drawer-panel">
            <div
                style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; background: var(--bg); z-index: 1;">
                <h2 id="business-edit-title" style="margin: 0; font-size: 1.1rem; font-weight: 700;">Düzenle</h2>
                <button type="button" class="btn ghost" data-close-drawer
                    style="font-size: 1.1rem; padding: 0.25rem 0.55rem;">✕</button>
            </div>
            <form id="business-edit-form" method="POST" action="{{ route('admin.isletmeler.store') }}"
                enctype="multipart/form-data" style="display: flex; flex-direction: column; flex: 1;">
                @csrf
                <input type="hidden" name="_method" id="business-form-method" value="POST">
                <input type="hidden" name="redirect_search" value="{{ $search }}">
                <input type="hidden" name="redirect_status" value="{{ $status }}">
                <input type="hidden" name="redirect_category" value="{{ $category }}">
                <input type="hidden" name="redirect_page" value="{{ $businesses->currentPage() }}">
                <input type="hidden" id="business-remove-logo" name="remove_logo" value="0">

                <div style="flex: 1; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label class="muted"
                            style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">Logo</label>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div
                                style="position: relative; width: 80px; height: 80px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: rgb(15, 16, 24); display: flex; align-items: center; justify-content: center;">
                                <img id="business-logo-preview" alt="Logo önizleme"
                                    style="width: 100%; height: 100%; object-fit: contain; display: none;">
                                <span id="business-logo-placeholder" class="muted"
                                    style="font-size: 0.7rem; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;">Logo
                                    yok</span>
                                <button type="button" id="business-logo-remove-btn"
                                    style="position: absolute; top: 4px; right: 4px; background: rgba(0, 0, 0, 0.6); border: none; color: #fff; width: 20px; height: 20px; border-radius: 50%; cursor: pointer; display: none; align-items: center; justify-content: center; font-size: 10px;">✕</button>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--muted);">
                                <p style="margin: 0;">Önerilen: 400x400 PNG/JPG</p>
                                <p style="margin: 4px 0 0;">Max 4 MB</p>
                                <label class="btn ghost" style="margin-top: 8px; font-size: 0.8rem; cursor: pointer;">
                                    Dosya seç
                                    <input id="business-logo-input" name="logo" accept="image/*" type="file"
                                        style="display: none;">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 130px;">
                            <label class="muted"
                                style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Durum</label>
                            <select id="field-status" name="status" class="select form-control">
                                <option value="active">active</option>
                                <option value="inactive">inactive</option>
                                <option value="closed">closed</option>
                            </select>
                        </div>
                        <div style="flex: 1; min-width: 130px;">
                            <label class="muted"
                                style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Kategori</label>
                            <select id="field-category_id" name="category_id" class="select form-control">
                                <option value="">— Seçilmedi —</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label
                            style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; align-self: flex-end; padding-bottom: 8px;">
                            <input id="field-verified" name="verified" type="checkbox" value="1"
                                style="accent-color: rgb(74, 222, 128);">
                            <span>Doğrulanmış</span>
                        </label>
                    </div>

                    <div>
                        <label class="muted"
                            style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">İşletme adı
                            *</label>
                        <input id="field-name" name="name" class="form-control"
                            placeholder="Örn: Crypto Café Berlin" required>
                    </div>
                    <div>
                        <label class="muted"
                            style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Açıklama</label>
                        <textarea id="field-description" name="description" rows="3" class="form-control"
                            placeholder="Kısa açıklama…"></textarea>
                    </div>
                    <div>
                        <label class="muted"
                            style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Adres</label>
                        <input id="field-address" name="address" class="form-control" placeholder="Tam adres">
                    </div>
                    <div style="display: flex; gap: 0.75rem;">
                        <div style="flex: 1;">
                            <label class="muted"
                                style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Lat
                                (enlem)</label>
                            <input id="field-lat" name="lat" step="any" type="number" class="form-control"
                                placeholder="52.52">
                        </div>
                        <div style="flex: 1;">
                            <label class="muted"
                                style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Lng
                                (boylam)</label>
                            <input id="field-lng" name="lng" step="any" type="number" class="form-control"
                                placeholder="13.405">
                        </div>
                    </div>
                    <p class="muted" style="font-size: 0.75rem; margin: 0;">Haritaya tıklayarak konumu işaretle —
                        Lat/Lng
                        otomatik dolar.</p>
                    <div
                        style="height: 260px; border-radius: 0.5rem; overflow: hidden; border: 1px solid var(--border); margin-top: 4px;">
                        <div id="admin-business-map" style="width: 100%; height: 100%;"></div>
                    </div>
                    <div style="display: flex; gap: 0.75rem;">
                        <div style="flex: 1;">
                            <label class="muted"
                                style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Website</label>
                            <input id="field-website" name="website" class="form-control" placeholder="https://">
                        </div>
                        <div style="flex: 1;">
                            <label class="muted"
                                style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Telefon</label>
                            <input id="field-phone" name="phone" class="form-control" placeholder="+49 30 …">
                        </div>
                    </div>
                    <div>
                        <label class="muted"
                            style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">Ödeme
                            tipi</label>
                        <p class="muted" style="font-size: 0.75rem; margin: 0 0 8px;">Haritadaki pin rengini
                            belirler.</p>
                        <select id="field-payment_provider" name="payment_provider" class="select form-control"
                            required>
                            <option value="zcash">Zcash</option>
                            <option value="flexa">Flexa</option>
                        </select>
                    </div>
                    <div>
                        <label class="muted"
                            style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Zcash
                            Adresi</label>
                        <input id="field-zcash_address" name="zcash_address" class="form-control"
                            style="font-family: ui-monospace, monospace; font-size: 0.85rem;">
                    </div>
                    <div>
                        <label class="muted"
                            style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Twitter / X</label>
                        <input id="field-social_twitter" name="social_twitter" class="form-control"
                            placeholder="@kullaniciadi">
                    </div>

                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <div style="flex: 0 0 120px;">
                            <label class="muted"
                                style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 6px;">Sıralama</label>
                            <input id="field-sort_order" name="sort_order" type="number" min="0"
                                max="9999" class="form-control" value="0" placeholder="0">
                        </div>
                        <div style="flex: 1; min-width: 180px;">
                            <label class="muted"
                                style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 6px;">Badge</label>
                            <select id="field-badge_key" name="badge_key" class="select form-control">
                                <option value="">— Yok —</option>
                                @foreach (\App\Models\Business::badgePresets() as $key => $preset)
                                    <option value="{{ $key }}">{{ $preset['label'] }}</option>
                                @endforeach
                                <option value="custom">Custom…</option>
                            </select>
                        </div>
                        <div id="field-badge-label-wrap" style="flex: 1; min-width: 180px; display: none;">
                            <label class="muted"
                                style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 6px;">Özel
                                badge metni</label>
                            <input id="field-badge_label" name="badge_label" type="text" maxlength="24"
                                class="form-control" placeholder="e.g. Partner">
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    style="padding: 1rem 1.5rem; border-top: 1px solid var(--border); display: flex; gap: 0.75rem; justify-content: flex-end; position: sticky; bottom: 0; background: var(--bg);">
                    <button type="button" class="btn ghost" data-close-drawer>İptal</button>
                    <button type="submit" class="btn primary">Kaydet</button>
                </div>
            </form>
        </aside>
    </div>

    <div id="business-import-modal" class="business-import-modal" aria-hidden="true" role="dialog"
        aria-labelledby="business-import-title">
        <button type="button" class="business-import-backdrop" data-close-import aria-label="Kapat"></button>
        <div class="business-import-dialog">
            <h2 id="business-import-title">JSON İçe Aktar</h2>
            <p class="muted" style="font-size: 0.85rem; margin: 0;">
                İşletme listesini JSON dosyası olarak yükleyin. Büyük dosyalarda işlem birkaç dakika sürebilir.
            </p>
            <form id="business-import-form" method="POST" action="{{ route('admin.isletmeler.import') }}"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="redirect_search" value="{{ $search }}">
                <input type="hidden" name="redirect_status" value="{{ $status }}">
                <input type="hidden" name="redirect_category" value="{{ $category }}">
                <input type="hidden" name="redirect_page" value="{{ $businesses->currentPage() }}">
                <div class="business-import-file">
                    <label class="muted" for="business-json-file"
                        style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">JSON dosyası</label>
                    <input id="business-json-file" name="json_file" type="file" accept=".json,application/json"
                        class="form-control" required>
                    <p class="muted" style="font-size: 0.75rem; margin: 0;">
                        Mükerrer kayıtlar atlanır. Maksimum dosya boyutu 50 MB.
                    </p>
                </div>
                <div id="business-import-error" class="business-import-error" role="alert"></div>
                <div class="business-import-actions">
                    <button type="button" class="btn ghost" data-close-import>İptal</button>
                    <button type="submit" class="btn primary" id="business-import-submit">İçe Aktar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="business-import-overlay" class="business-import-overlay" aria-hidden="true" aria-live="polite">
        <div class="business-import-overlay-card">
            <div class="business-import-spinner" aria-hidden="true"></div>
            <h3 class="business-import-overlay-title">İçe aktarılıyor…</h3>
            <p class="business-import-overlay-text">
                İşletmeler veritabanına ekleniyor. Lütfen bu sayfayı kapatmayın veya yenilemeyin.
            </p>
            <div class="business-import-progress" aria-hidden="true">
                <div class="business-import-progress-bar"></div>
            </div>
        </div>
    </div>

    <script type="application/json" id="businesses-data">@json($businessesData)</script>
    <span id="business-update-url-template" data-url="{{ url('/admin/isletmeler/__ID__') }}" hidden></span>
    <span id="business-store-url" data-url="{{ route('admin.isletmeler.store') }}" hidden></span>

    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
    <script src="{{ asset('assets/js/admin-business-edit.js') }}" defer></script>
    <script src="{{ asset('assets/js/admin-business-import.js') }}" defer></script>
</body>

</html>
