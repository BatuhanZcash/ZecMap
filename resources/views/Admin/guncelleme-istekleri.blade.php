<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZecMap') }} Admin — Güncelleme İstekleri</title>
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
            --ok: #4ade80;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: var(--bg); }
        #root { min-height: 100vh; }
        a { color: inherit; text-decoration: none; }

        .admin-root { min-height: 100vh; display: flex; flex-direction: column; }
        .admin-main { flex: 1; padding: 1rem; max-width: 1100px; width: 100%; margin: 0 auto; }
        @media (min-width: 768px) { .admin-main { padding: 1.5rem; } .admin-top { padding: 0.75rem 1.25rem; gap: 1rem; } }

        .admin-top {
            display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border); background: rgba(20, 21, 41, 0.95);
            backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 10; flex-wrap: wrap;
        }
        .brand { font-weight: 700; letter-spacing: 0.04em; flex-shrink: 0; }
        .admin-nav {
            display: flex; gap: 0.25rem; flex: 1; overflow-x: auto; -webkit-overflow-scrolling: touch;
            scrollbar-width: none; margin: 0 -0.5rem; padding: 0 0.5rem;
        }
        .admin-nav::-webkit-scrollbar { display: none; }
        .nav-link {
            padding: 0.6rem 0.85rem; border-radius: 0.5rem; color: var(--muted); font-weight: 600;
            white-space: nowrap; flex-shrink: 0; min-height: 40px; display: inline-flex; align-items: center; font-size: 0.92rem;
        }
        .nav-link.active { color: var(--accent); background: rgba(243, 183, 36, 0.12); }

        .stack { display: flex; flex-direction: column; gap: 1rem; }
        .gap-lg { gap: 1.25rem; }
        .row-between { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .muted { color: var(--muted); margin: 0; }

        .card { background: var(--panel); border: 1px solid var(--border); border-radius: 1rem; padding: 1.25rem 1.5rem; }
        .select, .input, textarea.input {
            border-radius: 0.65rem; border: 1px solid var(--border); background: #0f1018;
            color: var(--text); padding: 0.6rem 0.75rem; font: inherit;
        }
        .btn {
            border-radius: 0.65rem; border: 1px solid var(--border); background: #1b1c2a;
            color: var(--text); padding: 0.55rem 0.9rem; font-weight: 600; cursor: pointer;
            min-height: 40px; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
        }
        .btn.primary { background: linear-gradient(135deg, #FFC107, #FF9800); color: #111827; border: none; }
        .btn.danger { border-color: rgba(248, 113, 113, 0.4); color: var(--danger); background: transparent; }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; }

        h1 { font-size: 1.5rem; margin: 0; }
        h2 { font-size: 1.05rem; margin: 0; }

        .alert-success {
            padding: 0.75rem 1rem; border-radius: 0.65rem; background: rgba(74, 222, 128, 0.12);
            border: 1px solid rgba(74, 222, 128, 0.25); color: var(--ok); font-size: 0.9rem;
        }
        .badge { font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 999px; }

        .diff { display: grid; gap: 0.5rem; margin: 0.75rem 0; }
        .diff-row {
            display: grid; grid-template-columns: 150px 1fr; gap: 0.5rem; padding: 0.5rem 0;
            border-bottom: 1px solid var(--border); font-size: 0.9rem; align-items: start;
        }
        @media (max-width: 640px) { .diff-row { grid-template-columns: 1fr; gap: 0.15rem; } }
        .diff-label { color: var(--muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; }
        .diff-old { color: var(--muted); text-decoration: line-through; opacity: 0.7; }
        .diff-new { color: var(--text); }
        .arrow { color: var(--accent); margin: 0 0.35rem; }
        .wallet-flag {
            margin-top: 0.5rem; padding: 0.6rem 0.8rem; border-radius: 0.6rem;
            background: rgba(243, 184, 38, .12); border: 1px solid rgba(243, 184, 38, .3); font-size: 0.86rem;
        }
        .logo-preview { max-width: 120px; max-height: 120px; border-radius: 0.6rem; border: 1px solid var(--border); margin-top: 0.4rem; }
        .review-forms { display: flex; gap: 0.65rem; flex-wrap: wrap; margin-top: 0.75rem; }
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
                            <h1>Güncelleme İstekleri</h1>
                            <p class="muted" style="margin: 4px 0 0; font-size: 0.85rem;">
                                Kullanıcıların işletme bilgileri için gönderdiği güncellemeler. Onaylandığında
                                değişiklikler işletmeye uygulanır. Cüzdan adresi formdan değişmez; sahip iletişime
                                geçince manuel yapılır.
                            </p>
                        </div>
                        <form method="GET" action="{{ route('admin.guncelleme-istekleri') }}">
                            <select name="status" class="select" onchange="this.form.submit()">
                                <option value="all" @selected($status === 'all')>Tümü</option>
                                <option value="pending" @selected($status === 'pending')>Beklemede ({{ $pendingCount }})</option>
                                <option value="approved" @selected($status === 'approved')>Onaylanan</option>
                                <option value="rejected" @selected($status === 'rejected')>Reddedilen</option>
                            </select>
                        </form>
                    </div>

                    @forelse ($requests as $req)
                        @php
                            $biz = $req->business;
                            $changes = is_array($req->changes) ? $req->changes : [];
                        @endphp
                        <div class="card stack" style="gap: 0.6rem;">
                            <div class="row-between">
                                <div>
                                    <h2>
                                        @if ($biz)
                                            <a href="{{ url('/business/'.$biz->slug) }}" target="_blank" rel="noopener">{{ $biz->name }}</a>
                                        @else
                                            —
                                        @endif
                                    </h2>
                                    <p class="muted" style="font-size: 0.82rem; margin-top: 2px;">
                                        {{ $req->user?->name ?? '—' }}
                                        @if ($req->user?->email) · {{ $req->user->email }} @endif
                                        · {{ $req->created_at?->format('d.m.Y H:i') }}
                                    </p>
                                </div>
                                <span class="badge" style="{{ $req->statusBadgeStyle() }}">{{ $req->statusLabel() }}</span>
                            </div>

                            @if ($changes === [] && ! $req->proposed_logo_url && ! $req->wants_wallet_change && ! $req->message)
                                <p class="muted" style="font-size: 0.88rem;">Bu istekte önerilen bir değişiklik yok.</p>
                            @endif

                            <div class="diff">
                                @foreach ($changes as $field => $value)
                                    @if ($field === 'social_links')
                                        <div class="diff-row">
                                            <span class="diff-label">Sosyal linkler</span>
                                            <span class="diff-new">
                                                @foreach ((array) $value as $k => $v)
                                                    <div><strong>{{ ucfirst($k) }}:</strong> {{ $v }}</div>
                                                @endforeach
                                            </span>
                                        </div>
                                    @else
                                        <div class="diff-row">
                                            <span class="diff-label">{{ $fieldLabels[$field] ?? ucfirst($field) }}</span>
                                            <span>
                                                @php $old = $field === 'hours' ? null : ($biz->{$field} ?? null); @endphp
                                                @if (is_scalar($old) && trim((string) $old) !== '')
                                                    <span class="diff-old">{{ \Illuminate\Support\Str::limit((string) $old, 120) }}</span>
                                                    <span class="arrow">→</span>
                                                @endif
                                                <span class="diff-new">{{ \Illuminate\Support\Str::limit((string) $value, 240) }}</span>
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            @if ($req->proposed_logo_url)
                                <div>
                                    <span class="diff-label">Yeni görsel / logo</span><br>
                                    <img src="{{ asset($req->proposed_logo_url) }}" alt="Proposed logo" class="logo-preview">
                                </div>
                            @endif

                            @if ($req->wants_wallet_change)
                                <div class="wallet-flag">
                                    ⚠️ Kullanıcı <strong>ödeme (ZEC) adresini</strong> güncellemek istiyor. Adres formdan
                                    gelmez. Sahibi işletmenin kayıtlı mail/web/Telegram'ından doğrulayınca adresi
                                    <strong>İşletmeler</strong> ekranından manuel değiştir.
                                </div>
                            @endif

                            @if ($req->message)
                                <div>
                                    <span class="diff-label">Mesaj</span>
                                    <p style="margin: 2px 0 0; white-space: pre-wrap; font-size: 0.9rem;">{{ $req->message }}</p>
                                </div>
                            @endif

                            @if ($req->admin_note)
                                <p class="muted" style="font-size: 0.85rem;"><strong>Admin notu:</strong> {{ $req->admin_note }}</p>
                            @endif

                            @if ($req->isPending())
                                <div class="review-forms">
                                    <form method="POST" action="{{ route('admin.guncelleme-istekleri.approve', $req->id) }}"
                                        style="display: flex; gap: 0.5rem; flex: 1; min-width: 260px; flex-wrap: wrap;">
                                        @csrf
                                        <input type="hidden" name="redirect_status" value="{{ $status }}">
                                        <input type="text" name="admin_note" class="input" placeholder="Not (isteğe bağlı)"
                                            style="flex: 1; min-width: 140px;">
                                        <button type="submit" class="btn primary">Onayla ve uygula</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.guncelleme-istekleri.reject', $req->id) }}">
                                        @csrf
                                        <input type="hidden" name="redirect_status" value="{{ $status }}">
                                        <button type="submit" class="btn danger">Reddet</button>
                                    </form>
                                </div>
                            @else
                                <p class="muted" style="font-size: 0.82rem;">
                                    İncelendi: {{ $req->reviewed_at?->format('d.m.Y H:i') }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="card">
                            <p class="muted" style="text-align: center;">Güncelleme isteği bulunamadı.</p>
                        </div>
                    @endforelse
                </div>
            </main>
        </div>
    </div>
</body>

</html>
