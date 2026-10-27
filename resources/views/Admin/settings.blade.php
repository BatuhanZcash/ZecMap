<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Ayarlar</title>
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
            padding: 1rem;
        }

        @media (min-width: 768px) {
            .card {
                padding: 1.25rem 1.5rem;
            }
        }

        .muted {
            color: var(--muted);
            margin: 0;
        }

        .error {
            color: var(--danger);
            margin: 0;
            font-size: 0.85rem;
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

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        h1 {
            margin: 0 0 0.5rem;
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

        .settings-textarea {
            width: 100%;
            background: rgb(15, 16, 24);
            border: 1px solid var(--border);
            border-radius: 0.65rem;
            color: var(--accent);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.8rem;
            padding: 0.65rem 0.75rem;
            resize: vertical;
            outline: none;
            line-height: 1.6;
            transition: border-color 0.2s ease;
        }

        .settings-textarea:focus {
            border-color: rgba(255, 193, 7, 0.45);
        }

        .settings-textarea.is-invalid {
            border-color: rgba(248, 113, 113, 0.6);
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
                            <h1 style="margin-bottom: 4px;">Ayarlar</h1>
                            <p class="muted" style="font-size: 0.88rem;">
                                Buradan yapılan değişiklikler anında siteye yansır.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.settings.update') }}" class="stack">
                        @csrf
                        @method('PUT')

                        <div class="card stack" style="gap: 0.75rem;">
                            <div>
                                <p style="margin: 0; font-weight: 700; font-size: 0.95rem;">Destek Sayfası Zcash Adresi
                                </p>
                                <p class="muted" style="margin: 2px 0 0; font-size: 0.82rem;">
                                    {{ route('support') }} sayfasındaki bağış adresini buradan güncelleyebilirsiniz.
                                </p>
                            </div>

                            <textarea id="donation_address" name="donation_address" rows="3" required maxlength="2000"
                                @class([
                                    'settings-textarea',
                                    'is-invalid' => $errors->has('donation_address'),
                                ])>{{ old('donation_address', $donationAddress) }}</textarea>

                            @error('donation_address')
                                <p class="error">{{ $message }}</p>
                            @enderror

                            <div class="row-between" style="flex-wrap: nowrap; gap: 0.75rem;">
                                <p class="muted" style="font-size: 0.78rem; margin: 0;">
                                    @if ($donationUpdatedAt)
                                        Son güncelleme:
                                        {{ $donationUpdatedAt->timezone(config('app.timezone'))->format('d M Y H:i') }}
                                    @else
                                        Henüz kaydedilmedi
                                    @endif
                                </p>
                                <button type="submit" class="btn primary"
                                    style="min-width: 110px; font-size: 0.88rem;">
                                    Kaydet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <script type="module" src="/src/main.tsx"></script>
</body>

</html>
