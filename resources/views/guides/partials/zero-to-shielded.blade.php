{{-- Zero to Shielded: embeddable version. Use inside the Zecmap layout: @include('guides.partials.zero-to-shielded')
     It follows Zecmap's html.dark class and hides its own header/theme button. All CSS is scoped to .zts. --}}
<script>window.ZTS_BASE = "{{ asset('guides/zero-to-shielded') }}/";</script>
@verbatim
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&family=Geist+Mono:wght@400;500&display=swap">
<style>
/* Zero to Shielded: Zecmap palette (cream #F9F7F2 / navy #00051C, gold #F3B826). Everything is scoped to .zts so it can live inside the Zecmap layout.
   Theme: html.dark / html.light (Zecmap's own switch) wins; otherwise the OS preference, except when embedded (data-embed) where Zecmap decides. */
.zts{
  --bg:#F9F7F2; --soft:#FFFFFF; --card:rgba(255,255,255,.72); --ink:#00051C; --muted:#5F626F; --line:rgba(0,5,28,.10);
  --gold:#9A6A00; --gold-fill:#F3B826; --gold-soft:rgba(243,184,38,.16); --on-gold:#01061C; --grad:linear-gradient(90deg,#F3B826 0%,#F3CC83 100%);
  --pub:#2F5F8A; --pub-soft:rgba(47,95,138,.10); --ok:#1F8A5B; --warn:#C2410C;
  --hdr-bg:#01061C; --hdr-ink:#FFFFFF; --hdr-line:rgba(255,255,255,.14); --shadow:0 1px 2px rgba(0,5,28,.04),0 6px 20px rgba(0,5,28,.06); color-scheme:light;
  --sans:"Inter", "Segoe UI", system-ui, -apple-system, sans-serif;
  --head:"Poppins", "Inter", system-ui, sans-serif;
  --mono:"Geist Mono", ui-monospace, "SFMono-Regular", Menlo, monospace;
  --sticky-top:0px;
}
@media (prefers-color-scheme: dark){html:not(.light):not([data-theme="light"]) .zts:not([data-embed]){
  --bg:#00051C; --soft:#071031; --card:rgba(7,16,49,.45); --ink:#FFFFFF; --muted:#CCCDD1; --line:rgba(255,255,255,.10);
  --gold:#F3B826; --gold-fill:#F3B826; --gold-soft:rgba(243,184,38,.13); --on-gold:#01061C;
  --pub:#8FB8E0; --pub-soft:rgba(143,184,224,.12); --ok:#34D399; --warn:#F08A5D;
  --hdr-bg:#FFFFFF; --hdr-ink:#000000; --hdr-line:rgba(0,5,28,.12); --shadow:0 4px 24px rgba(0,0,0,.35); color-scheme:dark;}}
html.dark .zts,html[data-theme="dark"] .zts{
  --bg:#00051C; --soft:#071031; --card:rgba(7,16,49,.45); --ink:#FFFFFF; --muted:#CCCDD1; --line:rgba(255,255,255,.10);
  --gold:#F3B826; --gold-fill:#F3B826; --gold-soft:rgba(243,184,38,.13); --on-gold:#01061C;
  --pub:#8FB8E0; --pub-soft:rgba(143,184,224,.12); --ok:#34D399; --warn:#F08A5D;
  --hdr-bg:#FFFFFF; --hdr-ink:#000000; --hdr-line:rgba(0,5,28,.12); --shadow:0 4px 24px rgba(0,0,0,.35); color-scheme:dark;}
.zts h1,.zts h2,.zts h3,.zts h4,.zts p,.zts li,.zts dt,.zts dd,.zts summary{color:inherit;font-weight:inherit}
.zts,.zts *{box-sizing:border-box}
.zts{background:var(--bg);color:var(--ink);font:400 16px/1.65 var(--sans);padding-inline:20px;padding-block:0 80px;-webkit-font-smoothing:antialiased}
.zts .wrap{max-width:880px;margin:0 auto}
.zts h1,.zts h2,.zts h3{margin:0;text-wrap:balance;letter-spacing:-.02em;line-height:1.15}
.zts p{margin:0}
.zts a{color:var(--gold);text-underline-offset:3px}
.zts code,.zts .mono{font-family:var(--mono);font-size:.9em}
.zts button{font:inherit;color:inherit;cursor:pointer}
.zts :focus-visible{outline:2px solid var(--gold-fill);outline-offset:2px;border-radius:6px}

/* header */
.zts .top{display:flex;justify-content:space-between;align-items:center;gap:12px;padding-block:20px}
.zts .brand{display:flex;align-items:center;gap:10px;font-weight:600;font-size:15px}
.zts .mark{width:26px;height:26px;border-radius:8px;background:var(--gold-fill);display:grid;place-items:center}
.zts .seg{display:inline-flex;background:var(--soft);border-radius:999px;padding:3px}
.zts .seg button{border:0;background:none;padding:5px 12px;border-radius:999px;font-size:13px;font-weight:500;color:var(--muted)}
.zts .seg button[aria-pressed="true"]{background:var(--bg);color:var(--ink);box-shadow:0 1px 2px rgba(0,0,0,.08)}

/* hero */
.zts .hero{padding-block:48px 56px;display:grid;gap:36px}
.zts .eyebrow{font-size:13px;font-weight:500;color:var(--gold)}
.zts .hero h1{font-size:clamp(36px,6vw,58px);font-weight:700;margin-top:10px}
.zts .hero .lead{font-size:18px;color:var(--muted);max-width:56ch;margin-top:14px}
.zts .facts{display:flex;flex-wrap:wrap;gap:28px;margin-top:22px;font-size:14px;color:var(--muted)}
.zts .facts b{display:block;color:var(--ink);font-size:20px;font-weight:600;font-variant-numeric:tabular-nums}

.zts .compare{border:1px solid var(--line);border-radius:16px;overflow:hidden}
.zts .compare-head{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:14px 18px;border-bottom:1px solid var(--line);flex-wrap:wrap}
.zts .compare-head span{font-size:14px;color:var(--muted)}
.zts .rows{padding:6px 18px}
.zts .r{display:grid;grid-template-columns:90px minmax(0,1fr);gap:12px;padding:10px 0;border-bottom:1px solid var(--line);align-items:center;font-size:15px}
.zts .r:last-child{border-bottom:0}
.zts .r .k{font-size:13px;color:var(--muted)}
.zts .r .v{font-family:var(--mono);font-size:14px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;position:relative;min-height:1.4em}
.zts .v .plain{color:var(--pub);transition:opacity .35s}
.zts .v .hide{position:absolute;left:0;top:50%;height:12px;margin-top:-6px;border-radius:3px;background:var(--gold-fill);transform-origin:left;transform:scaleX(0);transition:transform .45s cubic-bezier(.3,.7,.2,1)}
.zts .compare.sh .v .plain{opacity:0}
.zts .compare.sh .v .hide{transform:scaleX(1)}
.zts .compare.sh .r:nth-child(2) .hide{transition-delay:.06s}
.zts .compare.sh .r:nth-child(3) .hide{transition-delay:.12s}
.zts .compare.sh .r:nth-child(4) .hide{transition-delay:.18s}
.zts .compare-foot{padding:12px 18px;background:var(--soft);font-size:14px;color:var(--muted)}
.zts .compare-foot b{color:var(--ink);font-weight:600}

/* wallet picker */
.zts .picker{position:sticky;top:env(safe-area-inset-top,0px);z-index:5;background:var(--bg);padding-block:14px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
.zts .picker-label{font-size:14px;color:var(--muted)}
.zts .tabs{display:flex;gap:6px;flex-wrap:wrap}
.zts .tab{border:1px solid var(--line);background:var(--bg);border-radius:999px;padding:7px 14px;font-size:14px;font-weight:500;display:flex;align-items:center;gap:8px}
.zts .tab small{color:var(--muted);font-weight:400;font-size:12px}
.zts .tab[aria-pressed="true"]{border-color:var(--ink);background:var(--ink);color:var(--bg)}
.zts .tab[aria-pressed="true"] small{color:inherit;opacity:.7}
@media (max-width:560px){.zts .tab small,.zts .picker-label{display:none}.zts .tabs{flex-wrap:nowrap}}

/* chapter */
.zts .chapter{padding-block:64px 8px;display:grid;gap:28px;scroll-margin-top:70px}
.zts .ch-top{display:grid;gap:10px;max-width:62ch}
.zts .ch-n{font-family:var(--mono);font-size:13px;color:var(--gold)}
.zts .chapter h2{font-size:clamp(26px,3.8vw,34px);font-weight:700}
.zts .ch-top p{color:var(--muted);font-size:17px}

.zts .walk{display:grid;gap:28px;align-items:start}
@media (min-width:760px){.zts .walk{grid-template-columns:260px minmax(0,1fr);gap:44px}.zts .walk.wide{grid-template-columns:1fr}}
.zts .phone{width:100%;max-width:260px;margin:0 auto;aspect-ratio:540/1173;cursor:pointer;border-radius:38px;border:8px solid var(--ink);background:var(--soft);position:relative;overflow:hidden;box-shadow:0 20px 50px -24px rgba(0,0,0,.35)}
.zts .phone.desk{max-width:none;aspect-ratio:16/10;border-radius:12px;border-width:6px}
@media (min-width:760px){.zts .phone:not(.desk){position:sticky;top:96px}}
.zts .phone::before{content:"";position:absolute;top:8px;left:50%;width:70px;height:18px;margin-left:-35px;border-radius:12px;background:var(--ink);z-index:2}
.zts .phone.desk::before{display:none}
.zts .phone.has-img::before{display:none}
.zts .phone.has-img .counter{display:none}
.zts .phone.desk .counter{bottom:auto;top:10px;right:14px;left:auto}
.zts .screen{position:absolute;inset:0;display:grid;place-items:center;transition:opacity .3s}
.zts .screen img{width:100%;height:100%;object-fit:cover;object-position:top}
.zts .ph{display:grid;gap:8px;place-items:center;text-align:center;padding:24px;color:var(--muted);font-size:13px}
.zts .ph .ic{width:44px;height:44px;border-radius:12px;border:1.5px dashed var(--muted);display:grid;place-items:center;font-size:20px}
.zts .ph b{color:var(--ink);font-weight:600;font-size:14px}
.zts .counter{position:absolute;bottom:12px;left:0;right:0;text-align:center;font:500 11px var(--mono);color:var(--muted)}

.zts .steps{list-style:none;margin:0;padding:0;display:grid;gap:4px;counter-reset:s}
.zts .steps li{counter-increment:s}
.zts .step{width:100%;text-align:left;border:0;background:none;border-radius:12px;padding:14px 16px 14px 56px;position:relative;display:grid;gap:4px;transition:background .2s}
.zts .step::before{content:counter(s);position:absolute;left:16px;top:14px;width:26px;height:26px;border-radius:50%;border:1.5px solid var(--line);display:grid;place-items:center;font-size:13px;font-weight:600;color:var(--muted);transition:all .2s}
.zts .step:hover{background:var(--soft)}
.zts .step[aria-current="true"]{background:var(--soft)}
.zts .step[aria-current="true"]::before{background:var(--gold-fill);border-color:var(--gold-fill);color:var(--on-gold)}
.zts .step b{font-weight:600;font-size:16px}
.zts .step span{color:var(--muted);font-size:15px}
.zts .step em{font-style:normal;font-weight:600;color:var(--ink)}
.zts .na{font-size:15px;color:var(--muted);background:var(--soft);border-radius:12px;padding:16px}

.zts .locked{position:absolute;inset:0;background:#15181B;color:#E9ECEE;display:grid;align-content:center;justify-items:center;gap:12px;padding:28px 18px;text-align:center}
.zts .lk-ic{width:56px;height:56px;border-radius:16px;background:#F2B632;color:#1B1404;display:grid;place-items:center}
.zts .locked b{font-size:15px;font-weight:600}
.zts .locked p{font-size:12.5px;color:#A9B1B8;line-height:1.5}
.zts .lk-words{display:grid;grid-template-columns:repeat(3,1fr);gap:5px;width:100%;margin-top:6px}
.zts .lk-words span{font:500 10px var(--mono);color:#6E777F;background:#22262A;border-radius:6px;padding:6px 0 6px 7px;text-align:left;position:relative;overflow:hidden}
.zts .lk-words span::after{content:"";position:absolute;left:38%;right:12%;top:50%;height:5px;margin-top:-2px;border-radius:3px;background:#3A4046}
.zts .seedbox{border:1.5px solid var(--gold-fill);background:var(--gold-soft);border-radius:16px;padding:22px;display:grid;gap:14px}
.zts .seedbox h3{font-size:19px;font-weight:700}
.zts .seedbox p{color:var(--ink);max-width:62ch}
.zts .seedbox ul{list-style:none;margin:0;padding:0;display:grid;gap:10px}
@media (min-width:640px){.zts .seedbox ul{grid-template-columns:repeat(3,1fr)}}
.zts .seedbox li{background:var(--bg);border-radius:12px;padding:14px;display:grid;gap:4px;font-size:14px;color:var(--muted)}
.zts .seedbox li b{color:var(--ink);font-size:15px;font-weight:600}
.zts .proof{display:grid;gap:12px}
@media (min-width:640px){.zts .proof{grid-template-columns:1fr 1fr}}
.zts .pcol{border:1px solid var(--line);border-radius:14px;padding:18px;display:grid;gap:12px;align-content:start}
.zts .pcol dl{margin:0;display:grid;grid-template-columns:auto 1fr;gap:8px 16px;font-size:14px}
.zts .pcol dt{color:var(--muted)}
.zts .pcol dd{margin:0;text-align:right;font-family:var(--mono);font-size:13px}
.zts .txid{display:flex;flex-wrap:wrap;gap:8px 12px;align-items:center;font-size:13px;color:var(--muted)}
.zts .txid code{word-break:break-all;color:var(--ink);background:var(--soft);padding:4px 8px;border-radius:6px;min-width:0;flex:1 1 300px}
.zts .more{display:grid;gap:8px;font-size:14px}
.zts .mtx{display:flex;flex-wrap:wrap;gap:6px 14px;align-items:center;padding:12px 14px;border:1px solid var(--line);border-radius:12px;text-decoration:none;color:var(--ink)}
.zts .mtx:hover{border-color:var(--gold-fill)}
.zts .mtx code{color:var(--muted)}
.zts .mtx .mb{margin-left:auto;font-family:var(--mono);font-size:12px;color:var(--muted)}
.zts .ledger{display:grid;border:1px solid var(--line);border-radius:12px;overflow:hidden}
.zts .lrow{display:grid;grid-template-columns:24px 56px minmax(0,1fr) auto 100px;gap:12px;align-items:center;padding:12px 14px;border-bottom:1px solid var(--line);text-decoration:none;color:var(--ink);font-size:14px}
.zts .lrow:last-child{border-bottom:0}
.zts .lrow:hover{background:var(--soft)}
.zts .ln{font-family:var(--mono);color:var(--muted);font-size:12px}
.zts .lw{font-weight:600}
.zts .lb{font-family:var(--mono);font-size:12px;color:var(--muted);text-align:right;white-space:nowrap}
@media (max-width:640px){.zts .lrow{grid-template-columns:20px minmax(0,1fr) auto;row-gap:4px}.zts .lw{grid-column:2}.zts .ld{grid-column:2/4}.zts .lrow .pill{grid-column:3;grid-row:1}.zts .lb{grid-column:2;text-align:left}}
.zts .phone.ext{max-width:300px;aspect-ratio:420/790;border-radius:14px;border-width:6px}
.zts .phone.ext::before{display:none}
.zts .screen img.fit{object-fit:contain;object-position:center;background:#13141B}
.zts .step .inl{display:block;width:100%;border-radius:10px;border:1px solid var(--line);margin-top:10px}
.zts .zlogo{width:auto;display:block}
/* chain card */
.zts .chain{border:1px solid var(--line);border-radius:14px;padding:18px;display:grid;gap:14px}
.zts .chain-h{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
.zts .chain-h h3{font-size:15px;font-weight:600}
.zts .pill{font-size:12px;font-weight:600;padding:3px 10px;border-radius:999px}
.zts .pill.pub{background:var(--pub-soft);color:var(--pub)}
.zts .pill.mix{background:var(--gold-soft);color:var(--gold)}
.zts .pill.priv{background:var(--gold-fill);color:var(--on-gold)}
.zts .pill.none{background:var(--soft);color:var(--muted)}
.zts .flow{display:grid;grid-template-columns:minmax(0,1fr) 64px minmax(0,1fr);align-items:center;position:relative}
.zts .node{border-radius:10px;padding:12px;min-width:0;display:grid;gap:2px}
.zts .node small{font-size:12px;color:var(--muted)}
.zts .node b{font-family:var(--mono);font-weight:500;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.zts .node.t{background:var(--pub-soft)}
.zts .node.t b{color:var(--pub)}
.zts .node.s{background:var(--gold-soft)}
.zts .node.s b{color:var(--gold)}
.zts .node.x{background:var(--soft)}
.zts .track{height:2px;background:var(--line);position:relative;margin:0 8px}
.zts .dot{position:absolute;top:-4px;left:0;width:10px;height:10px;border-radius:50%;background:var(--gold-fill);animation:go 2.4s ease-in-out infinite}
.zts .track.pubt .dot{background:var(--pub)}
@keyframes go{0%{left:0;opacity:0}15%{opacity:1}85%{opacity:1}100%{left:calc(100% - 10px);opacity:0}}
.zts .hidebar{display:inline-block;width:70%;height:10px;border-radius:3px;background:var(--gold-fill);vertical-align:middle}
.zts .chain p{font-size:14px;color:var(--muted)}

.zts .tip{font-size:15px;border-left:3px solid var(--gold-fill);padding:4px 0 4px 16px;max-width:64ch}
.zts .tip b{font-weight:600}
.zts .tip.w{border-color:var(--warn)}

/* end */
.zts .check{list-style:none;margin:0;padding:0;display:grid;gap:2px}
.zts .check label{display:flex;gap:12px;padding:12px 14px;border-radius:10px;cursor:pointer;align-items:flex-start}
.zts .check label:hover{background:var(--soft)}
.zts .check input{width:18px;height:18px;margin-top:3px;accent-color:var(--gold-fill);flex:none}
.zts .check input:checked+span{color:var(--muted);text-decoration:line-through}
.zts .faq details{border-bottom:1px solid var(--line);padding-block:14px}
.zts .faq summary{cursor:pointer;font-weight:600}
.zts .faq p{color:var(--muted);margin-top:8px;max-width:64ch}
.zts .src{display:grid;gap:4px;font-size:14px;color:var(--muted);padding-top:12px}
.zts footer{margin-top:64px;padding-top:20px;border-top:1px solid var(--line);font-size:13px;color:var(--muted);display:grid;gap:4px}
@media (prefers-reduced-motion: reduce){.zts,.zts *{animation:none !important;transition:none !important}}

/* Zecmap look */
.zts{min-height:100vh;transition:background .3s,color .3s}
.zts h1,.zts h2,.zts h3,.zts .ch-n{font-family:var(--head)}
.zts .top{background:var(--hdr-bg);color:var(--hdr-ink);border-radius:54px;padding:10px 12px 10px 14px;margin-top:16px}
.zts .brand{gap:12px;color:inherit;text-decoration:none}
.zts .bt{display:grid;line-height:1.2}
.zts .bt b{font:700 16px var(--head);letter-spacing:-.01em}
.zts .bt small{font-size:12px;opacity:.7;font-weight:500}
.zts .zlogo{height:34px}
.zts .icon-btn{border:1px solid var(--hdr-line);background:transparent;color:var(--hdr-ink);width:40px;height:40px;border-radius:50%;display:grid;place-items:center}
.zts .icon-btn:hover{filter:brightness(1.1);background:rgba(127,127,127,.12)}
.zts .icon-btn .moon{display:none}
html.dark .zts .icon-btn .sun{display:none} html.dark .zts .icon-btn .moon{display:block}
@media (prefers-color-scheme: dark){html:not(.light) .zts .icon-btn .sun{display:none} html:not(.light) .zts .icon-btn .moon{display:block}}
.zts[data-embed] .icon-btn{display:none}
.zts[data-embed] .top{display:none}
.zts .compare,.zts .chain,.zts .pcol,.zts .ledger,.zts .mtx{background:var(--card);box-shadow:var(--shadow);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px)}
.zts .compare,.zts .chain,.zts .pcol{border-radius:10px}
.zts .picker{top:var(--sticky-top)}
.zts .tab{border-radius:5px;background:var(--card);padding:8px 16px;font-weight:600}
.zts .tab[aria-pressed="true"]{background:var(--grad);border-color:transparent;color:var(--on-gold);box-shadow:0 10px 15px -3px rgba(243,184,38,.25)}
.zts .tab:active{transform:scale(.97)}
.zts .seg button[aria-pressed="true"]{background:var(--grad);color:var(--on-gold)}
.zts .eyebrow{font-weight:600;letter-spacing:.02em}
.zts .hero h1{font-weight:800}
.zts .step[aria-current="true"]{box-shadow:var(--shadow)}
.zts .locked{background:#01061C}
.zts .lk-words span{background:#0C1538;color:#8A90A6}
.zts .lk-words span::after{background:#2A3358}
.zts .phone{border-color:#01061C}
html.dark .zts .phone{border-color:#2A3358}
@media (prefers-color-scheme: dark){html:not(.light) .zts:not([data-embed]) .phone{border-color:#2A3358}}
.zts footer{border-top:1px solid var(--line)}

</style>


<div class="zts" id="top-zts" lang="en" data-embed>
<div class="wrap">
<header class="top">
  <a class="brand" href="#top-zts"><span class="mark" id="brand-mark" aria-hidden="true"><svg width="14" height="16" viewBox="0 0 14 16"><path d="M7 1 L13 3.5 V8 C13 11.5 10.4 14 7 15 C3.6 14 1 11.5 1 8 V3.5 Z" fill="none" stroke="#1B1404" stroke-width="1.8"/></svg></span><span class="bt"><b>Zero to Shielded</b><small>Zcash wallet guide</small></span></a>
  <button type="button" class="icon-btn" id="theme" aria-label="Switch light or dark theme"><svg class="sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg><svg class="moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 14.5A8.5 8.5 0 0 1 9.5 3.5a8.5 8.5 0 1 0 11 11z"/></svg></button>
</header>

<section class="hero">
  <div>
    <div class="eyebrow" data-i="eyebrow"></div>
    <h1 data-i="h1"></h1>
    <p class="lead" data-i="lead"></p>
    <div class="facts"><div><b>5</b><span data-i="f1"></span></div><div><b>~20 min</b><span data-i="f2"></span></div><div><b>0.0001 ZEC</b><span data-i="f3"></span></div></div>
  </div>
  <div class="compare sh" id="cmp">
    <div class="compare-head"><span data-i="cmp.h"></span><div class="seg" role="group"><button type="button" id="v-t" aria-pressed="false" data-i="pub"></button><button type="button" id="v-s" aria-pressed="true" data-i="shd"></button></div></div>
    <div class="rows">
      <div class="r"><span class="k" data-i="k.from"></span><span class="v"><span class="plain">t1Vx7Rq4mN…H9fQ</span><i class="hide" style="width:62%"></i></span></div>
      <div class="r"><span class="k" data-i="k.to"></span><span class="v"><span class="plain">t1Kp3LmZs8…w2aL</span><i class="hide" style="width:54%"></i></span></div>
      <div class="r"><span class="k" data-i="k.amt"></span><span class="v"><span class="plain">1.2500 ZEC</span><i class="hide" style="width:30%"></i></span></div>
      <div class="r"><span class="k" data-i="k.memo"></span><span class="v"><span class="plain">—</span><i class="hide" style="width:72%"></i></span></div>
      <div class="r"><span class="k" data-i="k.fee"></span><span class="v">0.0001 ZEC</span></div>
    </div>
    <div class="compare-foot" id="cmp-foot"></div>
  </div>
</section>

<nav class="picker" aria-label="Wallet">
  <span class="picker-label" data-i="pick"></span>
  <div class="tabs" id="tabs"></div>
</nav>

<div id="chapters"></div>

<section class="chapter" id="proof">
  <div class="ch-top"><span class="ch-n" data-i="pf.n"></span><h2 data-i="pf.h"></h2><p data-i="pf.p"></p></div>
  <div class="proof">
    <div class="pcol"><div class="pcol-h"><span class="pill priv" data-i="pf.w"></span></div>
      <dl><dt data-i="k.from"></dt><dd>Shielded sender</dd><dt data-i="k.amt"></dt><dd>0.005 ZEC</dd><dt data-i="k.memo"></dt><dd>"Vizor Wallet"</dd><dt data-i="pf.pool"></dt><dd>Ironwood</dd><dt data-i="pf.time"></dt><dd>Oct 3, 00:21 (UTC+3)</dd></dl></div>
    <div class="pcol"><div class="pcol-h"><span class="pill pub" data-i="pf.e"></span></div>
      <dl><dt data-i="pf.blk"></dt><dd>3,504,157</dd><dt data-i="pf.type"></dt><dd>Shielded · v6</dd><dt data-i="pf.io"></dt><dd>0 / 0</dd><dt data-i="pf.amt"></dt><dd data-i="pf.hidden"></dd><dt data-i="k.fee"></dt><dd>0.0001 ZEC</dd></dl></div>
  </div>
  <p class="txid"><span>Tx ID</span><code>e25b95829529cb2f02bd683d7f89fff620c0b2b5e774cf51ec1a734b545d05f7</code><a href="https://mainnet.zcashexplorer.app/transactions/e25b95829529cb2f02bd683d7f89fff620c0b2b5e774cf51ec1a734b545d05f7" target="_blank" rel="noopener" data-i="pf.open"></a></p>
  <div class="more"><b data-i="pf.more"></b>
  <div class="ledger">
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/e25b95829529cb2f02bd683d7f89fff620c0b2b5e774cf51ec1a734b545d05f7" target="_blank" rel="noopener"><span class="ln">1</span><span class="lw">Vizor</span><span class="ld" data-i="pf.r1"></span><span class="pill priv" data-i="pf.r1v"></span><span class="lb">#3,504,157</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/e302792256cd4c226dc59b2a1b515d6f5dea5969e38b0e2337a4a73980db9ca8" target="_blank" rel="noopener"><span class="ln">2</span><span class="lw">Vizor</span><span class="ld" data-i="pf.r2"></span><span class="pill priv" data-i="pf.r2v"></span><span class="lb">#3,504,166</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/2722a026442fc7ee084eb9371578bc8b62242ed701085219255b64175712d471" target="_blank" rel="noopener"><span class="ln">3</span><span class="lw">Zingo</span><span class="ld" data-i="pf.r3"></span><span class="pill priv" data-i="pf.r3v"></span><span class="lb">#3,504,197</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/78cb1af778dd1b4dbdd3090dec5afa5ad5fd92fdf8e8961cdb905af86f583a84" target="_blank" rel="noopener"><span class="ln">4</span><span class="lw">Zingo</span><span class="ld" data-i="pf.r4"></span><span class="pill priv" data-i="pf.r4v"></span><span class="lb">#3,504,201</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/f3f4d70c24c60cc171f763025c8172131f32ecf176c19e82c2c1b95face6edc1" target="_blank" rel="noopener"><span class="ln">5</span><span class="lw">Zingo</span><span class="ld" data-i="pf.r5"></span><span class="pill pub" data-i="pf.r5v"></span><span class="lb">#3,504,201</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/fe182500b448fed6be0ed9fda474255eb681786412e306dd3a0ae0ddd46ebd3e" target="_blank" rel="noopener"><span class="ln">6</span><span class="lw">Zingo</span><span class="ld" data-i="pf.r6"></span><span class="pill pub" data-i="pf.r6v"></span><span class="lb">#3,504,211</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/f12db816656effd1b610bc3dd02e556e878e084ed61a9e18b8a7c28c12060a77" target="_blank" rel="noopener"><span class="ln">7</span><span class="lw">Zingo</span><span class="ld" data-i="pf.r7"></span><span class="pill pub" data-i="pf.r7v"></span><span class="lb">#3,504,213</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/2ea1ec478173a5adcbf479cc20c71ba6ace7c926ab5a2e28ab9ec44041f28a06" target="_blank" rel="noopener"><span class="ln">8</span><span class="lw">Noir</span><span class="ld" data-i="pf.r9"></span><span class="pill priv" data-i="pf.r9v"></span><span class="lb">#3,504,230</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/b11936dc493fe036f2a2d877c529d53deaa3985d21e59a12a8d9034f13c7e965" target="_blank" rel="noopener"><span class="ln">9</span><span class="lw">Noir</span><span class="ld" data-i="pf.r10"></span><span class="pill pub" data-i="pf.r10v"></span><span class="lb">#3,504,239</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/513bffe2e095b2528de69614b48835945e26d7db6f1e27f40bf9a90df35e423e" target="_blank" rel="noopener"><span class="ln">10</span><span class="lw">Noir</span><span class="ld" data-i="pf.r8"></span><span class="pill pub" data-i="pf.r8v"></span><span class="lb">#3,504,246</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/5008e394080f77b0a31ea4775fdc592f747be612f1bbf2cda36ebcaf41f1d783" target="_blank" rel="noopener"><span class="ln">11</span><span class="lw">Noir</span><span class="ld" data-i="pf.r11"></span><span class="pill priv" data-i="pf.r11v"></span><span class="lb">#3,504,253</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/221645820ae7d1a2ccee63c2f0c05b7ed8a4979df2e30e024c0c323d6318ed15" target="_blank" rel="noopener"><span class="ln">12</span><span class="lw">Noir</span><span class="ld" data-i="pf.r12"></span><span class="pill pub" data-i="pf.r12v"></span><span class="lb">#3,504,252</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/55c274e58514f0aa69b9fefff55923318a2d00cf8e654b95149d2dede4de49d6" target="_blank" rel="noopener"><span class="ln">13</span><span class="lw">Zodl</span><span class="ld" data-i="pf.r13"></span><span class="pill priv" data-i="pf.r13v"></span><span class="lb">#3,504,239</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/291559df2872faeabfd56c1eb17fd21aa9466e46d0b82db0f8af507b47b5de12" target="_blank" rel="noopener"><span class="ln">14</span><span class="lw">Zodl</span><span class="ld" data-i="pf.r14"></span><span class="pill pub" data-i="pf.r14v"></span><span class="lb">#3,504,265</span></a>
    <a class="lrow" href="https://mainnet.zcashexplorer.app/transactions/1f724ef533650c5e8508d45cbe0a9a84407fa5ccec5eb0c6f11178d9b7e9882d" target="_blank" rel="noopener"><span class="ln">15</span><span class="lw">Zodl</span><span class="ld" data-i="pf.r15"></span><span class="pill pub" data-i="pf.r15v"></span><span class="lb">#3,504,272</span></a>
  </div>
  <p class="tip w" data-i="pf.lesson"></p>
  </div>
</section>

<section class="chapter" id="finish">
  <div class="ch-top"><span class="ch-n">✓</span><h2 data-i="fin.h"></h2><p data-i="fin.p"></p></div>
  <ul class="check" id="check"></ul>
  <p class="tip" data-i="iron"></p>
  <div class="faq">
    <details><summary data-i="q1"></summary><p data-i="a1"></p></details>
    <details><summary data-i="q2"></summary><p data-i="a2"></p></details>
    <details><summary data-i="q3"></summary><p data-i="a3"></p></details>
    <details><summary data-i="q4"></summary><p data-i="a4"></p></details>
  </div>
  <div class="src"><b data-i="src"></b>
    <a href="https://zodl.com/" target="_blank" rel="noopener">zodl.com</a>
    <a href="https://vizor.cash/" target="_blank" rel="noopener">vizor.cash</a>
    <a href="https://zknoir.com/" target="_blank" rel="noopener">zknoir.com</a>
    <a href="https://zingolabs.org/" target="_blank" rel="noopener">zingolabs.org</a>
    <a href="https://support.zodl.com/article/26-swapping-into-zec" target="_blank" rel="noopener">Zodl Support · Swapping into ZEC</a>
    <a href="https://zcash.github.io/ironwood/concepts.html" target="_blank" rel="noopener">Ironwood design book</a>
    <a href="https://forum.zcashcommunity.com/t/ironwood-is-here-updated-wallets-libraries-aug-1/56557" target="_blank" rel="noopener">Zcash Forum · Ironwood is here: updated wallets</a>
    <a href="https://support.zodl.com/article/42-moving-your-funds-to-ironwood" target="_blank" rel="noopener">Zodl Support · Moving your funds to Ironwood</a>
    <a href="https://zips.z.cash/zip-0320" target="_blank" rel="noopener">ZIP 320 · TEX addresses</a>
    <a href="https://zips.z.cash/zip-0317" target="_blank" rel="noopener">ZIP 317 · Fees</a>
  </div>
</section>

<footer><span data-i="foot1"></span><span data-i="foot2"></span></footer>
</div>
</div>

<script>
const BASE=(window.ZTS_BASE||"");
/* Wallets. `shots` maps chapter -> list of image paths per step (null = placeholder until screenshots arrive). */
const WALLETS={
  zodl:{name:"Zodl",plat:"iOS · Android",site:"zodl.com",swap:true,desk:false,shots:{},
    steps:{
      setup:[
        {img:"shots/zodl/1-01-welcome.jpg",en:["Create New Wallet","Install Zodl from <em>zodl.com</em>, open it and tap <em>Create New Wallet</em>. <em>Restore Existing Wallet</em> is for when you already have 24 words."]},
        {img:"shots/zodl/1-02-home-empty.jpg",en:["Your wallet opens right away","Zodl takes you straight to the home screen: Receive, Send, Pay and Swap. You can receive before backing up, but don't keep money here until you have."]},
        {img:"shots/zodl/1-03-home-backup.jpg",en:["Start the backup","A purple <em>Wallet Backup Required</em> banner stays on the home screen until you back up. Tap <em>Start</em>."]},
        {img:"shots/zodl/1-04-backup-intro.jpg",en:["Read the four rules","Control your wallet, keep it private, store it securely, and note the <em>Wallet Birthday Height</em>, which speeds up recovery. Zodl itself warns: don't take screenshots of the phrase."]},
        {img:null,lock:"hide",en:["Write the 24 words on paper","Zodl shows your words next. Following its own advice, we don't show them here. Write every word in order, plus the birthday height. <em>These words are your wallet.</em>"]}
      ],
      get:[
        {img:"shots/zodl/2-01-receive.jpg",en:["Tap Receive","Two addresses: <em>Zcash Shielded Address</em> (u1) on top with Copy, QR Code and Request, and the transparent one below. Zodl's own tip: \"For privacy, always use shielded address.\""]},
        {img:"shots/zodl/2-02-receive-transparent.jpg",en:["Transparent, only if you must","The transparent address has its own QR screen, clearly labelled <em>Not Private</em>. Use it only for an exchange that can't send to u1."]},
        {img:"shots/zodl/2-03-swap.jpg",en:["Or swap from another coin","Tap <em>Swap</em>. Pick what you pay with under <em>From</em> (here 0.05 SOL), enter a <em>Refund Address</em> on that chain, and you get ZEC. Tap <em>Get a quote</em>."]},
        {img:"shots/zodl/2-04-no-refunds.jpg",en:["Read the refund warning","\"No Refunds Under $300\": for small swaps, NEAR doesn't refund mistakes like a wrong asset or wrong network. Double-check, then <em>Continue</em>."]},
        {img:"shots/zodl/2-05-quote.jpg",en:["Review the quote","You see both sides, the total fees and how much less you could get within your 2% slippage. Tap <em>Confirm</em>."]},
        {img:"shots/zodl/2-06-deposit.jpg",en:["Send the deposit","Zodl shows a deposit address and QR. Send exactly that amount of SOL on Solana from your other wallet, then tap <em>I've sent the funds</em>."]},
        {img:"shots/zodl/2-07-pending.jpg",en:["Follow the swap","Status moves from <em>Pending deposit</em> to <em>Processing</em>. Note the <em>Recipient</em>: a fresh shielded address, not your usual one."]},
        {img:"shots/zodl/2-08-swapped.jpg",en:["Swapped","0.05 SOL became 0.00422824 ZEC, with 0% realized slippage. It lands shielded."]},
        {img:"shots/zodl/1-03-home-backup.jpg",en:["Read your activity","A shield icon next to an entry means it's shielded. Here the 0.005 ZEC came in shielded; the 0.001 ZEC without the icon came to the transparent address."]}
      ],
      shield:[
        {img:"shots/zodl/3-01-unshielded.jpg",en:["Spot the Unshielded Balance","When transparent ZEC arrives, a purple <em>Unshielded Balance</em> banner appears on the home screen with a <em>Shield</em> button."]},
        {img:"shots/zodl/3-02-dialog.jpg",en:["Read why, then tap Shield","\"Always Shield Transparent Funds\": Zodl doesn't spend transparent ZEC at all. Shielding makes it spendable and your whole balance private, for a typical fee under 0.001 ZEC. Tap <em>Shield</em>."]},
        {img:"shots/zodl/3-03-shielding.jpg",en:["Shielding","The transaction shows <em>Shielding</em> with a fee of 0.00015 ZEC. Once it confirms, the amount joins your shielded balance and becomes spendable."]}
      ],
      send:[
        {img:"shots/zodl/4-01-send-empty.jpg",en:["Open Send","Three fields: <em>Send to</em>, <em>Amount</em>, and <em>Message</em>, which says \"Write encrypted message here...\" with a 512-byte counter."]},
        {img:"shots/zodl/4-02-send.jpg",en:["Fill it in","Paste or scan a <em>u1</em> address, enter the amount and an optional message. Tap <em>Review</em>."]},
        {img:"shots/zodl/4-03-confirm.jpg",en:["Check the confirmation","The full address is shown so you can compare every character. Total = amount + fee (0.0001 ZEC). Tap <em>Send</em>."]},
        {img:"shots/zodl/4-04-sending.jpg",en:["Sending…","Your phone builds the proof and broadcasts."]},
        {img:"shots/zodl/4-05-sent.jpg",en:["Sent!","Tap <em>View Transaction</em> for the details."]},
        {img:"shots/zodl/4-06-detail.jpg",en:["The details","Recipient, transaction ID, fee and your message. Status goes from <em>Pending</em> to confirmed; in Activity the entry carries a shield icon."]}
      ],
      exit:[
        {img:"shots/zodl/5-01-send-t.jpg",en:["Send to the transparent address","Paste the exchange's <em>t1</em> deposit address in Send. The message field changes to \"Transparent transactions can't have memos\". <em>Spendable</em> shows what you can use right now."]},
        {img:"shots/zodl/5-02-confirm.jpg",en:["Check the confirmation","Fee here is 0.00015 ZEC. Compare the full t1 address, then tap <em>Send</em>."]},
        {img:"shots/zodl/5-03-sending.jpg",en:["Sending…","The proof is built on your phone and the transaction is broadcast."]},
        {img:"shots/zodl/5-04-sent.jpg",en:["Sent!","Tap <em>View Transaction</em>."]},
        {img:"shots/zodl/5-05-detail.jpg",en:["The details","0.00115 ZEC left your wallet: 0.001 to the t1 address plus the 0.00015 fee. The destination and amount are now public; where the ZEC came from is not."]}
      ]
    }},
  vizor:{name:"Vizor",plat:"iOS · Android · Desktop",site:"vizor.cash",swap:true,desk:false,shots:{},
    steps:{
      setup:[
        {img:"shots/vizor/1-01-start.jpg",en:["Open Vizor","Install from <em>vizor.cash</em>, open the app and tap <em>Get started</em>."]},
        {img:"shots/vizor/1-02-method.jpg",en:["Choose Create Wallet","Other options let you import a wallet, link Vizor Desktop, or connect a Keystone or Ledger."]},
        {img:"shots/vizor/1-03-intro.jpg",en:["Read the short intro","Vizor explains what shielded means. Tap <em>Tell me how Zcash works</em>, or skip if you already know."]},
        {img:"shots/vizor/1-04-phrase.jpg",en:["Reveal your Secret Passphrase","Make sure nobody can see your screen, then tap <em>Reveal phrase</em>."]},
        {img:null,lock:true,en:["Write the 24 words on paper","Vizor blocks screenshots on this screen on purpose, so there is no picture here. Write every word in order, on paper. <em>These words are your wallet.</em>"]},
        {img:"shots/vizor/1-06-account.jpg",en:["Name your account","Pick a name and a picture, then tap <em>Continue</em>."]},
        {img:"shots/vizor/1-07-home.jpg",en:["You're in","Your <em>Shielded balance</em> shows 0 ZEC and the top right says <em>Vizor is synced</em>."]}
      ],
      get:[
        {img:"shots/vizor/1-07-home.jpg",en:["Tap Receive your first ZEC","It's right under your balance on the home screen."]},
        {img:"shots/vizor/2-02-receive-shielded.jpg",en:["Share your shielded address","The <em>Shielded</em> tab shows a <em>u1</em> address. Use this by default. The ↻ button gives you a fresh address."]},
        {img:"shots/vizor/2-03-receive-transparent.jpg",en:["Exchange won't take u1? Use Transparent","Switch to the <em>Transparent</em> tab for a <em>t1</em> address. Funds sent here are public until you shield them."]},
        {img:"shots/vizor/5-01-swap.jpg",en:["Or swap from another coin","The ⇅ tab opens <em>Swap</em>, powered by NEAR Intents. Tap the middle ⇅ button to flip it, so you pay with SOL, BTC or USDC and receive ZEC."]},
        {img:"shots/vizor/2-05-syncing.jpg",en:["Watch it arrive","Vizor syncs with the network and shows <em>Receiving…</em> under Recent activity, tagged <em>Ironwood</em>, the new shielded pool."]},
        {img:"shots/vizor/2-06-receiving.jpg",en:["Tap it for details","<em>From: Shielded sender.</em> Not even you can see who sent it. The memo (here \"Vizor Wallet\") is only readable by you."]},
        {img:"shots/vizor/2-07-received.jpg",en:["Completed","After a confirmation the status turns <em>Completed</em>. The <em>Tx ID</em> links to a block explorer, where the payment exists but its contents don't show. <a href=\"https://mainnet.zcashexplorer.app/transactions/e25b95829529cb2f02bd683d7f89fff620c0b2b5e774cf51ec1a734b545d05f7\" target=\"_blank\" rel=\"noopener\">See this one ↗</a>"]},
        {img:"shots/vizor/2-08-home-balance.jpg",en:["Your first shielded ZEC","The balance is shielded from the start. If ZEC ever lands on your transparent address, chapter 03 shows the one-tap fix."]}
      ],
      shield:[
        {img:"shots/vizor/3-01-transparent.jpg",en:["Spot the transparent balance","When ZEC lands on your transparent address, Vizor lists it under the shielded balance: <em>Transparent: 0.001 ZEC</em>, with a <em>Shield</em> button. In Activity it's tagged <em>Transparent</em>, not Ironwood."]},
        {img:"shots/vizor/3-02-shielding.jpg",en:["Tap Shield","One tap. Vizor shows <em>Shielding…</em>, then a <em>Shielding complete</em> banner. The amount moves into your shielded balance."]},
        {img:"shots/vizor/3-03-shield-detail.jpg",en:["Check the details","<em>From transparent balance → To Shielded balance</em>. The fee here is 0.00015 ZEC, a bit more than a normal send, because spending a transparent coin counts as an extra action under ZIP 317."]}
      ],
      send:[
        {img:"shots/vizor/2-08-home-balance.jpg",en:["Tap Send","It's on the home screen, next to Receive."]},
        {img:"shots/vizor/4-02-recipient.jpg",en:["Select the recipient","Paste the address or tap <em>Scan a QR Code</em>. A <em>u1</em> address keeps the payment private. Tap <em>Continue</em>."]},
        {img:"shots/vizor/4-03-amount.jpg",en:["Enter the amount","Type it in ZEC; the dollar value appears below. <em>Max</em> sends everything minus the fee. Tap <em>Finish &amp; review</em>."]},
        {img:"shots/vizor/4-04-review.jpg",en:["Review, add a message, confirm","Vizor confirms it's going to a <em>Shielded address</em>. Tap <em>Full address</em> to compare every character. The <em>Message</em> is your encrypted memo. Fee: 0.0001 ZEC. Then <em>Confirm &amp; Send</em>."]},
        {img:"shots/vizor/4-05-sent.jpg",en:["Sent","Your device built a zero-knowledge proof and broadcast it. It confirms on-chain shortly; track it in Activity."]}
      ],
      exit:[
        {img:"shots/vizor/5-01-swap.jpg",en:["Swap ZEC out, no exchange needed","Open the ⇅ tab. Under <em>You pay</em> enter an amount of ZEC; under <em>You receive</em> pick the coin you want, here SOL. Powered by NEAR Intents."]},
        {img:"shots/vizor/5-02-swap-address.jpg",en:["Add the recipient address","Paste your address on the other chain and tap <em>Update</em>. Double-check it: crypto sent to a wrong address can't be recalled."]},
        {img:"shots/vizor/5-03-quote.jpg",en:["Get a quote","Vizor asks NEAR Intents solvers for a price. The 2% next to the gear is your slippage tolerance."]},
        {img:"shots/vizor/5-04-review.jpg",en:["Review the quote","You see what you pay, what you receive, and the <em>Guaranteed minimum</em>: you never get less than that. The swap fee is included in the rate. Tap <em>Confirm &amp; swap</em>."]},
        {img:"shots/vizor/5-05-progress.jpg",en:["Follow the progress","Four stages: <em>ZEC sent</em>, <em>Deposit confirmation</em>, <em>Swap</em>, <em>Deliver SOL</em>. It took about five minutes here."]},
        {img:"shots/vizor/5-06-completed.jpg",en:["Swap completed","0.004 ZEC became 0.0431 SOL. Realized slippage was 0.24%, well under the 2% limit, and the swap fee was 0.0000268 ZEC."]}
      ]
    }},
  noir:{name:"Noir",plat:"Chrome extension",site:"zknoir.com",swap:true,desk:false,ext:true,shots:{},
    steps:{
      setup:[
        {img:"shots/noir/1-01-store.jpg",fit:true,en:["Install from the Chrome Web Store","On <em>zknoir.com</em> tap <em>Install Wallet</em>. In the store, check the verified <em>zknoir.com</em> badge next to the name before you tap <em>Add to Chrome</em>."]},
        {img:"shots/noir/1-02-welcome.jpg",en:["Create Wallet","Open Noir from the extensions menu and choose <em>Create Wallet</em>. You can also import a wallet or connect a hardware wallet."]},
        {img:"shots/noir/1-03-password.jpg",en:["Set a password","The password only unlocks Noir in this browser. It is not a backup: if you forget it, you restore with your recovery phrase."]},
        {img:"shots/noir/1-04-secure.jpg",en:["Read the two rules","<em>Store it safely</em> and <em>Never share it</em>. Then tap <em>Start backup</em>."]},
        {img:"shots/noir/1-05-phrase-hidden.jpg",en:["Reveal and write down 24 words","Noir hides the words until you click. Write them on paper, in order, then tick <em>I acknowledge</em>. <em>These words are your wallet.</em>"]},
        {img:"shots/noir/1-06-confirm.jpg",en:["Confirm two words","Noir asks for two words by position, here the 12th and 13th (blurred). This proves you wrote them down."]},
        {img:"shots/noir/1-07-ready.jpg",en:["Your wallet is ready","Tap <em>Got it</em>."]},
        {img:"shots/noir/1-08-home.jpg",en:["The home screen","Send, Receive, Lending and Swap at the top. Below, your two addresses: one <em>Shielded</em> (u1), one <em>Transparent</em> (t1)."]}
      ],
      get:[
        {img:"shots/noir/2-01-receive-shielded.jpg",en:["Receive to the shielded address","Tap <em>Receive</em>. The u1 address is selected by default: \"Shielded addresses provide enhanced privacy for your transactions.\" Tap <em>Copy Address</em>."]},
        {img:"shots/noir/2-02-receive-transparent.jpg",en:["Transparent, only if you must","Select the t1 address if an exchange can't send to u1. Funds here are public until you shield them."]},
        {img:"shots/noir/2-03-activity.jpg",en:["Watch it confirm","Under <em>Activity</em> the payment shows <em>Confirming (1/10)</em>. Noir counts up to 10 confirmations."]},
        {img:"shots/noir/2-04-detail.jpg",fit:true,en:["Shielded pool to shielded pool","Once <em>Confirmed</em>, the details read <em>From: Shielded Pool → To: Shielded Pool</em>. No addresses, just the fee (0.0001 ZEC) and a transaction hash."]},
        {img:"shots/noir/2-05-balances.jpg",en:["Balances per address","Under <em>Assets</em> Noir lists each address on its own line: here 0.005 ZEC on the shielded one and 0.001 ZEC on the transparent one, which is marked <em>unspendable</em>. Tap ⓘ for details."]}
      ],
      shield:[
        {img:"shots/noir/3-01-shield-button.jpg",en:["Tap Shield Funds","When transparent ZEC is waiting, a <em>Shield Funds</em> button appears under your total. Activity also shows the transparent payment as <em>Received ZEC · Confirmed</em>."]},
        {img:"shots/noir/3-02-shield-sheet.jpg",en:["Review and tap Shield Now","\"Move transparent funds into the private pool for enhanced privacy.\" 0.001 ZEC, network fee about 0.00015 ZEC. Tap <em>Shield Now</em>."]},
        {img:"shots/noir/3-03-progress.jpg",en:["Shielding in progress","Noir shows exactly where the money moves: <em>From</em> your t1 address <em>To</em> your u1 address."]},
        {img:"shots/noir/3-04-sent.jpg",en:["Shield transaction sent","Submitted to the blockchain. Tap <em>Done</em>. After it confirms, the whole balance is shielded."]},
        {img:"shots/noir/3-05-detail.jpg",en:["Check it in Activity","The <em>Shield</em> entry shows From your <em>Transparent</em> address To your <em>Shielded</em> one, fee 0.00015 ZEC, and an automatic memo: \"Shielding transparent funds\". After 3 confirmations the status turns <em>Confirmed</em>."]}
      ],
      send:[
        {img:"shots/noir/4-01-send.jpg",en:["Fill in the send form","Tap <em>Send</em>. Paste a <em>u1</em> address, enter the amount and keep the source set to <em>Shielded</em>. Add an optional memo; the counter shows how much of the 512 bytes you've used. Tap <em>Continue</em>."]},
        {img:"shots/noir/4-02-review.jpg",en:["Review and confirm","<em>Review Send</em> shows From (your shielded address), To, the memo and the network fee (0.0001 ZEC). Tap <em>Confirm</em>."]},
        {img:"shots/noir/4-03-sending.jpg",en:["Building and broadcasting","Noir builds the zero-knowledge proof and broadcasts the transaction."]},
        {img:"shots/noir/4-04-sent.jpg",en:["Transaction sent","You get the time and the transaction hash. Tap <em>Done</em>."]},
        {img:"shots/noir/4-05-detail.jpg",en:["Confirmed","In Activity the send shows <em>Shielded</em> on both sides, From and To, plus your memo. Only you and the recipient can see this view."]}
      ],
      exit:[
        {img:"shots/noir/5-01-swap.jpg",en:["Swap ZEC out","Tap <em>Swap</em>. <em>From</em> is your shielded balance; pick the coin under <em>To</em> (here SOL) and paste your address on that chain. You see the rate, slippage (1%) and network fee before anything happens. Tap <em>Get a quote</em>."]},
        {img:"shots/noir/5-02-review.jpg",en:["Review the quote","Price impact 0.48%, <em>Minimum Received</em> 0.04292 SOL: you never get less than that. Tap <em>Confirm</em>."]},
        {img:"shots/noir/5-03-waiting.jpg",en:["Wait for the deposit","Noir sends your ZEC to the swap's deposit address and waits for it to confirm. Don't send again; the SOL follows once it's confirmed."]},
        {img:"shots/noir/5-04-completed.jpg",en:["Swap confirmed","In Activity the swap shows <em>Confirmed</em>: From your <em>Shielded</em> address To a <em>Transparent</em> destination, and a <em>Solana Hash</em> for the delivery on the other chain. <a href=\"https://solscan.io/tx/2HMLAZo9uTHNSLEWcGSRDMepk8d7YfGXSWWQvicVgyjK4aNskXFm1aaBsHBNdKZbKNQZppGRX8buSbtP1soR891N?cluster=mainnet-beta\" target=\"_blank\" rel=\"noopener\">See it on Solscan ↗</a>"]}
      ]
    }},
  zingo:{name:"Zingo",plat:"iOS · Android",site:"zingolabs.org",swap:false,desk:false,shots:{},
    steps:{
      setup:[
        {img:"shots/zingo/1-01-welcome.jpg",en:["Create a new wallet","Open Zingo and tap <em>Create New Wallet</em>. The screen also shows which server you're connected to (Mainnet)."]},
        {img:"shots/zingo/1-02-seed.jpg",en:["Write down the seed and birthday","Zingo shows 24 words (blurred here) and the <em>Wallet Birthday</em>, the block your wallet was born in. Write both on paper, then tap <em>I have saved the seed</em>. <em>These words are your wallet.</em>"]},
        {img:"shots/zingo/1-03-home.jpg",en:["You're in","The home screen shows your balance (0) and history. The three buttons at the bottom are Home, Send and Receive."]}
      ],
      get:[
        {img:"shots/zingo/2-01-receive-shielded.jpg",en:["Open Receive","Tap the receive button at the bottom right. Zingo shows your <em>Shielded Address</em>, labelled <em>Orchard (Ironwood)</em>. Share this by default."]},
        {img:"shots/zingo/2-02-receive-transparent.jpg",en:["Transparent, only if you must","Tap the title to switch to <em>Transparent Address</em>. Zingo marks it <em>Exposed transparent</em>: anything sent here is public."]},
        {img:"shots/zingo/2-03-mempool.jpg",en:["Watch it arrive","The payment appears as <em>Receiving…</em>, <em>In Mempool</em>: broadcast, waiting for a block."]},
        {img:"shots/zingo/2-04-receiving.jpg",en:["Tap it for details","Pool: <em>Ironwood</em>. There's no sender field at all: a shielded payment doesn't reveal it."]},
        {img:"shots/zingo/2-05-received.jpg",en:["Received, almost spendable","After the first block it says <em>Received</em>. Zingo waits for <em>3 confirmations</em> before you can spend it, about four minutes."]}
      ],
      shield:[
        {img:"shots/zingo/3-01-transparent-in.jpg",en:["Transparent ZEC arrives","Here 0.001 ZEC came to the transparent address. The details say <em>Pool: Transparent</em>: this one is public."]},
        {img:"shots/zingo/3-02-pools.jpg",en:["See every pool at a glance","Zingo's <em>Fund Pools</em> screen splits your balance by pool: Ironwood (UA), Orchard (legacy), Sapling and Transparent."]},
        {img:"shots/zingo/3-03-shield-button.jpg",en:["Tap Shield funds","Zingo shows a <em>Shield funds</em> button and the numbers: 0.00085 ZEC will be shielded, fee 0.00015 ZEC."]},
        {img:"shots/zingo/3-04-confirm.jpg",en:["Confirm","\"Confirm to shield your funds to the latest Ironwood shielded pool.\" Tap <em>Confirm</em>."]},
        {img:"shots/zingo/3-05-processing.jpg",en:["The proof is built","<em>Computing Transaction…</em> takes a few seconds on your phone."]},
        {img:"shots/zingo/3-06-shielding.jpg",en:["Shielding…","The transaction is transmitted. <em>View on block explorer</em> opens it publicly, and you'll see the transparent side there."]},
        {img:"shots/zingo/3-07-shielded.jpg",en:["Shielded","Status <em>Shielded</em>, pool <em>Ironwood</em>. After 3 confirmations it's spendable."]}
      ],
      send:[
        {img:"shots/zingo/4-01-send.jpg",en:["Open Send","Tap the paper plane. If <em>Spendable</em> shows 0, your last payment is still confirming. Wait a few minutes."]},
        {img:"shots/zingo/4-02-filled.jpg",en:["Fill in address, amount and memo","Paste a <em>u1</em> address or scan its QR. A green check confirms it's valid. Add an amount and an optional memo, then tap <em>Send</em>."]},
        {img:"shots/zingo/4-03-confirm-nym.jpg",en:["Confirm, routed through Nym","Zingo can send your transaction through the <em>Nym mixnet</em>, which hides your IP address from the network too. Privacy level: <em>Private, NYM Enhanced</em>. Tap <em>Confirm</em>."]},
        {img:"shots/zingo/4-04-processing.jpg",en:["Your phone builds the proof","<em>Computing Transaction…</em> is your device creating the zero-knowledge proof. It takes a few seconds."]},
        {img:"shots/zingo/4-05-created.jpg",en:["Transaction created","Tap <em>Continue</em>. Details are in your history."]},
        {img:"shots/zingo/4-06-home-sending.jpg",en:["It's on its way","History shows <em>Sending…</em>, <em>Transmitted</em>. The speech bubble means it carries a memo."]},
        {img:"shots/zingo/4-07-sending-detail.jpg",en:["Check the details","Amount, fee (0.0001 ZEC), recipient, pool <em>Ironwood</em> and your memo. Confirmations count up as blocks arrive."]}
      ],
      exit:[
        {img:"shots/zingo/5-01-send-t.jpg",en:["Paste the transparent address","Open Send and paste the exchange's deposit address, here a <em>t1</em> address. Enter the amount and tap <em>Send</em>. Memos can't travel to transparent addresses, so the memo field disappears."]},
        {img:"shots/zingo/5-02-confirm.jpg",en:["Check the privacy level","The confirm screen says <em>Privacy Level: Deshielded</em>: the amount and destination will be public. Fee here is 0.00015 ZEC. Tap <em>Confirm</em>."]},
        {img:"shots/zingo/5-03-processing.jpg",en:["The proof is built","Your phone computes the transaction. It still proves the funds came from the shielded pool without saying which notes."]},
        {img:"shots/zingo/5-04-created.jpg",en:["Transaction created","Tap <em>Continue</em>."]},
        {img:"shots/zingo/5-05-detail.jpg",en:["Pool: Transparent","The details show the recipient <em>t1</em> address and <em>Pool: Transparent</em>. Once it confirms, the exchange credits your account."]}
      ]
    }}
};
const CH=["setup","get","shield","send","exit"];

const T={
en:{
 "pf.more":"Every real transaction in this guide","pf.r1":"Received 0.005 ZEC, shielded","pf.r2":"Sent 0.0001 ZEC with a memo","pf.r3":"Received 0.005 ZEC, shielded","pf.r4":"Sent 0.001 ZEC with a memo, via Nym","pf.r5":"Unshielded 0.001 ZEC to t1Uaqcq6…Go2f","pf.r6":"Shielded 0.001 ZEC from t1Uaqcq6…Go2f","pf.r1v":"Fee only","pf.r2v":"Fee only","pf.r3v":"Fee only","pf.r4v":"Fee only","pf.r5v":"Address + amount","pf.r6v":"Address + amount","pf.r7":"Unshielded 0.001 ZEC to t1SZkuc…B4my","pf.r7v":"Address + amount","pf.r8":"Swap out: 0.004 ZEC to the swap's deposit address","pf.r8v":"Address + amount","pf.r9":"Received 0.005 ZEC, shielded","pf.r9v":"Fee only","pf.r10":"Received 0.001 ZEC on the transparent address t1Wzzj…1ni8q","pf.r10v":"Address + amount","pf.r11":"Sent 0.0001 ZEC with a memo","pf.r11v":"Fee only","pf.r12":"Shielded 0.001 ZEC from t1Wzzj…1ni8q","pf.r12v":"Address + amount","pf.r13":"Received 0.005 ZEC, shielded","pf.r13v":"Fee only","pf.r14":"Unshielded 0.001 ZEC to t1e4J8…7nDCjn","pf.r14v":"Address + amount","pf.r15":"Shielded 0.005 ZEC from t1Lmua…hKEpiE","pf.r15v":"Address + amount",
 "pf.lesson":"<b>Read the pills.</b> Every <em>Fee only</em> row is a payment that stayed inside the shielded pool: the explorer shows nothing but the fee. Every <em>Address + amount</em> row touched a transparent address, so the address and amount are public. Look at 5 and 6: the same transparent address receives 0.001 ZEC and spends 0.001 ZEC twelve minutes later, and anyone can connect the two. A swap out (10) crosses the same public boundary as a send to a transparent address (7).",
 "pf.n":"Proof","pf.h":"A real transaction, two views","pf.p":"This is the Vizor payment from chapter 02, mined on Zcash mainnet on 2 October 2026 at 21:21 UTC. Left: what Vizor shows its owner. Right: what anyone sees on a public block explorer.","pf.w":"In the wallet","pf.e":"On the explorer","pf.pool":"Pool","pf.time":"Time","pf.blk":"Block","pf.type":"Type","pf.io":"Public in / out","pf.amt":"Amount","pf.hidden":"not shown","pf.open":"Open on explorer ↗",
 "lk2.h":"Not shown on purpose","lk2.p":"Your recovery phrase never belongs in a screenshot, so this guide doesn't show one. Write the words on paper.","lk.h":"Screenshots blocked","lk.p":"This screen can't be captured. That's deliberate: a photo in your gallery or cloud is a copy of your wallet someone could steal.",
 "sb.h":"Your 24 words are your money","sb.p":"Not a password, not a login. Whoever has these words controls your ZEC from any device, and if you lose them, nobody can recover your funds. Not the wallet team, not anyone.",
 "sb.l":[["Paper, never pixels","No screenshots, photos, cloud notes or password managers synced online."],["Never share them","No real support person, airdrop or website will ever ask for them. Anyone who does is stealing."],["Two copies, two places","Keep them somewhere safe from fire and water, and away from your phone."]],
 brand:"Zero to Shielded",eyebrow:"Zcash wallet guide · 4 wallets · real screens",
 h1:"From zero to your first shielded transaction",
 lead:"Pick your wallet and follow its real screens, step by step. After each step you'll see exactly what the blockchain can see.",
 f1:"chapters",f2:"start to finish",f3:"typical fee",
 "cmp.h":"One payment, seen from the outside",pub:"Transparent",shd:"Shielded",
 "k.from":"From","k.to":"To","k.amt":"Amount","k.memo":"Memo","k.fee":"Fee",
 "cmp.t":"<b>Transparent:</b> anyone can read who paid whom, and how much. Forever.",
 "cmp.s":"<b>Shielded:</b> only the fee is public. Sender, recipient, amount and memo are encrypted.",
 pick:"Your wallet",
 ph:"Screenshot coming",step:"Step",
 "chain.h":"What the chain sees",
 "setup.t":"Install and back up","setup.p":"Create a self-custody wallet. Only you hold the keys, so the backup is the most important part.",
 "setup.s":[["Download from the official site","Go to <em>{site}</em> and use its store or download link. Never install from search ads."],["Create a new wallet","Choose <em>Create new wallet</em>."],["Write down the recovery phrase","24 words on paper, in order. Also write the <em>birthday height</em> next to them."],["Confirm the backup","The app asks for some of the words to check you wrote them down."],["Lock the app","Turn on a passcode or biometrics."]],
 "setup.c":["none","Nothing yet","Creating a wallet happens on your device. Nothing is written to the blockchain."],
 "setup.tip":"<b>Anyone with your 24 words controls your ZEC.</b> Never photograph them, store them in the cloud, or type them into a website.",
 "get.t":"Get ZEC","get.p":"Two easy ways in. A swap inside the wallet delivers shielded ZEC directly. Many exchanges send transparent ZEC, which you shield in the next chapter.",
 "get.s":[["Open Receive","Your wallet shows a shielded address starting with <em>u1</em>. Share this one by default."],["Or swap from another coin","Tap <em>Swap</em>, pick the asset and network you're paying with, add a refund address, and send. It arrives shielded."],["Or withdraw from an exchange","If the exchange won't accept the <em>u1</em> address, use the transparent address shown on Receive."],["Wait for it to arrive","A new block comes about every 75 seconds."]],
 "get.noswap":"The {w} mobile app has no built-in swap. Use an exchange or ask a friend to send to your u1 address.",
 "get.c":["mix","Depends on the route","From a shielded friend: nothing but the fee. In-wallet swap: the amount entering the pool is visible, you are not. Exchange withdrawal to a t1 address: address and amount are public and tied to your exchange account."],
 "shield.t":"Shield it","shield.p":"Shielding moves ZEC from a transparent address into the shielded pool. From then on, it's private.",
 "shield.s":[["Spot the transparent balance","When transparent ZEC arrives, the wallet offers to shield it."],["Tap Shield","Shield the whole transparent balance in one go."],["Review the fee","Usually 0.0001 ZEC."],["Confirm and wait","After a few blocks the shielded balance is ready to spend."]],
 "shield.c":["mix","The crossing is visible","Observers see your transparent address put this amount into the pool, then lose track of it."],
 "shield.tip":"<b>Shield everything at once.</b> Many small shields from the same address are easy to link together.",
 "send.t":"Send and receive privately","send.p":"Between two shielded addresses, nothing but the fee is public. You can attach an encrypted memo only the recipient can read.",
 "send.s":[["Tap Send","Paste or scan the recipient's address. Check it starts with <em>u1</em>."],["Enter amount and memo","The memo is optional and encrypted, up to 512 bytes."],["Review and confirm","Your device builds a zero-knowledge proof, then broadcasts."],["Receive","Share your u1 address or QR code. Incoming memos appear in your activity."]],
 "send.c":["priv","Nothing but the fee","Sender, recipient, amount and memo are all encrypted."],
 "exit.t":"Unshield when needed","exit.p":"Some exchanges only accept transparent deposits. Sending there makes the amount and destination public, but your shielded history stays hidden.",
 "exit.s":[["Copy the exchange deposit address","Often starts with <em>t1</em> or <em>tex1</em>."],["Send to it","Paste the address in Send, check the amount and the first characters of the address, then confirm."],["Confirm","The exchange credits you after its confirmations."]],
 "exit.c":["mix","Destination and amount","Where it goes and how much are public. Where it came from is not."],
 "exit.tip":"<b>Avoid easy links.</b> Don't unshield the exact amount you just shielded, right after. Vary amounts and timing.",
 "n.pool":"Shielded pool","n.you":"You","n.ex":"Exchange","n.dev":"Your device","n.recv":"Recipient","n.src":"Source",
 "fin.h":"Your checklist","fin.p":"Do it for real with a small amount first. Ticks are saved in this browser.",
 ck:["Install a shielded wallet from its official site","Write the 24 words and birthday height on paper","Get a small amount of ZEC","Shield anything that arrived transparent","Send a shielded payment with a memo","Receive one back","Unshield only when a service requires it"],
 iron:"<b>Held shielded ZEC before 28 July 2026?</b> Ironwood activated that day at block 3,428,143, and the old Orchard pool now only allows withdrawals. Update your wallet to its latest version and follow its migration prompt. The private option moves your balance in several smaller transfers over time. There is no deadline: Orchard funds do not expire.",
 q1:"What does it cost?",a1:"Usually 0.0001 ZEC per payment, and 0.00015 ZEC when a transparent coin is spent, as in shielding (ZIP 317). Swaps add the provider's spread.",
 q2:"I lost my phone. Is my ZEC gone?",a2:"Not if you have your 24 words. Install a wallet on a new device, choose Restore, and enter the words and birthday height.",
 q3:"Can I use a hardware wallet?",a3:"Yes. Keystone signs shielded transactions with Zodl and Vizor (Ironwood needs Keystone firmware 3.0.4 or later).",
 q4:"Can anyone see my shielded balance?",a4:"Not from the chain. You can choose to share a viewing key, for example with an accountant, without giving spending access.",
 src:"Sources",
 foot1:"Made by Zcash Türkiye for Zecmap. Entered in the ZECATHON Wildcard track.",foot2:"Current as of October 2026. Not financial advice."
}};

let lang="en",wallet="zodl";const cur={};
try{wallet=localStorage.getItem("zc-wallet")||wallet;}catch(e){}
if(!WALLETS[wallet])wallet="zodl";
const $=id=>document.getElementById(id);
const t=k=>T.en[k]??k;
const fill=(s,w)=>s.replace("{site}",w.site).replace("{w}",w.name);
const save=()=>{try{localStorage.setItem("zc-wallet",wallet);}catch(e){}};

function flowHTML(ch){
  const N=(c,s,b)=>`<div class="node ${c}"><small>${s}</small><b>${b}</b></div>`;
  const H='<span class="hidebar"></span>';
  const tr=p=>`<div class="track ${p?"pubt":""}"><i class="dot"></i></div>`;
  switch(ch){
    case"setup":return `<div class="flow">${N("x",t("n.dev"),"24 words")}${tr()}${N("x","Blockchain","—")}</div>`;
    case"get":return `<div class="flow">${N("t",t("n.src"),"t1…  0.005 ZEC")}${tr(1)}${N("s",t("n.pool"),t("n.you")+": "+H)}</div>`;
    case"shield":return `<div class="flow">${N("t",t("n.you"),"t1…  0.001 ZEC")}${tr(1)}${N("s",t("n.pool"),"0.00085 → "+H)}</div>`;
    case"send":return `<div class="flow">${N("s",t("n.you"),H)}${tr()}${N("s",t("n.recv"),H)}</div>`;
    case"exit":return `<div class="flow">${N("s",t("n.pool"),H)}${tr(1)}${N("t",t("n.ex"),"t1SZkuc…B4my  0.001 ZEC")}</div>`;
  }
}

function render(){
  document.querySelectorAll("[data-i]").forEach(el=>el.innerHTML=t(el.dataset.i));
  setView($("cmp").classList.contains("sh"));
  $("tabs").innerHTML=Object.entries(WALLETS).map(([k,w])=>`<button type="button" class="tab" data-w="${k}" aria-pressed="${k===wallet}">${w.name}<small>${w.plat}</small></button>`).join("");
  const w=WALLETS[wallet];
  $("chapters").innerHTML=CH.map((ch,ci)=>{
    const c=t(ch+".c");const custom=w.steps&&w.steps[ch];
    const noswap=ch==="get"&&!w.swap;
    const list=custom?custom.map(x=>x[lang]||x.en):t(ch+".s").filter((_,i)=>!(noswap&&i===1));
    return `<section class="chapter" id="ch-${ch}">
      <div class="ch-top"><span class="ch-n">0${ci+1}</span><h2>${t(ch+".t")}</h2><p>${t(ch+".p")}</p></div>
      <div class="walk ${w.desk?"wide":""}">
        <div class="phone ${w.desk?"desk":""} ${w.ext?"ext":""}" id="ph-${ch}"><div class="screen"></div><span class="counter"></span></div>
        <div style="display:grid;gap:14px;min-width:0">
          <ol class="steps">${list.map((s,i)=>`<li><button type="button" class="step" data-ch="${ch}" data-s="${i}"><b>${s[0]}</b><span>${fill(s[1],w)}</span></button></li>`).join("")}</ol>
          ${noswap?`<p class="na">${fill(t("get.noswap"),w)}</p>`:""}
        </div>
      </div>
      ${ch==="setup"?`<div class="seedbox"><h3>${t("sb.h")}</h3><p>${t("sb.p")}</p><ul>${t("sb.l").map(x=>`<li><b>${x[0]}</b>${x[1]}</li>`).join("")}</ul></div>`:T.en[ch+".tip"]?`<p class="tip ${ch==="exit"?"w":""}">${t(ch+".tip")}</p>`:""}
      <div class="chain"><div class="chain-h"><h3>${t("chain.h")}</h3><span class="pill ${c[0]}">${c[1]}</span></div>${flowHTML(ch)}<p>${c[2]}</p></div>
    </section>`;}).join("");
  CH.forEach(ch=>showStep(ch,cur[ch]||0));
  $("check").innerHTML=T[lang].ck.map((x,i)=>`<li><label for="ck${i}"><input type="checkbox" id="ck${i}" data-k="${i}" ${checks[i]?"checked":""}><span>${x}</span></label></li>`).join("");
}
function showStep(ch,i){
  const w=WALLETS[wallet];const steps=document.querySelectorAll(`.step[data-ch="${ch}"]`);
  if(!steps.length)return;i=Math.min(i,steps.length-1);cur[ch]=i;
  steps.forEach((s,j)=>s.setAttribute("aria-current",j===i));
  document.querySelectorAll(`.step[data-ch="${ch}"] .inl`).forEach(x=>x.remove());
  {const cw=WALLETS[wallet].steps&&WALLETS[wallet].steps[ch];if(cw&&cw[i]&&cw[i].fit&&cw[i].img){const im=document.createElement("img");im.className="inl";im.src=BASE+cw[i].img;im.alt="";steps[i].appendChild(im);}}
  const ph=$("ph-"+ch);const scr=ph.querySelector(".screen");const custom=w.steps&&w.steps[ch];const src=custom?custom[i].img:(w.shots[ch]||[])[i];const fit=custom&&custom[i].fit;const lock=custom&&custom[i].lock;
  scr.style.opacity=0;ph.classList.toggle('has-img',!!src||!!lock);
  setTimeout(()=>{scr.innerHTML=lock?`<div class="locked"><div class="lk-ic"><svg width="26" height="30" viewBox="0 0 26 30" fill="none"><rect x="2" y="13" width="22" height="15" rx="3" stroke="currentColor" stroke-width="2.2"/><path d="M7 13V9a6 6 0 0 1 12 0v4" stroke="currentColor" stroke-width="2.2"/></svg></div><b>${t(lock==="hide"?"lk2.h":"lk.h")}</b><p>${t(lock==="hide"?"lk2.p":"lk.p")}</p><div class="lk-words">${Array.from({length:24},(_,k)=>`<span>${k+1}</span>`).join("")}</div></div>`:src?`<img class="${fit?"fit":""}" src="${BASE+src}" alt="${w.name} · ${steps[i].querySelector("b").textContent}">`
    :`<div class="ph"><span class="ic">▢</span><b>${w.name}</b>${t("ph")}<span>${t("step")} ${i+1} · ${steps[i].querySelector("b").textContent}</span></div>`;scr.style.opacity=1;},150);
  ph.querySelector(".counter").textContent=`${i+1} / ${steps.length}`;
}
function setView(sh){$("cmp").classList.toggle("sh",sh);$("v-s").setAttribute("aria-pressed",sh);$("v-t").setAttribute("aria-pressed",!sh);$("cmp-foot").innerHTML=sh?t("cmp.s"):t("cmp.t");}

let checks={};try{checks=JSON.parse(localStorage.getItem("zc-checks")||"{}");}catch(e){}
document.addEventListener("click",e=>{
  const st=e.target.closest(".step");if(st)return showStep(st.dataset.ch,+st.dataset.s);
  const ph=e.target.closest(".phone");if(ph){const ch=ph.id.slice(3);const n=document.querySelectorAll(`.step[data-ch="${ch}"]`).length;return showStep(ch,((cur[ch]||0)+1)%n);}
  const tb=e.target.closest(".tab");if(tb){wallet=tb.dataset.w;save();render();}
});
document.addEventListener("change",e=>{if(e.target.dataset.k!=null){checks[e.target.dataset.k]=e.target.checked;try{localStorage.setItem("zc-checks",JSON.stringify(checks));}catch(x){}}});
let auto=true;$("v-t").onclick=()=>{auto=false;setView(false);};$("v-s").onclick=()=>{auto=false;setView(true);};
if(!(window.matchMedia&&matchMedia("(prefers-reduced-motion: reduce)").matches))setInterval(()=>{if(auto)setView(!$("cmp").classList.contains("sh"));},3800);
render();
(function(){const L=new Image();L.alt="Zcash";L.className="zlogo";L.onload=()=>{const m=document.getElementById("brand-mark");if(m)m.replaceWith(L);};L.src=BASE+"zcash-logo.png";})();
(function(){const r=document.documentElement,b=document.getElementById("theme"),z=document.querySelector(".zts");
if(!b||!z||z.hasAttribute("data-embed"))return;
try{const s=localStorage.getItem("zc-theme");if(s&&!r.classList.contains("dark")&&!r.classList.contains("light"))r.classList.add(s);}catch(e){}
const isDark=()=>r.classList.contains("dark")||r.dataset.theme==="dark"||(!r.classList.contains("light")&&r.dataset.theme!=="light"&&window.matchMedia&&matchMedia("(prefers-color-scheme: dark)").matches);
b.onclick=()=>{const d=!isDark();r.classList.toggle("dark",d);r.classList.toggle("light",!d);r.removeAttribute("data-theme");try{localStorage.setItem("zc-theme",d?"dark":"light");}catch(e){}};})();
</script>
@endverbatim
