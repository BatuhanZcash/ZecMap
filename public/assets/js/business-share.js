(function () {
    const sheet = document.querySelector("[data-business-share-sheet]");
    const shareButtons = document.querySelectorAll("[data-business-share]");

    if (!sheet || !shareButtons.length) {
        return;
    }

    const nameEl = sheet.querySelector("[data-business-share-name]");
    const linkInput = sheet.querySelector("[data-business-share-link]");
    const copyBtn = sheet.querySelector("[data-share-action='copy']");
    const copyLabel = copyBtn?.querySelector("[data-business-share-copy-label]");
    const nativeBtn = sheet.querySelector("[data-share-action='native']");
    const toastStack = document.querySelector(".toast-stack");

    const copySuccessText =
        copyBtn?.getAttribute("data-copy-success") ||
        copyLabel?.textContent ||
        "Link copied!";

    let activeShare = null;
    let copyResetTimer = null;

    function encode(value) {
        return encodeURIComponent(value || "");
    }

    function showToast(message) {
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

    function setSheetOpen(isOpen) {
        sheet.hidden = !isOpen;
        sheet.setAttribute("aria-hidden", isOpen ? "false" : "true");
        document.body.classList.toggle("biz-share-sheet-open", isOpen);

        if (isOpen) {
            sheet.querySelector(".biz-share-sheet-close")?.focus();
            return;
        }

        activeShare = null;
    }

    function updateShareTargets(data) {
        const message = data.text
            ? `${data.title} — ${data.text}`
            : data.title;

        sheet.querySelector("[data-share-action='whatsapp']").href =
            "https://wa.me/?text=" + encode(`${message}\n${data.url}`);

        sheet.querySelector("[data-share-action='x']").href =
            "https://twitter.com/intent/tweet?text=" +
            encode(data.title) +
            "&url=" +
            encode(data.url);

        sheet.querySelector("[data-share-action='telegram']").href =
            "https://t.me/share/url?url=" +
            encode(data.url) +
            "&text=" +
            encode(data.title);

        sheet.querySelector("[data-share-action='email']").href =
            "mailto:?subject=" +
            encode(data.title) +
            "&body=" +
            encode(`${message}\n\n${data.url}`);
    }

    function getShareData(button) {
        return {
            url: button.getAttribute("data-share-url") || window.location.href,
            title: button.getAttribute("data-share-title") || document.title,
            text: button.getAttribute("data-share-text") || "",
        };
    }

    function openShareSheet(button) {
        activeShare = getShareData(button);

        if (nameEl) {
            nameEl.textContent = activeShare.title;
        }

        if (linkInput) {
            linkInput.value = activeShare.url;
        }

        updateShareTargets(activeShare);
        setSheetOpen(true);
    }

    async function copyShareLink() {
        if (!activeShare) {
            return;
        }

        const url = activeShare.url;

        try {
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(url);
            } else {
                if (!linkInput) {
                    throw new Error("clipboard unavailable");
                }
                linkInput.removeAttribute("readonly");
                linkInput.focus();
                linkInput.select();
                document.execCommand("copy");
                linkInput.setAttribute("readonly", "readonly");
            }

            showToast(copySuccessText);

            if (copyLabel) {
                const original = copyLabel.textContent;
                copyLabel.textContent = copySuccessText;
                window.clearTimeout(copyResetTimer);
                copyResetTimer = window.setTimeout(function () {
                    copyLabel.textContent = original;
                }, 2000);
            }
        } catch (_error) {
            window.prompt("Copy this link:", url);
        }
    }

    async function nativeShare() {
        if (!activeShare || !navigator.share) {
            return;
        }

        try {
            await navigator.share({
                title: activeShare.title,
                text: activeShare.text || undefined,
                url: activeShare.url,
            });
            setSheetOpen(false);
        } catch (error) {
            if (error && error.name === "AbortError") {
                return;
            }
        }
    }

    if (nativeBtn && navigator.share) {
        nativeBtn.hidden = false;
    }

    shareButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            if (button.getAttribute("aria-disabled") === "true") {
                return;
            }

            openShareSheet(button);
        });
    });

    sheet.querySelectorAll("[data-business-share-close]").forEach(function (el) {
        el.addEventListener("click", function () {
            setSheetOpen(false);
        });
    });

    copyBtn?.addEventListener("click", copyShareLink);

    nativeBtn?.addEventListener("click", nativeShare);

    sheet.querySelectorAll("[data-share-action='whatsapp'], [data-share-action='x'], [data-share-action='telegram']").forEach(
        function (link) {
            link.addEventListener("click", function () {
                window.setTimeout(function () {
                    setSheetOpen(false);
                }, 120);
            });
        },
    );

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && !sheet.hidden) {
            setSheetOpen(false);
        }
    });
})();
