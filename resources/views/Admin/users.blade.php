<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Kullanıcılar</title>
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
    </style>
</head>

<body cz-shortcut-listen="true">
    <div id="root">
        <div class="admin-root">
            @include('Admin.layouts.header')
            <main class="admin-main">
                <div class="stack gap-lg">
                    <div class="row-between">
                        <div>
                            <h1 style="margin-bottom: 4px;">Kullanıcılar</h1>
                            <p class="muted" style="font-size: 0.88rem;">
                                Toplam {{ $totalUsers }} kullanıcı · {{ $adminCount }} admin
                            </p>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('admin.users') }}" class="row-between"
                        style="gap: 0.5rem; flex-wrap: wrap;">
                        @if ($role)
                            <input type="hidden" name="role" value="{{ $role }}">
                        @endif
                        <input name="search" placeholder="Ad veya e-posta ara…" type="search"
                            value="{{ $search }}"
                            style="flex: 1 1 220px; background: rgb(15, 16, 24); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 0.65rem; color: var(--text); padding: 0.6rem 0.75rem; font: inherit; font-size: 0.9rem; outline: none;">
                        <div style="display: flex; gap: 0.4rem;">
                            @php
                                $roleFilters = [
                                    null => 'Tümü',
                                    'user' => 'Kullanıcı',
                                    'admin' => 'Admin',
                                ];
                            @endphp
                            @foreach ($roleFilters as $filterValue => $filterLabel)
                                <a href="{{ route('admin.users', array_filter(['role' => $filterValue, 'search' => $search ?: null])) }}"
                                    @class([
                                        'btn',
                                        'primary' => ($role ?? null) === $filterValue,
                                    ])
                                    style="min-height: 38px; font-size: 0.85rem; padding: 0 0.9rem;">{{ $filterLabel }}</a>
                            @endforeach
                        </div>
                    </form>
                    <div class="card" style="padding: 0px; overflow: hidden;">
                        <table class="table" style="display: table; white-space: normal;">
                            <thead>
                                <tr>
                                    <th>Kullanıcı</th>
                                    <th>Kullanıcı Adı</th>
                                    <th>Rol</th>
                                    <th>İşletme</th>
                                    <th>Son Giriş</th>
                                    <th>Kayıt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.6rem;">
                                                <span
                                                    style="width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; background: rgba(255, 193, 7, 0.15); color: var(--accent); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.82rem;">{{ $user->initials() }}</span>
                                                <span style="font-size: 0.9rem;">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td style="font-size: 0.85rem; color: var(--muted); font-family: monospace;">
                                            {{ '@' . $user->username }}
                                        </td>
                                        <td>
                                            @if ($user->isAdmin())
                                                <span
                                                    style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: rgba(243, 183, 36, 0.18); color: var(--accent); text-transform: uppercase; letter-spacing: 0.05em;">admin</span>
                                            @else
                                                <span
                                                    style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: rgba(255, 255, 255, 0.06); color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;">user</span>
                                            @endif
                                        </td>
                                        <td style="font-size: 0.9rem; text-align: center;">
                                            <span class="muted">—</span>
                                        </td>
                                        <td style="font-size: 0.82rem; color: var(--muted); white-space: nowrap;">
                                            {{ $user->last_sign_in_at?->format('d M Y') ?? '—' }}
                                        </td>
                                        <td style="font-size: 0.82rem; color: var(--muted); white-space: nowrap;">
                                            {{ $user->created_at->format('d M Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            style="padding: 2rem 1rem; text-align: center; color: var(--muted);">
                                            Kullanıcı bulunamadı.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>
