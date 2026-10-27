<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Yorumlar</title>
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

        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1rem;
        }

        .muted {
            color: var(--muted);
            margin: 0;
        }

        .select,
        .input {
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
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn.primary {
            background: linear-gradient(135deg, #FFC107, #FF9800);
            color: #111827;
            border: none;
        }

        .btn.ghost {
            background: transparent;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            display: block;
            overflow-x: auto;
            white-space: nowrap;
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

        .review-stars {
            color: var(--accent);
            letter-spacing: 0.05em;
        }

        .review-image-preview {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 0.65rem;
            border: 1px solid var(--border);
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
                            <h1 style="margin: 0;">Yorum Moderasyonu</h1>
                            <p class="muted" style="margin: 4px 0 0; font-size: 0.85rem;">
                                Kullanıcıların gönderdiği işletme yorumları — onaylandıktan sonra yayınlanır.
                            </p>
                        </div>
                        <form method="GET" action="{{ route('admin.yorumlar') }}">
                            <select name="status" class="select" onchange="this.form.submit()">
                                <option value="all" @selected($status === 'all')>Tümü</option>
                                <option value="pending" @selected($status === 'pending')>
                                    Beklemede ({{ $pendingCount }})
                                </option>
                                <option value="approved" @selected($status === 'approved')>Onaylanan</option>
                            </select>
                        </form>
                    </div>

                    <div class="card" style="padding: 0; overflow: hidden;">
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>İşletme</th>
                                        <th>Gönderen</th>
                                        <th>Puan</th>
                                        <th>Durum</th>
                                        <th>Tarih</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reviews as $review)
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600;">{{ $review->business?->name ?? '—' }}</div>
                                            </td>
                                            <td>{{ $review->user?->name ?? '—' }}</td>
                                            <td>
                                                <span class="review-stars" aria-label="{{ $review->rating }} / 5">
                                                    {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 999px; {{ $review->statusBadgeStyle() }}">
                                                    {{ $review->statusLabel() }}
                                                </span>
                                            </td>
                                            <td class="muted" style="font-size: 0.8rem; white-space: nowrap;">
                                                {{ $review->created_at?->format('d.m.Y H:i:s') }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn ghost btn-review-moderation"
                                                    data-id="{{ $review->id }}" style="font-size: 0.8rem;">İncele</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6"
                                                style="padding: 2rem 1rem; text-align: center; color: var(--muted);">
                                                Yorum bulunamadı.
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

    <div id="review-moderation-drawer" class="verification-drawer" aria-hidden="true">
        <button type="button" class="verification-drawer-backdrop" data-close-drawer
            aria-label="Kapat"></button>
        <aside class="card stack verification-drawer-panel">
            <div class="row-between">
                <h2 id="review-moderation-title" style="margin: 0; font-size: 1.1rem;">Yorum İncelemesi</h2>
                <button type="button" class="btn ghost" data-close-drawer>Kapat</button>
            </div>

            <div class="stack" style="gap: 0.75rem;">
                <div>
                    <p class="muted"
                        style="margin: 0 0 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        İşletme</p>
                    <p id="review-business-name" style="margin: 0; font-weight: 700;">—</p>
                    <p id="review-business-slug" class="muted" style="margin: 4px 0 0; font-size: 0.82rem;">—</p>
                </div>

                <div>
                    <p class="muted"
                        style="margin: 0 0 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        Gönderen</p>
                    <p id="review-user-name" style="margin: 0;">—</p>
                    <p id="review-user-email" class="muted" style="margin: 4px 0 0; font-size: 0.82rem;">—</p>
                </div>

                <div>
                    <p class="muted"
                        style="margin: 0 0 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        Puan</p>
                    <p id="review-rating" class="review-stars" style="margin: 0;">—</p>
                </div>

                <div id="review-community-wrap" hidden>
                    <p class="muted"
                        style="margin: 0 0 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        Topluluk</p>
                    <p id="review-community" style="margin: 0;">—</p>
                </div>

                <div>
                    <p class="muted"
                        style="margin: 0 0 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        Yorum</p>
                    <p id="review-body" style="margin: 0; white-space: pre-wrap; font-size: 0.9rem;">—</p>
                </div>

                <div id="review-image-wrap" hidden>
                    <p class="muted"
                        style="margin: 0 0 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        Fotoğraf</p>
                    <img id="review-image" class="review-image-preview" src="" alt="Yorum fotoğrafı">
                </div>

                <div id="review-reviewed-meta" hidden>
                    <p class="muted"
                        style="margin: 0 0 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        İnceleme</p>
                    <p id="review-reviewed-at" class="muted" style="margin: 0; font-size: 0.82rem;">—</p>
                </div>

                <form id="review-approve-form" method="POST" action="#" class="stack" style="gap: 0.75rem;">
                    @csrf
                    <input type="hidden" name="redirect_status" value="{{ $status }}">
                    <div>
                        <label class="muted"
                            style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Admin notu
                            (isteğe bağlı)</label>
                        <textarea id="review-admin-note" name="admin_note" class="input" rows="3"
                            placeholder="Onay veya red notu…" style="width: 100%; margin-top: 6px; resize: vertical;"></textarea>
                    </div>
                    <div style="display: flex; gap: 0.65rem;">
                        <button type="submit" class="btn primary" style="flex: 1;">Onayla</button>
                        <button type="button" id="review-reject-btn" class="btn ghost"
                            style="flex: 1; border-color: rgba(248, 113, 113, 0.4); color: rgb(248, 113, 113);">
                            Reddet
                        </button>
                    </div>
                </form>

                <form id="review-reject-form" method="POST" action="#" hidden>
                    @csrf
                    <input type="hidden" name="redirect_status" value="{{ $status }}">
                    <input type="hidden" name="admin_note" id="review-reject-note" value="">
                </form>
            </div>
        </aside>
    </div>

    <script type="application/json" id="review-moderation-data">@json($reviewsData)</script>
    <span id="review-approve-url-template" data-url="{{ url('/admin/yorumlar/__ID__/approve') }}" hidden></span>
    <span id="review-reject-url-template" data-url="{{ url('/admin/yorumlar/__ID__/reject') }}" hidden></span>
    <script src="{{ asset('assets/js/admin-reviews.js') }}" defer></script>
</body>

</html>
