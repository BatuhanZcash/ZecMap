<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Moderasyon</title>
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

        .alert-success {
            padding: 0.75rem 1rem;
            border-radius: 0.65rem;
            background: rgba(74, 222, 128, 0.12);
            border: 1px solid rgba(74, 222, 128, 0.25);
            color: rgb(74, 222, 128);
            font-size: 0.9rem;
        }

        .moderation-drawer {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
        }

        .moderation-drawer.is-open {
            display: flex;
        }

        .moderation-drawer-backdrop {
            flex: 1;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            border: none;
            cursor: pointer;
        }

        .moderation-drawer-panel {
            width: min(440px, 100vw);
            height: 100vh;
            border-radius: 0;
            overflow-y: auto;
            box-shadow: rgba(0, 0, 0, 0.35) -8px 0 32px;
            border-left: 1px solid var(--border);
            padding: 1.25rem 1.5rem;
            gap: 1rem;
        }

        .moderation-detail-label {
            margin: 0 0 4px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--muted);
        }

        .moderation-detail-value {
            margin: 0;
            font-size: 0.9rem;
            word-break: break-word;
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
                            <h1 style="margin: 0;">Başvurular</h1>
                            <p class="muted" style="margin: 4px 0 0; font-size: 0.85rem;">
                                İncelenmeyi bekleyen tüm işletme başvuruları (giriş yapmış ve misafir gönderimler dahil).
                            </p>
                        </div>
                        <form method="GET" action="{{ route('admin.moderasyon') }}">
                            <select name="status" class="select" onchange="this.form.submit()">
                                <option value="all" @selected($status === 'all')>Tümü</option>
                                <option value="pending" @selected($status === 'pending')>
                                    Beklemede ({{ $pendingCount }})
                                </option>
                                <option value="approved" @selected($status === 'approved')>Onaylandı</option>
                                <option value="rejected" @selected($status === 'rejected')>Reddedildi</option>
                            </select>
                        </form>
                    </div>

                    <div class="card" style="padding: 0; overflow: hidden;">
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>İşletme</th>
                                        <th>Adres</th>
                                        <th>E-posta</th>
                                        <th>Durum</th>
                                        <th>Tarih</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($applications as $application)
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600;">{{ $application->name }}</div>
                                                @if ($application->category)
                                                    <div class="muted" style="font-size: 0.75rem;">
                                                        {{ $application->category->name }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $application->fullAddress() ?: '—' }}</td>
                                            <td>{{ $application->contactEmail() ?? '—' }}</td>
                                            <td>
                                                <span
                                                    style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 999px; {{ $application->moderationStatusBadgeStyle() }}">
                                                    {{ $application->moderationStatusLabel() }}
                                                </span>
                                            </td>
                                            <td class="muted" style="font-size: 0.8rem; white-space: nowrap;">
                                                {{ $application->created_at?->format('d.m.Y H:i') ?? '—' }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn ghost btn-review-application"
                                                    data-id="{{ $application->id }}"
                                                    style="font-size: 0.8rem;">İncele</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6"
                                                style="padding: 2rem 1rem; text-align: center; color: var(--muted);">
                                                @if ($status === 'pending')
                                                    Onay bekleyen başvuru bulunmuyor.
                                                @else
                                                    Başvuru bulunamadı.
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="moderation-review-drawer" class="moderation-drawer" aria-hidden="true">
        <button type="button" class="moderation-drawer-backdrop" data-close-drawer
            aria-label="Kapat"></button>
        <aside class="card stack moderation-drawer-panel">
            <div class="row-between">
                <h2 id="moderation-review-title" style="margin: 0; font-size: 1.1rem;">Başvuru</h2>
                <button type="button" class="btn ghost" data-close-drawer>Kapat</button>
            </div>

            <div class="stack" style="gap: 0.75rem;">
                <div>
                    <p class="moderation-detail-label">İşletme</p>
                    <p id="moderation-business-name" class="moderation-detail-value" style="font-weight: 700;">—</p>
                </div>
                <div>
                    <p class="moderation-detail-label">Kategori</p>
                    <p id="moderation-business-category" class="moderation-detail-value">—</p>
                </div>
                <div>
                    <p class="moderation-detail-label">Adres</p>
                    <p id="moderation-business-address" class="moderation-detail-value">—</p>
                </div>
                <div>
                    <p class="moderation-detail-label">E-posta</p>
                    <p id="moderation-business-email" class="moderation-detail-value">—</p>
                </div>
                <div>
                    <p class="moderation-detail-label">Gönderen</p>
                    <p id="moderation-business-owner" class="moderation-detail-value">—</p>
                </div>
                <div>
                    <p class="moderation-detail-label">Web sitesi</p>
                    <p id="moderation-business-website" class="moderation-detail-value">—</p>
                </div>
                <div>
                    <p class="moderation-detail-label">Telefon</p>
                    <p id="moderation-business-phone" class="moderation-detail-value">—</p>
                </div>
                <div>
                    <p class="moderation-detail-label">Zcash adresi</p>
                    <p id="moderation-business-zcash" class="moderation-detail-value">—</p>
                </div>
                <div>
                    <p class="moderation-detail-label">Başvuru tarihi</p>
                    <p id="moderation-business-created-at" class="moderation-detail-value">—</p>
                </div>

                <div id="moderation-review-actions" style="display: flex; gap: 0.65rem;">
                    <form id="moderation-approve-form" method="POST" action="#" style="flex: 1;">
                        @csrf
                        <input type="hidden" name="redirect_status" value="{{ $status }}">
                        <button type="submit" class="btn primary" style="width: 100%;">Onayla</button>
                    </form>
                    <form id="moderation-reject-form" method="POST" action="#" hidden>
                        @csrf
                        <input type="hidden" name="redirect_status" value="{{ $status }}">
                    </form>
                    <button type="button" id="moderation-reject-btn" class="btn ghost"
                        style="flex: 1; border-color: rgba(248, 113, 113, 0.4); color: rgb(248, 113, 113);">
                        Reddet
                    </button>
                </div>
            </div>
        </aside>
    </div>

    <script type="application/json" id="moderation-applications-data">@json($applicationsData)</script>
    <span id="moderation-approve-url-template" data-url="{{ url('/admin/moderasyon/__ID__/approve') }}" hidden></span>
    <span id="moderation-reject-url-template" data-url="{{ url('/admin/moderasyon/__ID__/reject') }}" hidden></span>
    <script src="{{ asset('assets/js/admin-moderation.js') }}" defer></script>
</body>

</html>
