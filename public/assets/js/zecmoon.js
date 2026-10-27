(function () {
    var container = document.getElementById("zecmoon-map");
    if (!container || typeof maplibregl === "undefined") return;

    var cfg = window.ZECMOON || {};
    var i18n = cfg.i18n || {};
    var isDark = document.documentElement.classList.contains("dark");

    /* ── Zcash logolu ZecMoon işareti (SVG) ── */
    function moonSvg(size) {
        return (
            '<svg width="' + size + '" height="' + size + '" viewBox="0 0 120 120" class="coin">' +
            '<defs><radialGradient id="zmg' + size + '" cx="38%" cy="30%">' +
            '<stop offset="0" stop-color="#ffd463"/><stop offset=".55" stop-color="#f4b728"/><stop offset="1" stop-color="#d99a12"/>' +
            "</radialGradient></defs>" +
            '<circle cx="60" cy="60" r="44" fill="none" stroke="#3dff9e" stroke-width="1.4" stroke-dasharray="2 6" opacity=".7"/>' +
            '<circle cx="60" cy="16" r="2.6" fill="#3dff9e"/>' +
            '<circle cx="100" cy="76" r="2" fill="#3dff9e"/>' +
            '<circle cx="60" cy="60" r="34" fill="url(#zmg' + size + ')"/>' +
            '<g transform="translate(60,60) scale(0.69) translate(-50,-50)">' +
            '<path fill="#ffffff" d="M30 30 L46 30 L46 22 L54 22 L54 30 L70 30 L70 38 L48.5 62 L70 62 L70 70 L54 70 L54 78 L46 78 L46 70 L30 70 L30 62 L51.5 38 L30 38 Z"/>' +
            "</g>" +
            '<path d="M31 88 A34 34 0 0 0 56 94" fill="none" stroke="#3dff9e" stroke-width="2" opacity=".6" stroke-linecap="round"/>' +
            "</svg>"
        );
    }

    /* ── Harita ── */
    var map = new maplibregl.Map({
        container: container,
        style: isDark ? cfg.styleUrlDark : cfg.styleUrlLight,
        center: [25, 20],
        zoom: 1.8,
        attributionControl: { compact: true },
    });
    map.addControl(new maplibregl.NavigationControl({ showCompass: false }), "bottom-right");

    map.on("load", function () {
        var features = (cfg.pins || []).map(function (p) {
            return {
                type: "Feature",
                geometry: { type: "Point", coordinates: [p.lng, p.lat] },
                properties: { p: p.p },
            };
        });
        map.addSource("businesses", {
            type: "geojson",
            data: { type: "FeatureCollection", features: features },
        });
        map.addLayer({
            id: "business-dots",
            type: "circle",
            source: "businesses",
            paint: {
                "circle-radius": ["interpolate", ["linear"], ["zoom"], 1, 2, 8, 3.5, 13, 6],
                "circle-color": ["case", ["==", ["get", "p"], "flexa"], "#9333EA", "#FFC109"],
                "circle-opacity": 0.85,
                "circle-stroke-width": 0.6,
                "circle-stroke-color": isDark ? "#01061c" : "#ffffff",
            },
        });
    });

    /* ── Düşüş uyarı sesi (WebAudio; kullanıcı kapatabilir) ── */
    var soundOn = true;
    try { soundOn = localStorage.getItem("zecmoon-sound") !== "off"; } catch (e) {}
    var audioCtx = null;
    var audioUnlocked = false;

    function unlockAudio() {
        if (audioUnlocked) return;
        try {
            audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === "suspended") audioCtx.resume();
            audioUnlocked = true;
        } catch (e) {}
    }
    document.addEventListener("click", unlockAudio, { once: false });
    document.addEventListener("keydown", unlockAudio, { once: false });

    function playDropSound() {
        if (!soundOn || !audioUnlocked || !audioCtx) return;
        try {
            var t = audioCtx.currentTime;
            // Karanlık, derin bir "uyarı" tonu: alçak sinüs + beşli aralık, yavaş sönüm
            [55, 82.5].forEach(function (freq, idx) {
                var osc = audioCtx.createOscillator();
                var gain = audioCtx.createGain();
                osc.type = idx === 0 ? "sine" : "triangle";
                osc.frequency.setValueAtTime(freq, t);
                osc.frequency.exponentialRampToValueAtTime(freq * 1.5, t + 0.9);
                gain.gain.setValueAtTime(0.0001, t);
                gain.gain.exponentialRampToValueAtTime(idx === 0 ? 0.22 : 0.1, t + 0.06);
                gain.gain.exponentialRampToValueAtTime(0.0001, t + 1.6);
                osc.connect(gain).connect(audioCtx.destination);
                osc.start(t);
                osc.stop(t + 1.7);
            });
        } catch (e) {}
    }

    var soundBtn = document.getElementById("zmn-sound");
    function renderSoundBtn() {
        if (!soundBtn) return;
        soundBtn.classList.toggle("zmn-pill--muted", !soundOn);
        soundBtn.setAttribute("aria-pressed", soundOn ? "true" : "false");
        soundBtn.querySelector(".zmn-sound-on").style.display = soundOn ? "" : "none";
        soundBtn.querySelector(".zmn-sound-off").style.display = soundOn ? "none" : "";
    }
    if (soundBtn) {
        renderSoundBtn();
        soundBtn.addEventListener("click", function () {
            soundOn = !soundOn;
            try { localStorage.setItem("zecmoon-sound", soundOn ? "on" : "off"); } catch (e) {}
            renderSoundBtn();
            if (soundOn) { unlockAudio(); playDropSound(); }
        });
    }

    /* ── Durum / spawn (çoklu) ── */
    var markers = {}; // spawn.id -> {marker, spawn}
    var firstFit = true;
    var statusDot = document.getElementById("zmn-status-dot");
    var statusText = document.getElementById("zmn-status-text");
    var weekCount = document.getElementById("zmn-week-count");

    function setStatus(count) {
        if (!statusDot) return;
        statusDot.classList.toggle("zmn-dot--idle", count === 0);
        statusText.textContent = count > 0
            ? i18n.activeCount.replace(":n", count)
            : i18n.scanning;
    }

    function addMoon(spawn) {
        if (markers[spawn.id]) return;

        var el = document.createElement("div");
        el.className = "zmn-moon";
        el.innerHTML =
            '<span class="ring"></span><span class="ring ring--late"></span>' + moonSvg(64);
        el.addEventListener("click", function (event) {
            event.stopPropagation();
            openCatch(spawn);
        });

        markers[spawn.id] = {
            spawn: spawn,
            marker: new maplibregl.Marker({ element: el, anchor: "center" })
                .setLngLat([spawn.lng, spawn.lat])
                .addTo(map),
        };
    }

    function removeMoon(id) {
        if (markers[id]) { markers[id].marker.remove(); delete markers[id]; }
    }

    var firstSync = true;
    function syncMoons(spawns) {
        var alive = {};
        var newOnes = 0;
        spawns.forEach(function (spawn) {
            alive[spawn.id] = true;
            if (!markers[spawn.id]) newOnes++;
            addMoon(spawn);
        });
        if (!firstSync && newOnes > 0) playDropSound();
        firstSync = false;
        Object.keys(markers).forEach(function (id) {
            if (!alive[id]) removeMoon(id);
        });

        if (firstFit && spawns.length > 0) {
            firstFit = false;
            if (spawns.length === 1) {
                map.flyTo({ center: [spawns[0].lng, spawns[0].lat], zoom: Math.max(map.getZoom(), 4), speed: 0.9 });
            } else {
                var bounds = new maplibregl.LngLatBounds();
                spawns.forEach(function (spawn) { bounds.extend([spawn.lng, spawn.lat]); });
                map.fitBounds(bounds, { padding: 120, maxZoom: 5, duration: 1400 });
            }
        }
    }

    function poll() {
        fetch(cfg.stateUrl, { headers: { Accept: "application/json" }, credentials: "same-origin" })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data) return;
                if (weekCount) weekCount.textContent = data.weekly_captured;
                setStatus(data.count || 0);
                syncMoons(data.spawns || []);
            })
            .catch(function () {});
    }
    if (!cfg.guest) {
        poll();
        setInterval(poll, 15000);
    }

    /* ── Modal yardımcıları ── */
    function open(id) { document.getElementById(id).classList.add("open"); }
    function close(id) { document.getElementById(id).classList.remove("open"); }
    document.querySelectorAll("[data-zmn-close]").forEach(function (btn) {
        btn.addEventListener("click", function () {
            document.querySelectorAll(".zmn-overlay.open").forEach(function (o) { o.classList.remove("open"); });
            stopTimer();
        });
    });

    document.getElementById("zmn-moon-mini").innerHTML = moonSvg(58);
    document.getElementById("zmn-moon-win").innerHTML = moonSvg(74);

    /* ── Yakalama akışı ── */
    var token = null;
    var timerId = null;

    function stopTimer() { if (timerId) { clearInterval(timerId); timerId = null; } }

    function openCatch(spawn) {
        if (!spawn) return;
        fetch(cfg.attemptUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": cfg.csrf,
                Accept: "application/json",
            },
            credentials: "same-origin",
            body: JSON.stringify({ spawn_id: spawn.id }),
        })
            .then(function (r) { return r.json().then(function (j) { return { s: r.status, j: j }; }); })
            .then(function (res) {
                if (!res.j.ok) { showMiss(res.j.reason || "taken"); poll(); return; }
                token = res.j.token;
                document.getElementById("zmn-a").textContent = res.j.a;
                document.getElementById("zmn-b").textContent = res.j.b;
                var input = document.getElementById("zmn-answer");
                input.value = "";
                open("zmn-catch");
                input.focus();
                startTimer(res.j.seconds);
            })
            .catch(function () {});
    }

    function startTimer(seconds) {
        stopTimer();
        var bar = document.getElementById("zmn-timer-bar");
        var left = seconds;
        bar.style.width = "100%";
        timerId = setInterval(function () {
            left -= 0.25;
            bar.style.width = Math.max(0, (left / seconds) * 100) + "%";
            if (left <= 0) {
                stopTimer();
                close("zmn-catch");
                showMiss("timeout");
            }
        }, 250);
    }

    function submit() {
        var answer = parseInt(document.getElementById("zmn-answer").value, 10);
        if (isNaN(answer) || !token) return;
        stopTimer();
        fetch(cfg.captureUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": cfg.csrf,
                Accept: "application/json",
            },
            credentials: "same-origin",
            body: JSON.stringify({ token: token, answer: answer }),
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                close("zmn-catch");
                token = null;
                if (data.ok) {
                    showWin(data);
                } else {
                    showMiss(data.reason || "taken");
                }
                poll();
            })
            .catch(function () {});
    }
    document.getElementById("zmn-submit").addEventListener("click", submit);
    document.getElementById("zmn-answer").addEventListener("keydown", function (e) {
        if (e.key === "Enter") submit();
    });

    function showWin(data) {
        document.getElementById("zmn-seq").textContent = data.seq_no;
        document.getElementById("zmn-win-text").textContent = i18n.winText
            .replace(":seq", data.seq_no)
            .replace(":week", data.week_captures)
            .replace(":total", data.total_captures);
        var shareLink = cfg.shareUrl + "?c=" + data.seq_no;
        var text = i18n.shareText.replace(":seq", data.seq_no) + " " + shareLink;
        document.getElementById("zmn-share").href =
            "https://twitter.com/intent/tweet?text=" + encodeURIComponent(text);
        open("zmn-win");
        loadLeaderboard();
    }

    function showMiss(reason) {
        var msg = reason === "timeout" ? i18n.missTimeout
            : reason === "wrong" ? i18n.missWrong
            : reason === "toofast" ? i18n.missToofast
            : reason === "limit" ? i18n.missLimit
            : i18n.missTaken;
        document.getElementById("zmn-miss-text").textContent = msg;
        open("zmn-miss");
    }

    /* ── Liderlik ── */
    var lb = document.getElementById("zmn-lb");
    var lbTab = "week";
    var lbClose = document.getElementById("zmn-lb-close");
    if (lbClose) lbClose.addEventListener("click", function () { lb.classList.remove("open"); });
    document.getElementById("zmn-lb-open").addEventListener("click", function () {
        lb.classList.toggle("open");
        if (lb.classList.contains("open")) loadLeaderboard();
    });
    document.addEventListener("click", function (e) {
        if (lb.classList.contains("open") && !lb.contains(e.target) && e.target.id !== "zmn-lb-open") {
            lb.classList.remove("open");
        }
    });
    document.querySelectorAll(".zmn-tab").forEach(function (tab) {
        tab.addEventListener("click", function () {
            document.querySelectorAll(".zmn-tab").forEach(function (t) { t.classList.remove("on"); });
            tab.classList.add("on");
            lbTab = tab.getAttribute("data-tab");
            loadLeaderboard();
        });
    });

    function initialOf(name) { return (name || "?").charAt(0).toUpperCase(); }

    function loadLeaderboard() {
        var body = document.getElementById("zmn-lb-body");
        fetch(cfg.leaderboardUrl + (lbTab === "all" ? "?all_time=1" : ""), {
            headers: { Accept: "application/json" },
            credentials: "same-origin",
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var rows = data.rows || [];
                if (!rows.length) {
                    body.innerHTML = '<div class="zmn-empty">' + i18n.empty + "</div>";
                } else {
                    body.innerHTML = rows
                        .map(function (row) {
                            var podium = lbTab === "week" && row.rank <= 3;
                            var prize = podium ? "$" + data.prizes[row.rank - 1] + " ZEC" : "";
                            var avatar = row.avatar_url
                                ? '<img src="' + row.avatar_url + '" alt="">'
                                : initialOf(row.username);
                            return (
                                '<div class="zmn-row' + (podium ? " podium" : "") + '">' +
                                '<span class="zmn-rank">' + row.rank + "</span>" +
                                '<span class="zmn-av">' + avatar + "</span>" +
                                '<span class="zmn-row-user">' + row.username + "</span>" +
                                '<span class="zmn-row-caps"><b>' + row.captures + "</b> " + i18n.captures + "</span>" +
                                '<span class="zmn-prize">' + prize + "</span>" +
                                "</div>"
                            );
                        })
                        .join("");
                }
                var foot = document.getElementById("zmn-lb-foot");
                if (lbTab === "week" && data.week_resets_at) {
                    var diff = new Date(data.week_resets_at).getTime() - Date.now();
                    var d = Math.max(0, Math.floor(diff / 86400000));
                    var h = Math.max(0, Math.floor((diff % 86400000) / 3600000));
                    foot.innerHTML = i18n.resetsIn.replace(":time", "<b>" + d + "g " + h + "s</b>") + " · " + i18n.rules;
                } else {
                    foot.textContent = i18n.rules;
                }
            })
            .catch(function () {});
    }
})();
