<div class="biz-page">
    <a class="biz-page-back-link" href="{{ route('browse') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-chevron-left" aria-hidden="true">
            <path d="m15 18-6-6 6-6"></path>
        </svg>
        Explore
    </a>

    <div class="biz-page-layout">
        <aside class="biz-page-aside">
            <div class="biz-page-map-card">
                <a class="biz-page-map-main-link"
                    href="{{ $business->lat && $business->lng ? route('map', ['business' => $business->slug]) : route('map') }}">
                    View on Map
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-external-link" aria-hidden="true">
                        <path d="M15 3h6v6"></path>
                        <path d="M10 14 21 3"></path>
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    </svg>
                </a>
                <div class="biz-page-map-inner biz-page-map-inner--placeholder">
                    @if ($business->logoDisplayUrl())
                        <img alt="{{ $business->name }}" class="biz-page-map-hero-logo"
                            src="{{ $business->logoDisplayUrl() }}">
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-map-pin" aria-hidden="true">
                            <path
                                d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                            </path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    @endif
                </div>
                <div class="biz-page-map-overlay">
                    <div class="biz-page-map-identity">
                        <div class="biz-page-map-text">
                            <p class="biz-page-map-name">{{ $business->name }}</p>
                            @if ($business->city)
                                <p class="biz-page-map-location">{{ $business->city }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <style>
                    .biz-page-map-hero-logo{width:96px;height:96px;object-fit:contain;border-radius:18px;background:rgba(255,255,255,.92);padding:10px;box-shadow:0 6px 20px rgba(0,5,28,.16)}
                </style>
            </div>

            <div class="biz-page-sidebar-badges">
                @if ($business->category?->name)
                    <span class="biz-page-category">{{ $business->category->name }}</span>
                @endif
                @if ($business->verified)
                    <span class="biz-page-verified-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-badge-check" aria-hidden="true">
                            <path
                                d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z">
                            </path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                        Verified
                    </span>
                @endif
            </div>

            <div class="biz-page-quick-actions">
                @if ($business->googleMapsDirectionsUrl())
                    <a class="biz-page-quick-action" href="{{ $business->googleMapsDirectionsUrl() }}" target="_blank"
                        rel="noopener noreferrer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-navigation" aria-hidden="true">
                            <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                        </svg>
                        <span>Navigate</span>
                    </a>
                @endif
                @if ($business->phone)
                    <a class="biz-page-quick-action" href="tel:{{ $business->phone }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-phone" aria-hidden="true">
                            <path
                                d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384">
                            </path>
                        </svg>
                        <span>Call</span>
                    </a>
                @else
                    <span class="biz-page-quick-action biz-page-quick-action--disabled" aria-disabled="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-phone" aria-hidden="true">
                            <path
                                d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384">
                            </path>
                        </svg>
                        <span>Call</span>
                    </span>
                @endif
                <button type="button" class="biz-page-quick-action" data-business-share
                    data-share-url="{{ url()->current() }}" data-share-title="{{ $business->name }}"
                    data-share-text="{{ $business->description ? \Illuminate\Support\Str::limit(strip_tags($business->description), 120) : __('browse.subtitle') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-share-2" aria-hidden="true">
                        <circle cx="18" cy="5" r="3"></circle>
                        <circle cx="6" cy="12" r="3"></circle>
                        <circle cx="18" cy="19" r="3"></circle>
                        <line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line>
                        <line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line>
                    </svg>
                    <span>{{ __('business.share') }}</span>
                </button>
            </div>

            @if ($lastVerification)
                <div class="biz-page-last-verification">
                    <p class="biz-page-last-verification-label">Last verified by</p>
                    <p class="biz-page-last-verification-name">
                        {{ $lastVerification->user?->name ?? 'Community member' }}</p>
                    <p class="biz-page-last-verification-date">
                        Verified on {{ $lastVerification->reviewed_at?->format('M j, Y') }}
                    </p>
                </div>
            @endif

            <div class="biz-page-sidebar-footer">
                @auth
                    @if ($hasPendingVerification)
                        <p class="biz-page-verify-pending">Your verification request is pending review.</p>
                    @endif
                    <button type="button" class="biz-page-verify-button biz-page-verify-button--compact"
                        data-open-verify-modal @disabled($hasPendingVerification)>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-map-pin-check" aria-hidden="true">
                            <path
                                d="M19.43 12.935c.357-.967.57-1.955.57-2.935a8 8 0 0 0-16 0c0 4.993 5.539 10.193 7.399 11.799a1 1 0 0 0 1.202 0 32.197 32.197 0 0 0 .813-.728">
                            </path>
                            <circle cx="12" cy="10" r="3"></circle>
                            <path d="m16 18 2 2 4-4"></path>
                        </svg>
                        Verify Location
                    </button>
                    @if ($canRequestUpdate)
                        @if ($hasPendingUpdateRequest)
                            <p class="biz-page-verify-pending">Your update request is pending review.</p>
                        @endif
                        <button type="button" class="biz-page-verify-button biz-page-verify-button--compact"
                            data-open-update-modal @disabled($hasPendingUpdateRequest)>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-pencil-line" aria-hidden="true">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                            </svg>
                            Update this business
                        </button>
                    @endif
                @else
                    <a class="biz-page-verify-button biz-page-verify-button--compact" href="{{ route('auth') }}">
                        Log in to verify
                    </a>
                    @if ($canRequestUpdate)
                        <a class="biz-page-verify-button biz-page-verify-button--compact" href="{{ route('auth') }}">
                            Log in to suggest an update
                        </a>
                    @endif
                @endauth
            </div>
        </aside>

        <main class="biz-page-main">
            @if (!empty($business->zcash_address))
                @php $zecUri = \App\Support\Zip321::uri($business->zcash_address); @endphp
                <section class="biz-page-section">
                    <h2 class="biz-page-section-title">Accept Zcash</h2>
                    <style>#zec-qr svg{display:block;width:100%;height:100%}</style>
                    <div style="display:flex;gap:22px;flex-wrap:wrap;align-items:flex-start">
                        <div id="zec-qr" aria-label="Zcash QR code"
                             style="background:#fff;border:1px solid rgba(0,0,0,.12);border-radius:16px;padding:14px;width:196px;height:196px;box-sizing:border-box;flex:0 0 auto"></div>
                        <div style="flex:1;min-width:240px">
                            <p style="margin:0 0 14px;opacity:.72;font-size:.95rem;line-height:1.5">
                                Scan to send Zcash straight to this business. Payments go directly to their own wallet. ZecMap never touches the funds.
                            </p>
                            <div style="display:flex;align-items:center;gap:10px;border:1px solid rgba(128,128,128,.28);border-radius:12px;padding:10px 12px;margin-bottom:14px;max-width:440px">
                                <code id="zec-addr" style="font-size:.78rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1">{{ $business->zcash_address }}</code>
                                <button type="button" id="zec-copy" style="border:0;background:transparent;cursor:pointer;color:#C8921A;font-weight:700;font-size:.8rem;flex:0 0 auto">Copy</button>
                            </div>
                            <a class="biz-page-link-pill" href="{{ $zecUri }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                                Open in wallet
                            </a>
                            <a class="biz-page-link-pill" href="{{ route('business.poster', $business) }}" target="_blank" rel="noopener" style="margin-left:8px">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                Poster
                            </a>

                            <div id="zec-conv" hidden style="margin-top:18px;max-width:460px;border:1px solid rgba(128,128,128,.22);border-radius:14px;padding:14px">
                                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:11px">
                                    <span style="font-weight:700;font-size:.9rem">Price &amp; amount</span>
                                    <span id="zec-price" style="font-size:.8rem;opacity:.72">…</span>
                                </div>
                                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                                    <input id="zec-amt" type="number" min="0" step="0.01" inputmode="decimal" placeholder="Amount"
                                        style="width:108px;padding:9px 10px;border:1px solid rgba(128,128,128,.3);border-radius:10px;background:transparent;color:inherit;font:inherit">
                                    <select id="zec-cur" style="padding:9px 10px;border:1px solid rgba(128,128,128,.3);border-radius:10px;background:transparent;color:inherit;font:inherit">
                                        <option value="usd">USD</option>
                                        <option value="eur">EUR</option>
                                        <option value="try">TRY</option>
                                    </select>
                                    <span style="opacity:.55">=</span>
                                    <strong id="zec-out" style="font-variant-numeric:tabular-nums;min-width:96px">— ZEC</strong>
                                    <button type="button" id="zec-setqr" class="biz-page-link-pill" style="margin-left:auto">Set on QR</button>
                                </div>
                                <p style="margin:10px 0 0;font-size:.76rem;opacity:.6">Live price from CoinGecko. The amount is added to the QR and the wallet link.</p>
                            </div>
                        </div>
                    </div>
                </section>
                <script src="{{ asset('assets/js/qrcode.min.js') }}"></script>
                <script>
                    (function(){
                        var addr = @json($business->zcash_address);
                        var baseUri = @json($zecUri);
                        var openLink = document.querySelector('.biz-page-section a.biz-page-link-pill');
                        function renderQR(uri){
                            try { var qr = qrcode(0,'M'); qr.addData(uri); qr.make();
                                document.getElementById('zec-qr').innerHTML = qr.createSvgTag({cellSize:4, margin:0, scalable:true}); } catch(e){}
                        }
                        renderQR(baseUri);

                        var btn=document.getElementById('zec-copy'), a=document.getElementById('zec-addr');
                        if(btn&&a) btn.addEventListener('click',function(){ if(navigator.clipboard){navigator.clipboard.writeText(a.textContent.trim()).then(function(){btn.textContent='Copied';setTimeout(function(){btn.textContent='Copy';},1500);}).catch(function(){});} });

                        // Live price + amount converter (client side, degrades gracefully).
                        var prices=null, box=document.getElementById('zec-conv');
                        var amt=document.getElementById('zec-amt'), cur=document.getElementById('zec-cur'),
                            out=document.getElementById('zec-out'), setBtn=document.getElementById('zec-setqr'),
                            priceEl=document.getElementById('zec-price');
                        function fmtZec(n){ return (Math.round(n*1e8)/1e8).toString(); }
                        function money(n,c){ try{ return new Intl.NumberFormat(undefined,{style:'currency',currency:c}).format(n);}catch(e){ return n.toFixed(2)+' '+c.toUpperCase(); } }
                        function currentZec(){
                            if(!prices) return null;
                            var v=parseFloat(amt.value); if(!(v>0)) return null;
                            var p=prices[cur.value]; if(!(p>0)) return null;
                            return v/p;
                        }
                        function recalc(){
                            var z=currentZec();
                            out.textContent = z ? (fmtZec(z)+' ZEC') : '— ZEC';
                        }
                        if(amt&&cur&&setBtn){
                            amt.addEventListener('input',recalc); cur.addEventListener('change',recalc);
                            setBtn.addEventListener('click',function(){
                                var z=currentZec();
                                var uri = z ? ('zcash:'+addr+'?amount='+fmtZec(z)) : baseUri;
                                renderQR(uri);
                                if(openLink) openLink.setAttribute('href', uri);
                                setBtn.textContent = z ? 'Amount set' : 'Set on QR';
                                setTimeout(function(){ setBtn.textContent='Set on QR'; }, 1600);
                            });
                        }
                        fetch('https://api.coingecko.com/api/v3/simple/price?ids=zcash&vs_currencies=usd,eur,try')
                            .then(function(r){ return r.json(); })
                            .then(function(d){
                                if(!d||!d.zcash) throw 0;
                                prices=d.zcash;
                                priceEl.textContent='1 ZEC ≈ '+money(prices.usd,'usd')+' · '+money(prices.try,'try');
                                if(box) box.hidden=false;
                                recalc();
                            })
                            .catch(function(){ /* price unavailable: leave converter hidden */ });
                    })();
                </script>
            @endif
            <section class="biz-page-section">
                <h2 class="biz-page-section-title">Details</h2>
                <dl class="biz-page-detail-list">
                    @if ($business->description)
                        <div class="biz-page-detail-row">
                            <dt class="biz-page-detail-label">Description</dt>
                            <dd class="biz-page-detail-value biz-page-detail-value--multiline">
                                {{ $business->description }}
                            </dd>
                        </div>
                    @endif
                    @if ($business->fullAddress())
                        <div class="biz-page-detail-row">
                            <dt class="biz-page-detail-label">Address</dt>
                            <dd class="biz-page-detail-value">{{ $business->fullAddress() }}</dd>
                        </div>
                    @endif
                    @if ($business->city)
                        <div class="biz-page-detail-row">
                            <dt class="biz-page-detail-label">City</dt>
                            <dd class="biz-page-detail-value">{{ $business->city }}</dd>
                        </div>
                    @endif
                    @if ($business->country_code)
                        <div class="biz-page-detail-row">
                            <dt class="biz-page-detail-label">Country</dt>
                            <dd class="biz-page-detail-value">{{ $business->country_code }}</dd>
                        </div>
                    @endif
                </dl>
            </section>

            @if ($website || $twitterUrl)
                <section class="biz-page-section">
                    <h3 class="biz-page-section-label">Links</h3>
                    <div class="biz-page-links">
                        @if ($website)
                            <a href="{{ $website }}" target="_blank" rel="noopener noreferrer"
                                class="biz-page-link-pill">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe"
                                    aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                                    <path d="M2 12h20"></path>
                                </svg>
                                Website
                            </a>
                        @endif
                        @if ($twitterUrl)
                            <a href="{{ $twitterUrl }}" target="_blank" rel="noopener noreferrer"
                                class="biz-page-link-pill">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-at-sign"
                                    aria-hidden="true">
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"></path>
                                </svg>
                                X / Twitter
                            </a>
                        @endif
                    </div>
                </section>
            @endif

            @auth
                @include('business-detail.partials.reviews')
            @endauth
        </main>
    </div>
</div>
