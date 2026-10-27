@php
    $periodLabels = [
        'weekly' => 'Haftalık',
        'monthly' => 'Aylık',
        'yearly' => 'Yıllık',
        'all' => 'Tüm zamanlar',
    ];
    $formatScore = fn (int $points): string => ($points >= 0 ? '+' : '').number_format($points);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Topluluklar</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; }
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
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: var(--bg); }
        #root { min-height: 100vh; }
        a { color: inherit; text-decoration: none; }
        .admin-root { min-height: 100vh; display: flex; flex-direction: column; }
        .admin-top {
            display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border); background: rgba(20, 21, 41, 0.95);
            backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 10; flex-wrap: wrap;
        }
        .brand { font-weight: 700; letter-spacing: 0.04em; flex-shrink: 0; display: inline-flex; align-items: center; gap: 0.625rem; }
        .admin-nav {
            display: flex; gap: 0.25rem; flex: 1; overflow-x: auto; scrollbar-width: none;
            margin: 0 -0.5rem; padding: 0 0.5rem;
        }
        .admin-nav::-webkit-scrollbar { display: none; }
        .nav-link {
            padding: 0.6rem 0.85rem; border-radius: 0.5rem; color: var(--muted); font-weight: 600;
            white-space: nowrap; flex-shrink: 0; min-height: 40px; display: inline-flex; align-items: center; font-size: 0.92rem;
        }
        .nav-link.active { color: var(--accent); background: rgba(243, 183, 36, 0.12); }
        .brand .text-primary { color: var(--accent); }
        .brand .text-on-surface { color: var(--text); }
        .brand .font-headline { display: inline-flex; align-items: baseline; font-size: 20px; font-weight: 900; line-height: 1; letter-spacing: -0.02em; }
        .brand svg { flex-shrink: 0; }
        .admin-main { flex: 1; padding: 1rem; max-width: 1200px; width: 100%; margin: 0 auto; }
        @media (min-width: 768px) {
            .admin-top { padding: 0.75rem 1.25rem; gap: 1rem; }
            .admin-main { padding: 1.5rem; }
        }
        .stack { display: flex; flex-direction: column; gap: 1rem; }
        .gap-lg { gap: 1.25rem; }
        .row-between { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .card { background: var(--panel); border: 1px solid var(--border); border-radius: 1rem; padding: 1rem; }
        .muted { color: var(--muted); margin: 0; }
        .btn {
            border-radius: 0.65rem; border: 1px solid var(--border); background: #1b1c2a; color: var(--text);
            padding: 0.55rem 0.9rem; font-weight: 600; cursor: pointer; min-height: 40px;
            display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; font: inherit;
        }
        .btn.primary { background: linear-gradient(135deg, #FFC107, #FF9800); color: #111827; border: none; }
        .btn.ghost { background: transparent; }
        .btn.danger { color: var(--danger); }
        .table {
            width: 100%; border-collapse: collapse; font-size: 0.9rem; display: block;
            overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch;
        }
        @media (min-width: 768px) { .table { display: table; white-space: normal; } }
        .table th, .table td { text-align: left; padding: 0.65rem 0.75rem; border-bottom: 1px solid var(--border); vertical-align: top; }
        .table th { color: var(--muted); font-weight: 600; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.04em; }
        .table tbody tr:last-child td { border-bottom: none; }
        h1 { margin: 0 0 0.5rem; font-size: 1.5rem; }
        .alert-success {
            padding: 0.75rem 1rem; border-radius: 0.65rem; background: rgba(74, 222, 128, 0.12);
            border: 1px solid rgba(74, 222, 128, 0.25); color: rgb(74, 222, 128); font-size: 0.9rem;
        }
        .site-drawer { display: none; position: fixed; inset: 0; z-index: 40; }
        .site-drawer.is-open { display: flex; }
        .site-drawer-backdrop { flex: 1; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); border: none; cursor: pointer; }
        .site-drawer-panel {
            width: 100%; max-width: 440px; background: var(--bg); border-left: 1px solid var(--border);
            display: flex; flex-direction: column; overflow-y: auto; box-shadow: rgba(0, 0, 0, 0.6) -30px 0 60px;
        }
        .form-control, .input {
            width: 100%; margin-top: 4px; background: rgb(15, 16, 24); border: 1px solid var(--border);
            border-radius: 0.5rem; padding: 0.55rem 0.75rem; color: var(--text); font-size: 0.9rem; font: inherit;
        }
        textarea.form-control, textarea.input { font-family: inherit; resize: vertical; }
        .field-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--muted); }
        .empty-row td { text-align: center; padding: 2rem 1rem; color: var(--muted); }
        .stat-grid { display: grid; gap: 0.75rem; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); }
        .stat-card { padding: 1rem; }
        .stat-value { font-size: 1.5rem; font-weight: 700; margin-top: 0.25rem; }
        .period-tabs { display: flex; gap: 0.5rem; flex-wrap: wrap; }
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
                            <h1 style="margin-bottom: 4px;">Topluluklar</h1>
                            <p class="muted" style="font-size: 0.88rem;">
                                Katkı puanları, sıralama ve topluluk yönetimi — {{ url('/communities') }} ile senkron.
                            </p>
                        </div>
                        <button type="button" class="btn primary" data-open-create-community>+ Yeni Topluluk</button>
                    </div>

                    <div class="stat-grid">
                        <div class="card stat-card">
                            <div class="muted" style="font-size: 0.75rem;">Topluluk</div>
                            <div class="stat-value">{{ number_format($stats['community_count']) }}</div>
                        </div>
                        <div class="card stat-card">
                            <div class="muted" style="font-size: 0.75rem;">Toplam katkı</div>
                            <div class="stat-value">{{ number_format($stats['contributions_count']) }}</div>
                        </div>
                        <div class="card stat-card">
                            <div class="muted" style="font-size: 0.75rem;">Aylık puan</div>
                            <div class="stat-value" style="color: var(--accent);">{{ $formatScore($stats['monthly_points']) }}</div>
                        </div>
                        <div class="card stat-card">
                            <div class="muted" style="font-size: 0.75rem;">Tüm zamanlar</div>
                            <div class="stat-value">{{ $formatScore($stats['all_time_points']) }}</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="period-tabs">
                            @foreach ($periodLabels as $key => $label)
                                <a href="{{ route('admin.topluluklar', ['period' => $key]) }}"
                                    @class(['btn', 'primary' => $period === $key, 'ghost' => $period !== $key])
                                    style="font-size: 0.82rem; padding: 6px 12px;">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="card" style="padding: 0; overflow: hidden;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Topluluk</th>
                                    <th>Hafta</th>
                                    <th>Ay</th>
                                    <th>Yıl</th>
                                    <th>Toplam</th>
                                    <th>{{ $periodLabels[$period] ?? 'Aylık' }}</th>
                                    <th>İşletme</th>
                                    <th>Katkı</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($communities as $index => $community)
                                    <tr>
                                        <td style="font-weight: 700; color: var(--muted);">{{ $index + 1 }}</td>
                                        <td>
                                            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                                <span style="font-size: 1.25rem;">{{ $community['flag_emoji'] ?: '🌍' }}</span>
                                                <div>
                                                    <div style="font-weight: 600;">{{ $community['name'] }}</div>
                                                    <div class="muted" style="font-size: 0.75rem;">{{ $community['slug'] }}</div>
                                                    @if ($community['description'])
                                                        <div class="muted" style="font-size: 0.78rem; margin-top: 2px; max-width: 260px; white-space: normal;">
                                                            {{ $community['description'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $formatScore($community['scores']['weekly']) }}</td>
                                        <td>{{ $formatScore($community['scores']['monthly']) }}</td>
                                        <td>{{ $formatScore($community['scores']['yearly']) }}</td>
                                        <td>{{ $formatScore($community['scores']['all']) }}</td>
                                        <td style="font-weight: 700; color: var(--accent);">
                                            {{ $formatScore($community['scores'][$period]) }}
                                        </td>
                                        <td>{{ number_format($community['business_count']) }}</td>
                                        <td>{{ number_format($community['contributions_count']) }}</td>
                                        <td>
                                            <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                                                <button type="button" class="btn ghost" data-edit-community
                                                    data-id="{{ $community['id'] }}">Düzenle</button>
                                                <form method="POST"
                                                    action="{{ route('admin.topluluklar.destroy', $community['id']) }}"
                                                    data-delete-community>
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="redirect_period" value="{{ $period }}">
                                                    <button type="submit" class="btn ghost danger">Sil</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="10">Henüz topluluk eklenmemiş.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="community-edit-drawer" class="site-drawer" aria-hidden="true">
        <button type="button" class="site-drawer-backdrop" data-close-drawer aria-label="Kapat"></button>
        <aside class="site-drawer-panel">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
                <h2 id="community-edit-title" style="margin: 0; font-size: 1.1rem;">+ Yeni Topluluk</h2>
                <button type="button" class="btn ghost" data-close-drawer aria-label="Kapat">✕</button>
            </div>
            <form id="community-edit-form" method="POST" action="{{ route('admin.topluluklar.store') }}"
                style="display: flex; flex-direction: column; flex: 1;">
                @csrf
                <input type="hidden" name="_method" id="community-form-method" value="POST">
                <input type="hidden" name="redirect_period" id="community-redirect-period" value="{{ $period }}">

                <div style="flex: 1; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                    <label class="stack" style="gap: 4px;">
                        <span class="field-label">Ad *</span>
                        <input id="field-name" name="name" class="input" required maxlength="255" placeholder="Örn. Turkey">
                    </label>
                    <label class="stack" style="gap: 4px;">
                        <span class="field-label">Slug *</span>
                        <input id="field-slug" name="slug" class="input" required maxlength="255" placeholder="turkey"
                            style="font-family: monospace; font-size: 0.85rem;">
                    </label>
                    <label class="stack" style="gap: 4px;">
                        <span class="field-label">Bayrak emoji</span>
                        <input id="field-flag_emoji" name="flag_emoji" class="input" maxlength="16" placeholder="🇹🇷"
                            style="width: 120px;">
                    </label>
                    <label class="stack" style="gap: 4px;">
                        <span class="field-label">Açıklama</span>
                        <textarea id="field-description" name="description" class="input" rows="3"
                            maxlength="1000" placeholder="Kısa topluluk açıklaması"></textarea>
                    </label>
                </div>

                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border); display: flex; gap: 0.5rem; justify-content: flex-end; position: sticky; bottom: 0; background: var(--bg);">
                    <button type="button" class="btn ghost" data-close-drawer>İptal</button>
                    <button type="submit" class="btn primary">Kaydet</button>
                </div>
            </form>
        </aside>
    </div>

    <script type="application/json" id="communities-data">@json($communitiesData)</script>
    <span id="community-update-url-template" data-url="{{ url('/admin/topluluklar/__ID__') }}" hidden></span>
    <span id="community-store-url" data-url="{{ route('admin.topluluklar.store') }}" hidden></span>

    <script src="{{ asset('assets/js/admin-communities.js') }}" defer></script>
</body>

</html>
