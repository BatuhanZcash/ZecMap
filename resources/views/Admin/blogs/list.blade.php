<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Blog</title>
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
                <div>
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <div>
                            <h1 style="margin: 0px; font-size: 1.4rem; font-weight: 700;">Blog Yazıları</h1>
                            <p style="margin: 4px 0px 0px; color: var(--muted); font-size: 0.85rem;">Blog içeriklerini
                                yönetin ve yayınlayın</p>
                        </div>
                        <a class="btn" href="{{ route('admin.blogs.create') }}">+ Yeni Yazı</a>
                    </div>
                    @if (session('success'))
                        <div class="blog-editor-error"
                            style="margin-bottom: 1rem; color: #86efac; background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.25);">
                            {{ session('success') }}
                        </div>
                    @endif
                    @php
                        $filters = [
                            null => 'tümü',
                            'published' => 'yayında',
                            'draft' => 'taslak',
                        ];
                    @endphp
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                        @foreach ($filters as $filterValue => $filterLabel)
                            <a href="{{ route('admin.blogs.list', $filterValue ? ['status' => $filterValue] : []) }}"
                                @class([
                                    'btn',
                                    'ghost' => ($status ?? null) !== $filterValue,
                                    'active' => ($status ?? null) === $filterValue,
                                ])
                                style="text-transform: capitalize;">{{ $filterLabel }}</a>
                        @endforeach
                    </div>
                    <div class="card" style="padding: 0px; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <th
                                        style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted);">
                                        Başlık</th>
                                    <th
                                        style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted);">
                                        Durum</th>
                                    <th
                                        style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted);">
                                        Yayınlanma</th>
                                    <th
                                        style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted);">
                                        Oluşturulma</th>
                                    <th
                                        style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted);">
                                        İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posts as $post)
                                    <tr style="border-bottom: 1px solid var(--border);">
                                        <td style="padding: 0.85rem 1rem; vertical-align: top;">
                                            <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                                                @if ($post->featuredImageUrl())
                                                    <img src="{{ $post->featuredImageUrl() }}" alt=""
                                                        style="width: 56px; height: 56px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); flex-shrink: 0;">
                                                @endif
                                                <div>
                                                    <div style="font-weight: 600;">{{ $post->title }}</div>
                                                    @if ($post->excerpt)
                                                        <div style="font-size: 0.78rem; color: var(--muted); margin-top: 2px;">
                                                            {{ \Illuminate\Support\Str::limit($post->excerpt, 120) }}
                                                        </div>
                                                    @endif
                                                    <div style="font-size: 0.72rem; color: var(--muted); margin-top: 2px;">
                                                        /{{ $post->slug }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 0.85rem 1rem; vertical-align: top;">
                                            @if ($post->isPublished())
                                                <span
                                                    style="padding: 2px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; background: rgba(34, 197, 94, 0.15); color: rgb(74, 222, 128);">{{ $post->statusLabel() }}</span>
                                            @else
                                                <span
                                                    style="padding: 2px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; background: rgba(156, 163, 175, 0.15); color: rgb(209, 213, 219);">{{ $post->statusLabel() }}</span>
                                            @endif
                                        </td>
                                        <td
                                            style="padding: 0.85rem 1rem; vertical-align: top; color: var(--muted); font-size: 0.82rem;">
                                            {{ $post->published_at?->format('d.m.Y') ?? '—' }}
                                        </td>
                                        <td
                                            style="padding: 0.85rem 1rem; vertical-align: top; color: var(--muted); font-size: 0.82rem;">
                                            {{ $post->created_at->format('d.m.Y') }}
                                        </td>
                                        <td style="padding: 0.85rem 1rem; vertical-align: top;">
                                            <div style="display: flex; gap: 0.4rem;">
                                                <a class="btn ghost"
                                                    href="{{ route('admin.blogs.edit', $post->id) }}"
                                                    style="font-size: 0.78rem; padding: 4px 10px;">Düzenle</a>
                                                @if ($post->isPublished())
                                                    <form method="POST"
                                                        action="{{ route('admin.blogs.unpublish', $post->id) }}"
                                                        data-confirm-unpublish>
                                                        @csrf
                                                        <input type="hidden" name="redirect_status"
                                                            value="{{ $status }}">
                                                        <button type="submit" class="btn ghost"
                                                            style="font-size: 0.78rem; padding: 4px 10px;">Yayından
                                                            Kaldır</button>
                                                    </form>
                                                @endif
                                                <form method="POST"
                                                    action="{{ route('admin.blogs.destroy', $post->id) }}"
                                                    data-delete-blog>
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="redirect_status"
                                                        value="{{ $status }}">
                                                    <button type="submit" class="btn ghost"
                                                        style="font-size: 0.78rem; padding: 4px 10px; color: var(--danger);">Sil</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            style="padding: 2rem 1rem; text-align: center; color: var(--muted);">
                                            Henüz blog yazısı yok.
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

    <script>
        document.querySelectorAll('[data-confirm-unpublish]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!window.confirm('Bu yazıyı yayından kaldırmak istediğinize emin misiniz?')) {
                    event.preventDefault();
                }
            });
        });

        document.querySelectorAll('[data-delete-blog]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!window.confirm('Bu blog yazısını kalıcı olarak silmek istediğinize emin misiniz?')) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>

</html>
