<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Destekleyenler</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
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
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
        }

        .admin-nav {
            display: flex;
            gap: 0.25rem;
            flex: 1;
            overflow-x: auto;
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

        .brand .text-primary {
            color: var(--accent);
        }

        .brand .text-on-surface {
            color: var(--text);
        }

        .brand .font-headline {
            display: inline-flex;
            align-items: baseline;
            font-size: 20px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .brand svg {
            flex-shrink: 0;
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
            padding: 0;
            overflow: hidden;
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
            padding: 0.65rem 0.75rem;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }

        .table th {
            color: var(--muted);
            font-weight: 600;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        h1 {
            margin: 0 0 0.5rem;
            font-size: 1.5rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid var(--border);
        }

        .status-badge--active {
            color: rgb(74, 222, 128);
            border-color: rgba(74, 222, 128, 0.25);
            background: rgba(74, 222, 128, 0.12);
        }

        .status-badge--inactive {
            color: var(--muted);
            background: rgba(255, 255, 255, 0.04);
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

        .alert-error ul {
            margin: 0.35rem 0 0;
            padding-left: 1.1rem;
        }

        .site-drawer {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 40;
        }

        .site-drawer.is-open {
            display: flex;
        }

        .site-drawer-backdrop {
            flex: 1;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            border: none;
            cursor: pointer;
        }

        .site-drawer-panel {
            width: 100%;
            max-width: 520px;
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

        .field-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
        }

        .icon-preview-wrap {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .icon-preview {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid var(--border);
        }

        .icon-placeholder {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            border: 1px dashed var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 0.75rem;
        }

        .empty-row td {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--muted);
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

                    @if ($errors->any())
                        <div class="alert-error" role="alert">
                            <strong>Kaydedilemedi.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row-between">
                        <div>
                            <h1 style="margin-bottom: 4px;">Destekleyenler</h1>
                            <p class="muted" style="font-size: 0.88rem;">
                                {{ route('support') }} sayfasındaki destekleyenler bölümünü buradan yönetin.
                            </p>
                        </div>
                        <button type="button" class="btn primary" data-open-create-supporter>+ Yeni
                            Destekleyen</button>
                    </div>

                    <div class="card">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>İkon</th>
                                    <th>Ad</th>
                                    <th>Etiket</th>
                                    <th>Sıra</th>
                                    <th>Durum</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($supporters as $supporter)
                                    <tr>
                                        <td>
                                            @if ($supporter->iconDisplayUrl())
                                                <img alt="" src="{{ $supporter->iconDisplayUrl() }}"
                                                    style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover;">
                                            @else
                                                <span class="icon-placeholder"
                                                    style="width: 32px; height: 32px; font-size: 0.65rem;">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;">{{ $supporter->name }}</div>
                                            <div class="muted"
                                                style="font-size: 0.78rem; margin-top: 2px; max-width: 280px; white-space: normal;">
                                                {{ $supporter->description }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($supporter->hasLink())
                                                <a href="{{ $supporter->link_url }}" target="_blank" rel="noreferrer"
                                                    style="color: var(--accent); font-size: 0.82rem;">
                                                    {{ $supporter->link_label }}
                                                </a>
                                            @else
                                                <span class="muted" style="font-size: 0.82rem;">{{ $supporter->link_label }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $supporter->sort_order }}</td>
                                        <td>
                                            <span @class([
                                                'status-badge',
                                                'status-badge--active' => $supporter->isActive(),
                                                'status-badge--inactive' => !$supporter->isActive(),
                                            ])>
                                                {{ $supporter->statusLabel() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                                                <button type="button" class="btn ghost" data-edit-supporter
                                                    data-id="{{ $supporter->id }}">Düzenle</button>
                                                <form method="POST"
                                                    action="{{ route('admin.supporters.destroy', $supporter) }}"
                                                    data-delete-supporter>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn ghost danger">Sil</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="6">Henüz destekleyen eklenmemiş.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="supporter-edit-drawer" class="site-drawer" aria-hidden="true">
        <button type="button" class="site-drawer-backdrop" data-close-drawer aria-label="Kapat"></button>
        <aside class="site-drawer-panel">
            <div
                style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                <h2 id="supporter-edit-title" style="margin: 0; font-size: 1.1rem;">Yeni Destekleyen</h2>
                <button type="button" class="btn ghost" data-close-drawer aria-label="Kapat">✕</button>
            </div>

            <form id="supporter-edit-form" method="POST" action="{{ route('admin.supporters.store') }}"
                enctype="multipart/form-data" style="display: flex; flex-direction: column; flex: 1;">
                @csrf
                <input type="hidden" name="_method" id="supporter-form-method" value="{{ old('_method', 'POST') }}">
                <input type="hidden" name="supporter_id" id="supporter-form-id" value="{{ old('supporter_id') }}">

                <div style="padding: 1rem 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label class="field-label" for="field-name">Ad *</label>
                        <input id="field-name" name="name" class="form-control" required maxlength="255"
                            value="{{ old('name') }}">
                    </div>

                    <div>
                        <label class="field-label" for="field-description">Açıklama *</label>
                        <textarea id="field-description" name="description" rows="3" class="form-control" required maxlength="1000">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="field-label" for="field-link_label">Etiket *</label>
                        <input id="field-link_label" name="link_label" class="form-control" required maxlength="100"
                            placeholder="Bağış, Destek, Wiki…" value="{{ old('link_label') }}">
                    </div>

                    <div>
                        <label class="field-label" for="field-link_url">Bağlantı URL (isteğe bağlı)</label>
                        <input id="field-link_url" name="link_url" class="form-control" maxlength="500"
                            placeholder="https://example.com" value="{{ old('link_url') }}">
                    </div>

                    <div>
                        <label class="field-label" for="field-sort_order">Sıra</label>
                        <input id="field-sort_order" name="sort_order" type="number" min="0" max="9999"
                            class="form-control" value="{{ old('sort_order', 0) }}">
                    </div>

                    <div>
                        <label class="field-label" for="field-status">Durum</label>
                        <select id="field-status" name="status" class="form-control">
                            <option value="active" @selected(old('status', 'active') === 'active')>Yayında</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>Pasif</option>
                        </select>
                    </div>

                    <div>
                        <label class="field-label">İkon</label>
                        <div class="icon-preview-wrap">
                            <img id="supporter-icon-preview" class="icon-preview" alt="" hidden>
                            <div id="supporter-icon-placeholder" class="icon-placeholder">İkon yok</div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <input id="supporter-icon-input" name="icon" type="file" accept="image/*"
                                    class="form-control">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                                    <input type="checkbox" id="supporter-remove-icon" name="remove_icon"
                                        value="1">
                                    İkonu kaldır
                                </label>
                            </div>
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

    @php
        $supporterFormFlash = [
            'open' => $errors->any() || old('name') !== null,
            'method' => old('_method', 'POST'),
            'supporter_id' => old('supporter_id'),
            'values' => [
                'name' => old('name'),
                'description' => old('description'),
                'link_label' => old('link_label'),
                'link_url' => old('link_url'),
                'sort_order' => old('sort_order', 0),
                'status' => old('status', 'active'),
            ],
        ];
    @endphp

    <script type="application/json" id="supporters-data">@json($supportersData)</script>
    <script type="application/json" id="supporter-form-flash">@json($supporterFormFlash)</script>
    <span id="supporter-update-url-template" data-url="{{ url('/admin/supporters/__ID__') }}" hidden></span>
    <span id="supporter-store-url" data-url="{{ route('admin.supporters.store') }}" hidden></span>

    <script src="{{ asset('assets/js/admin-supporters.js') }}" defer></script>
</body>

</html>
