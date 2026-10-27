<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Doğrulama</title>
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

        .verification-drawer {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
        }

        .verification-drawer.is-open {
            display: flex;
        }

        .verification-drawer-backdrop {
            flex: 1;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            border: none;
            cursor: pointer;
        }

        .verification-drawer-panel {
            width: min(440px, 100vw);
            height: 100vh;
            border-radius: 0;
            overflow-y: auto;
            box-shadow: rgba(0, 0, 0, 0.35) -8px 0 32px;
            border-left: 1px solid var(--border);
            padding: 1.25rem 1.5rem;
            gap: 1rem;
        }
    </style>
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

                    <div class="row-between">
                        <div>
                            <h1 style="margin: 0px;">Doğrulama İstekleri</h1>
                            <p class="muted" style="margin: 4px 0px 0px; font-size: 0.85rem;">Kullanıcıların gönderdiği
                                konum doğrulamaları — onaylandığında işletme doğrulanır.</p>
                        </div>
                        <form method="GET" action="{{ route('admin.dogrulama') }}">
                            <select name="status" class="select" onchange="this.form.submit()">
                                <option value="all" @selected($status === 'all')>Tümü</option>
                                <option value="pending" @selected($status === 'pending')>
                                    Beklemede ({{ $pendingCount }})
                                </option>
                                <option value="approved" @selected($status === 'approved')>Onaylanan</option>
                                <option value="rejected" @selected($status === 'rejected')>Reddedilen</option>
                            </select>
                        </form>
                    </div>

                    <div class="card" style="padding: 0px; overflow: hidden;">
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>İşletme</th>
                                        <th>Gönderen</th>
                                        <th>Durum</th>
                                        <th>Tarih</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($requests as $verificationRequest)
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600;">{{ $verificationRequest->business?->name ?? '—' }}</div>
                                            </td>
                                            <td>{{ $verificationRequest->user?->name ?? '—' }}</td>
                                            <td>
                                                <span
                                                    style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 999px; {{ $verificationRequest->statusBadgeStyle() }}">
                                                    {{ $verificationRequest->statusLabel() }}
                                                </span>
                                            </td>
                                            <td class="muted" style="font-size: 0.8rem; white-space: nowrap;">
                                                {{ $verificationRequest->created_at?->format('d.m.Y H:i:s') }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn ghost btn-review-verification"
                                                    data-id="{{ $verificationRequest->id }}"
                                                    style="font-size: 0.8rem;">İncele</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5"
                                                style="padding: 2rem 1rem; text-align: center; color: var(--muted);">
                                                Doğrulama isteği bulunamadı.
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

    <div id="verification-review-drawer" class="verification-drawer" aria-hidden="true">
        <button type="button" class="verification-drawer-backdrop" data-close-drawer
            aria-label="Kapat"></button>
        <aside class="card stack verification-drawer-panel">
            <div class="row-between">
                <h2 id="verification-review-title" style="margin: 0px; font-size: 1.1rem;">Doğrulama İsteği</h2>
                <button type="button" class="btn ghost" data-close-drawer>Kapat</button>
            </div>

            <div class="stack" style="gap: 0.75rem;">
                <div>
                    <p class="muted"
                        style="margin: 0px 0px 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        İşletme</p>
                    <p id="verification-business-name" style="margin: 0px; font-weight: 700;">—</p>
                    <p id="verification-business-slug" class="muted"
                        style="margin: 4px 0px 0px; font-size: 0.82rem;">—</p>
                    <span id="verification-business-verified" hidden
                        style="display: inline-block; margin-top: 8px; font-size: 0.75rem; color: rgb(74, 222, 128);">
                        İşletme zaten doğrulanmış
                    </span>
                </div>

                <div>
                    <p class="muted"
                        style="margin: 0px 0px 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        Gönderen</p>
                    <p id="verification-user-name" style="margin: 0px;">—</p>
                </div>

                <div>
                    <p class="muted"
                        style="margin: 0px 0px 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        Güncel olmayan bilgi</p>
                    <p id="verification-outdated-info"
                        style="margin: 0px; white-space: pre-wrap; font-size: 0.9rem;">—</p>
                </div>

                <div>
                    <p class="muted"
                        style="margin: 0px 0px 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        Nasıl doğruladı?</p>
                    <p id="verification-how-verified"
                        style="margin: 0px; white-space: pre-wrap; font-size: 0.9rem;">—</p>
                </div>

                <div id="verification-reviewed-meta" hidden>
                    <p class="muted"
                        style="margin: 0px 0px 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        İnceleme</p>
                    <p id="verification-reviewed-at" class="muted" style="margin: 0px; font-size: 0.82rem;">—</p>
                </div>

                <form id="verification-approve-form" method="POST" action="#" class="stack" style="gap: 0.75rem;">
                    @csrf
                    <input type="hidden" name="redirect_status" value="{{ $status }}">
                    <div>
                        <label class="muted"
                            style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Admin notu
                            (isteğe bağlı)</label>
                        <textarea id="verification-admin-note" name="admin_note" class="input" rows="3"
                            placeholder="Red veya onay notu…" style="width: 100%; margin-top: 6px; resize: vertical;"></textarea>
                    </div>
                    <div style="display: flex; gap: 0.65rem;">
                        <button type="submit" class="btn primary" style="flex: 1 1 0%;">Onayla</button>
                        <button type="button" id="verification-reject-btn" class="btn ghost"
                            style="flex: 1 1 0%; border-color: rgba(248, 113, 113, 0.4); color: rgb(248, 113, 113);">
                            Reddet
                        </button>
                    </div>
                </form>

                <form id="verification-reject-form" method="POST" action="#" hidden>
                    @csrf
                    <input type="hidden" name="redirect_status" value="{{ $status }}">
                    <input type="hidden" name="admin_note" id="verification-reject-note" value="">
                </form>
            </div>
        </aside>
    </div>

    <script type="application/json" id="verification-requests-data">@json($requestsData)</script>
    <span id="verification-approve-url-template" data-url="{{ url('/admin/dogrulama/__ID__/approve') }}" hidden></span>
    <span id="verification-reject-url-template" data-url="{{ url('/admin/dogrulama/__ID__/reject') }}" hidden></span>
    <script src="{{ asset('assets/js/admin-verification.js') }}" defer></script>
</body>

</html>
