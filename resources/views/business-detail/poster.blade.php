<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $business->name }} — Accept Zcash poster</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap">
    <style>
        :root { --navy:#00051C; --cream:#F9F7F2; --gold:#F3B826; --ink:#01061C; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { background:#e9e7e1; font-family:"Inter",system-ui,sans-serif; color:var(--ink);
            display:flex; flex-direction:column; align-items:center; padding:28px 16px 60px; }
        .toolbar { width:100%; max-width:720px; display:flex; justify-content:space-between; align-items:center;
            gap:12px; margin-bottom:20px; flex-wrap:wrap; }
        .toolbar p { font-size:14px; color:#5a5c66; }
        .btn { background:var(--gold); color:var(--ink); border:0; border-radius:12px; padding:12px 22px;
            font:700 15px "Inter"; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
        .poster { width:100%; max-width:720px; aspect-ratio:1 / 1.414; background:var(--navy); color:var(--cream);
            border-radius:22px; padding:7% 9%; display:flex; flex-direction:column; align-items:center; text-align:center;
            box-shadow:0 24px 70px rgba(0,0,0,.28); overflow:hidden; position:relative; }
        .brand { display:flex; align-items:center; gap:10px; font:700 22px "Poppins"; letter-spacing:.3px; }
        .brand .z-logo { width:40px; height:40px; object-fit:contain; display:block; }
        .brand .gold { color:var(--gold); }
        .kicker { margin-top:auto; font:600 15px "Inter"; letter-spacing:3px; text-transform:uppercase; color:var(--gold); opacity:.95; }
        .headline { font:800 clamp(34px,8vw,60px) "Poppins"; line-height:1.02; margin-top:10px; }
        .headline .gold { color:var(--gold); }
        .bizname { margin-top:14px; font:600 clamp(16px,3.4vw,22px) "Inter"; color:#d7d9e2; max-width:90%; }
        .qrwrap { background:#fff; border-radius:20px; padding:18px; margin:7% 0; width:min(56%,320px); aspect-ratio:1;
            display:grid; place-items:center; }
        .qrwrap svg { display:block; width:100%; height:100%; }
        .scan { font:600 15px "Inter"; color:#cfd2dc; }
        .scan b { color:var(--cream); }
        .foot { margin-top:auto; display:flex; flex-direction:column; gap:3px; }
        .foot .site { font:700 17px "Poppins"; color:var(--gold); }
        .foot .tag { font:400 13px "Inter"; color:#aeb1bd; }

        @media print {
            @page { size:A4; margin:0; }
            body { background:#fff; padding:0; display:block; }
            .toolbar { display:none; }
            .poster { max-width:none; width:100vw; height:100vh; border-radius:0; box-shadow:none;
                aspect-ratio:auto; page-break-after:avoid; }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <p>Print this poster or save it as a PDF, then display it at your business.</p>
        <button type="button" class="btn" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Print / Save as PDF
        </button>
    </div>

    <div class="poster">
        <div class="brand"><img src="{{ asset('assets/images/logo-icon.png') }}" alt="Zcash" class="z-logo"><span><span class="gold">zec</span>map</span></div>

        <p class="kicker">We accept</p>
        <h1 class="headline"><span class="gold">Zcash</span> here</h1>
        <p class="bizname">{{ $business->name }}</p>

        <div class="qrwrap" id="poster-qr" aria-label="Zcash payment QR"></div>

        <p class="scan">Scan with any Zcash wallet · <b>private, instant, low fee</b></p>

        <div class="foot">
            <span class="site">zecmap.com</span>
            <span class="tag">Find places that accept Zcash</span>
        </div>
    </div>

    <script src="{{ asset('assets/js/qrcode.min.js') }}"></script>
    <script>
        (function () {
            var uri = @json($zecUri);
            try {
                var qr = qrcode(0, 'M'); qr.addData(uri); qr.make();
                document.getElementById('poster-qr').innerHTML = qr.createSvgTag({ cellSize: 6, margin: 0, scalable: true });
            } catch (e) {}
        })();
    </script>
</body>

</html>
