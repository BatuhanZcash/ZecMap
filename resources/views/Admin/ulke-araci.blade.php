<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ZecMap') }} Admin — Ülke Aracı</title>
    <style type="text/css">
        :root {
            color-scheme: dark; --bg:#0b0c14; --panel:#141529; --border:rgba(255,255,255,.08);
            --text:#e5e7eb; --muted:#9ca3af; --accent:#FFC107;
            font-family: system-ui,-apple-system,'Segoe UI',Roboto,sans-serif; background:var(--bg); color:var(--text);
        }
        * { box-sizing:border-box; } body{ margin:0; min-height:100vh; background:var(--bg); } #root{ min-height:100vh; }
        a{ color:inherit; text-decoration:none; }
        .admin-root{ min-height:100vh; display:flex; flex-direction:column; }
        .admin-main{ flex:1; padding:1rem; max-width:1000px; width:100%; margin:0 auto; }
        @media (min-width:768px){ .admin-main{ padding:1.5rem; } .admin-top{ padding:.75rem 1.25rem; gap:1rem; } }
        .admin-top{ display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; border-bottom:1px solid var(--border);
            background:rgba(20,21,41,.95); backdrop-filter:blur(12px); position:sticky; top:0; z-index:10; flex-wrap:wrap; }
        .brand{ font-weight:700; letter-spacing:.04em; flex-shrink:0; }
        .admin-nav{ display:flex; gap:.25rem; flex:1; overflow-x:auto; scrollbar-width:none; margin:0 -.5rem; padding:0 .5rem; }
        .admin-nav::-webkit-scrollbar{ display:none; }
        .nav-link{ padding:.6rem .85rem; border-radius:.5rem; color:var(--muted); font-weight:600; white-space:nowrap;
            flex-shrink:0; min-height:40px; display:inline-flex; align-items:center; font-size:.92rem; }
        .nav-link.active{ color:var(--accent); background:rgba(243,183,36,.12); }
        .stack{ display:flex; flex-direction:column; gap:1.25rem; }
        .card{ background:var(--panel); border:1px solid var(--border); border-radius:1rem; padding:1.5rem; }
        h1{ font-size:1.5rem; margin:0 0 .4rem; } h2{ font-size:1.05rem; margin:0 0 .6rem; }
        .muted{ color:var(--muted); margin:0; line-height:1.6; }
        .grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:1rem; margin:1rem 0; }
        .stat{ background:#0f1018; border:1px solid var(--border); border-radius:.8rem; padding:1rem; }
        .stat b{ display:block; font-size:1.8rem; font-weight:700; }
        .stat span{ color:var(--muted); font-size:.85rem; }
        .btn{ border-radius:.65rem; border:none; background:linear-gradient(135deg,#FFC107,#FF9800); color:#111827;
            padding:.8rem 1.4rem; font-weight:700; cursor:pointer; min-height:44px; font-size:1rem; }
        .alert-success{ padding:.75rem 1rem; border-radius:.65rem; background:rgba(74,222,128,.12);
            border:1px solid rgba(74,222,128,.25); color:rgb(74,222,128); font-size:.95rem; }
    </style>
</head>

<body>
    <div id="root">
        <div class="admin-root">
            @include('Admin.layouts.header')
            <main class="admin-main">
                <div class="stack">
                    @if (session('success'))
                        <div class="alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="card">
                        <h1>Ülke Aracı</h1>
                        <p class="muted">
                            Yerel (Flexa olmayan) işletmeleri koordinatlarından ülkelerine atar ve işletmesi olan her
                            ülke için otomatik bir topluluk kaydı oluşturur. Böylece ülke sayfaları ve dünya haritası
                            o ülkeleri de gösterir. Flexa işletmelerine ve zaten ülke kodu olanlara dokunmaz.
                        </p>

                        <div class="grid">
                            <div class="stat"><b>{{ number_format($total) }}</b><span>yerel işletme</span></div>
                            <div class="stat"><b>{{ number_format($withCode) }}</b><span>ülkesi atanmış</span></div>
                            <div class="stat"><b>{{ number_format($missing) }}</b><span>atanacak (koordinatlı)</span></div>
                            <div class="stat"><b>{{ number_format($countryCount) }}</b><span>ülke</span></div>
                            <div class="stat"><b>{{ number_format($missingCommunities) }}</b><span>eksik topluluk</span></div>
                        </div>

                        <form method="POST" action="{{ route('admin.ulke-araci.run') }}">
                            @csrf
                            <button type="submit" class="btn">Ülkeleri ata ve toplulukları oluştur</button>
                        </form>
                        @if ($noCoords > 0)
                            <p class="muted" style="margin-top:.8rem; font-size:.85rem;">
                                Not: {{ $noCoords }} işletmenin koordinatı yok (çoğu online servis), onlar atanmaz.
                            </p>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>
