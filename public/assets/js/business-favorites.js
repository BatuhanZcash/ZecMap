(function () {
    const STORAGE_KEY = "zecmap_favorites";
    const listeners = new Set();

    let slugs = new Set();
    let ready = false;
    let config = {
        authenticated: false,
        slugsUrl: "",
        toggleUrlTemplate: "",
        loginUrl: "",
        savedText: "Saved!",
        removedText: "Removed from saved",
        loginRequiredText: "Sign in to save businesses",
    };

    function loadLocal() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            const parsed = raw ? JSON.parse(raw) : [];
            if (Array.isArray(parsed)) {
                slugs = new Set(parsed.filter(Boolean));
            }
        } catch {
            slugs = new Set();
        }
    }

    function saveLocal() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(slugs)));
        } catch {
            // ignore quota errors
        }
    }

    function notify() {
        listeners.forEach(function (listener) {
            try {
                listener();
            } catch {
                // ignore listener errors
            }
        });
        document.dispatchEvent(new CustomEvent("zecmap:favorites-changed"));
    }

    function showToast(message) {
        const toastStack = document.querySelector(".toast-stack");
        if (!toastStack || !message) {
            return;
        }

        const toast = document.createElement("div");
        toast.className = "biz-share-toast";
        toast.setAttribute("role", "status");
        toast.textContent = message;
        toastStack.appendChild(toast);

        window.requestAnimationFrame(function () {
            toast.classList.add("biz-share-toast--visible");
        });

        window.setTimeout(function () {
            toast.classList.remove("biz-share-toast--visible");
            window.setTimeout(function () {
                toast.remove();
            }, 220);
        }, 2600);
    }

    function isSaved(slug) {
        return Boolean(slug && slugs.has(slug));
    }

    function getSlugs() {
        return Array.from(slugs);
    }

    async function syncFromServer() {
        if (!config.authenticated || !config.slugsUrl) {
            return;
        }

        const response = await fetch(config.slugsUrl, {
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "same-origin",
        });

        if (!response.ok) {
            throw new Error("Failed to load saved businesses");
        }

        const data = await response.json();
        slugs = new Set((data.slugs || []).filter(Boolean));
        saveLocal();
    }

    async function init(nextConfig) {
        config = Object.assign({}, config, nextConfig || {});
        loadLocal();

        if (config.authenticated) {
            try {
                await syncFromServer();
            } catch {
                // keep local cache
            }
        }

        ready = true;
        notify();
    }

    function onChange(listener) {
        if (typeof listener === "function") {
            listeners.add(listener);
        }

        return function () {
            listeners.delete(listener);
        };
    }

    async function toggle(slug) {
        if (!slug) {
            return false;
        }

        const wasSaved = slugs.has(slug);

        if (!config.authenticated) {
            showToast(config.loginRequiredText);
            if (config.loginUrl) {
                window.setTimeout(function () {
                    window.location.href = config.loginUrl;
                }, 900);
            }
            return wasSaved;
        }

        if (wasSaved) {
            slugs.delete(slug);
        } else {
            slugs.add(slug);
        }
        saveLocal();
        notify();

        const toggleUrl = (config.toggleUrlTemplate || "").replace(
            "__SLUG__",
            encodeURIComponent(slug),
        );

        if (!toggleUrl) {
            return !wasSaved;
        }

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        try {
            const response = await fetch(toggleUrl, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
            });

            if (!response.ok) {
                throw new Error("Toggle failed");
            }

            const data = await response.json();
            slugs = new Set((data.slugs || []).filter(Boolean));
            saveLocal();
            notify();
            showToast(data.saved ? config.savedText : config.removedText);
            return Boolean(data.saved);
        } catch {
            if (wasSaved) {
                slugs.add(slug);
            } else {
                slugs.delete(slug);
            }
            saveLocal();
            notify();
            throw new Error("Toggle failed");
        }
    }

    window.ZecMapFavorites = {
        init: init,
        isSaved: isSaved,
        toggle: toggle,
        getSlugs: getSlugs,
        onChange: onChange,
        isReady: function () {
            return ready;
        },
    };
})();
