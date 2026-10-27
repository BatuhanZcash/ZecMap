<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Genel Bakış</title>
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

        .dashboard-stat-link {
            display: block;
            color: inherit;
            text-decoration: none;
            transition: border-color 0.2s ease;
        }

        .dashboard-stat-link:hover {
            border-color: rgba(255, 193, 7, 0.35);
        }

        .dashboard-chart {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            min-height: 200px;
        }

        .dashboard-chart-row {
            display: grid;
            grid-template-columns: minmax(90px, 130px) 1fr 36px;
            gap: 0.75rem;
            align-items: center;
        }

        @media (min-width: 640px) {
            .dashboard-chart-row {
                grid-template-columns: minmax(120px, 160px) 1fr 40px;
            }
        }

        .dashboard-chart-label {
            font-size: 0.82rem;
            color: var(--muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dashboard-chart-bar-wrap {
            height: 28px;
            border-radius: 6px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.04);
        }

        .dashboard-chart-bar {
            height: 100%;
            min-width: 4px;
            border-radius: 6px;
            background: linear-gradient(90deg, #FFC107, #FF9800);
            transition: width 0.3s ease;
        }

        .dashboard-chart-value {
            font-size: 0.85rem;
            font-weight: 700;
            text-align: right;
        }

        .dashboard-chart-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 200px;
            margin: 0;
            font-size: 0.9rem;
            color: var(--muted);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-badge--pending {
            background: rgba(251, 191, 36, 0.15);
            color: rgb(251, 191, 36);
        }

        .section-header-link {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--accent);
            text-decoration: none;
        }

        .section-header-link:hover {
            text-decoration: underline;
        }

        .table-empty {
            text-align: center;
            color: var(--muted);
            padding: 1.5rem 0.5rem !important;
        }
    </style>
</head>

<body cz-shortcut-listen="true">
    <div id="root">
        <div class="admin-root">
            @include('Admin.layouts.header')
            <main class="admin-main">
                <div class="stack gap-lg">
                    <h1>Genel Bakış</h1>
                    <div class="grid-cards">
                        <a class="card dashboard-stat-link" href="{{ route('admin.isletmeler', ['status' => 'active']) }}">
                            <p class="muted">Aktif İşletmeler</p>
                            <p class="stat">{{ number_format($activeBusinessCount) }}</p>
                        </a>
                        <a class="card dashboard-stat-link" href="{{ route('admin.moderasyon') }}">
                            <p class="muted">Bekleyen Başvurular</p>
                            <p class="stat">{{ number_format($pendingApplicationCount) }}</p>
                        </a>
                        <div class="card">
                            <p class="muted">Toplam Yorum</p>
                            <p class="stat">{{ number_format($totalReviewCount) }}</p>
                        </div>
                        <a class="card dashboard-stat-link" href="{{ route('admin.dogrulama') }}">
                            <p class="muted">Doğrulamalar</p>
                            <p class="stat">{{ number_format($verificationCount) }}</p>
                        </a>
                    </div>

                    <section class="card chart-card">
                        <h2>Kategoriye Göre İşletmeler</h2>
                        @if ($categoryStats->isEmpty())
                            <p class="dashboard-chart-empty">Henüz aktif işletme bulunmuyor.</p>
                        @else
                            <div class="dashboard-chart">
                                @foreach ($categoryStats as $category)
                                    <div class="dashboard-chart-row">
                                        <span class="dashboard-chart-label"
                                            title="{{ $category['name'] }}">{{ $category['name'] }}</span>
                                        <div class="dashboard-chart-bar-wrap">
                                            <div class="dashboard-chart-bar"
                                                style="width: {{ round(($category['count'] / $chartMaxCount) * 100, 1) }}%;"
                                                title="{{ $category['count'] }} işletme"></div>
                                        </div>
                                        <span class="dashboard-chart-value">{{ number_format($category['count']) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    <section class="card">
                        <div class="row-between" style="margin-bottom: 1rem;">
                            <h2 style="margin: 0;">Son Başvurular</h2>
                            <a class="section-header-link" href="{{ route('admin.moderasyon') }}">Tümünü gör</a>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>İşletme</th>
                                    <th>Adres</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentApplications as $application)
                                    <tr>
                                        <td>{{ $application->name }}</td>
                                        <td>{{ $application->fullAddress() ?: '—' }}</td>
                                        <td>
                                            <span class="status-badge status-badge--pending">Beklemede</span>
                                        </td>
                                        <td>{{ $application->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="table-empty" colspan="4">Bekleyen başvuru bulunmuyor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </section>
                </div>
            </main>
        </div>
    </div>
</body>

</html>
