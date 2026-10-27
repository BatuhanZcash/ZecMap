<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — İçe Aktarma Geçmişi</title>
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

        .muted {
            color: var(--muted);
            margin: 0;
        }

        .btn {
            border-radius: 0.65rem;
            border: 1px solid var(--border);
            background: #1b1c2a;
            color: var(--text);
            padding: 0.55rem 0.9rem;
            font-weight: 600;
            cursor: pointer;
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            font: inherit;
        }

        .btn.primary {
            background: linear-gradient(135deg, #FFC107, #FF9800);
            color: #111827;
            border: none;
        }

        .btn.ghost {
            background: transparent;
        }

        .btn.danger {
            color: var(--danger);
            border-color: rgba(248, 113, 113, 0.35);
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 0;
            overflow: hidden;
        }

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
            vertical-align: middle;
        }

        .table th {
            color: var(--muted);
            font-weight: 600;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            background: rgba(255, 255, 255, 0.03);
        }

        h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .alert-success {
            padding: 0.75rem 1rem;
            border-radius: 0.65rem;
            background: rgba(74, 222, 128, 0.12);
            border: 1px solid rgba(74, 222, 128, 0.25);
            color: rgb(74, 222, 128);
            font-size: 0.9rem;
        }

        .import-empty {
            padding: 2rem 1rem;
            text-align: center;
            color: var(--muted);
        }

        .admin-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1rem;
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

        .stat-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.75rem;
            padding: 0.15rem 0.45rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            font-size: 0.82rem;
            font-weight: 600;
        }

        .stat-badge.is-success {
            color: rgb(74, 222, 128);
            background: rgba(74, 222, 128, 0.12);
        }

        .stat-badge.is-muted {
            color: var(--muted);
        }

        .stat-badge.is-warning {
            color: #fbbf24;
            background: rgba(251, 191, 36, 0.12);
        }
    </style>
</head>

<body>
    <div id="root">
        <div class="admin-root">
            @include('Admin.layouts.header')
            <main class="admin-main">
                <div class="stack gap-lg">
                    @if (session('success'))
                        <div class="alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="row-between">
                        <div>
                            <h1>İçe Aktarma Geçmişi</h1>
                            <p class="muted" style="margin-top: 4px; font-size: 0.85rem;">
                                JSON dosyalarından yapılan içe aktarmaları takip edin ve toplu silin.
                            </p>
                        </div>
                        <a href="{{ route('admin.isletmeler') }}" class="btn ghost">← İşletmelere Dön</a>
                    </div>

                    <div class="card">
                        @if ($imports->isEmpty())
                            <div class="import-empty">
                                Henüz kayıtlı bir JSON içe aktarma işlemi yok.
                            </div>
                        @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Dosya</th>
                                        <th>Tarih</th>
                                        <th>Toplam</th>
                                        <th>Eklenen</th>
                                        <th>Atlanan</th>
                                        <th>Hatalı</th>
                                        <th>Mevcut</th>
                                        <th>Yükleyen</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($imports as $import)
                                        <tr>
                                            <td>{{ $import->id }}</td>
                                            <td>{{ $import->original_filename }}</td>
                                            <td>
                                                <span title="{{ $import->imported_at->format('d.m.Y H:i:s') }}">
                                                    {{ $import->imported_at->format('d.m.Y H:i') }}
                                                </span>
                                            </td>
                                            <td><span class="stat-badge">{{ number_format($import->total_items) }}</span></td>
                                            <td><span class="stat-badge is-success">{{ number_format($import->created_count) }}</span></td>
                                            <td><span class="stat-badge is-muted">{{ number_format($import->skipped_count) }}</span></td>
                                            <td><span class="stat-badge is-warning">{{ number_format($import->failed_count) }}</span></td>
                                            <td><span class="stat-badge">{{ number_format($import->businesses_count) }}</span></td>
                                            <td class="muted" style="font-size: 0.85rem;">
                                                {{ $import->uploadedBy?->name ?? $import->uploadedBy?->email ?? '—' }}
                                            </td>
                                            <td>
                                                @php
                                                    $deleteConfirm = $import->businesses_count > 0
                                                        ? 'Bu içe aktarmadan gelen '.number_format($import->businesses_count).' işletme silinecek. Emin misiniz?'
                                                        : 'Bu içe aktarma kaydı silinecek. Emin misiniz?';
                                                @endphp
                                                <form method="POST"
                                                    action="{{ route('admin.isletmeler.import-destroy', $import) }}"
                                                    onsubmit="return confirm(@json($deleteConfirm));">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn danger">Sil</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if ($imports->hasPages())
                                <div class="admin-pagination">
                                    <span class="admin-pagination-info">
                                        {{ $imports->firstItem() }}–{{ $imports->lastItem() }} / {{ number_format($imports->total()) }}
                                    </span>
                                    <div class="admin-pagination-nav">
                                        @if ($imports->onFirstPage())
                                            <span class="admin-pagination-link is-disabled">Önceki</span>
                                        @else
                                            <a href="{{ $imports->previousPageUrl() }}" class="admin-pagination-link">Önceki</a>
                                        @endif

                                        @foreach ($imports->getUrlRange(max(1, $imports->currentPage() - 2), min($imports->lastPage(), $imports->currentPage() + 2)) as $page => $url)
                                            <a href="{{ $url }}"
                                                class="admin-pagination-link{{ $imports->currentPage() === $page ? ' is-active' : '' }}">{{ $page }}</a>
                                        @endforeach

                                        @if ($imports->hasMorePages())
                                            <a href="{{ $imports->nextPageUrl() }}" class="admin-pagination-link">Sonraki</a>
                                        @else
                                            <span class="admin-pagination-link is-disabled">Sonraki</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>
