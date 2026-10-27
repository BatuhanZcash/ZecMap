(function () {
    const modal = document.getElementById("business-import-modal");
    const form = document.getElementById("business-import-form");
    const fileInput = document.getElementById("business-json-file");
    const submitButton = document.getElementById("business-import-submit");
    const errorBox = document.getElementById("business-import-error");
    const overlay = document.getElementById("business-import-overlay");
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

    if (!modal || !form) {
        return;
    }

    let isImporting = false;

    function openModal() {
        if (isImporting) {
            return;
        }

        hideError();
        modal.classList.add("is-open");
        modal.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function closeModal() {
        if (isImporting) {
            return;
        }

        modal.classList.remove("is-open");
        modal.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";

        if (fileInput) {
            fileInput.value = "";
        }

        hideError();
    }

    function showError(message) {
        if (!errorBox) {
            window.alert(message);
            return;
        }

        errorBox.textContent = message;
        errorBox.classList.add("is-visible");
    }

    function hideError() {
        if (!errorBox) {
            return;
        }

        errorBox.textContent = "";
        errorBox.classList.remove("is-visible");
    }

    function showOverlay() {
        if (!overlay) {
            return;
        }

        overlay.classList.add("is-visible");
        overlay.setAttribute("aria-hidden", "false");
    }

    function hideOverlay() {
        if (!overlay) {
            return;
        }

        overlay.classList.remove("is-visible");
        overlay.setAttribute("aria-hidden", "true");
    }

    function setImportingState(active) {
        isImporting = active;

        if (submitButton) {
            submitButton.disabled = active;
            submitButton.textContent = active ? "İçe aktarılıyor…" : "İçe Aktar";
        }

        modal.querySelectorAll("[data-close-import]").forEach(function (element) {
            element.disabled = active;
        });
    }

    function handleBeforeUnload(event) {
        if (!isImporting) {
            return;
        }

        event.preventDefault();
        event.returnValue = "";
    }

    function extractErrorMessage(payload, fallback) {
        if (!payload) {
            return fallback;
        }

        if (typeof payload.message === "string" && payload.message !== "") {
            return payload.message;
        }

        if (payload.errors) {
            const firstKey = Object.keys(payload.errors)[0];
            const firstError = payload.errors[firstKey];

            if (Array.isArray(firstError) && firstError[0]) {
                return firstError[0];
            }
        }

        return fallback;
    }

    async function submitImport(event) {
        event.preventDefault();

        if (isImporting) {
            return;
        }

        if (!fileInput?.files?.length) {
            showError("Lütfen bir JSON dosyası seçin.");
            return;
        }

        hideError();

        const selectedFile = fileInput.files[0];
        const formData = new FormData(form);
        formData.set("json_file", selectedFile, selectedFile.name);

        setImportingState(true);
        showOverlay();
        window.addEventListener("beforeunload", handleBeforeUnload);

        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {}),
                },
                credentials: "same-origin",
            });

            const payload = await response.json().catch(function () {
                return null;
            });

            if (!response.ok || !payload?.success) {
                throw new Error(
                    extractErrorMessage(payload, "İçe aktarma sırasında bir hata oluştu."),
                );
            }

            window.removeEventListener("beforeunload", handleBeforeUnload);
            window.location.href = payload.redirect || form.action;
        } catch (error) {
            window.removeEventListener("beforeunload", handleBeforeUnload);
            hideOverlay();
            setImportingState(false);
            showError(error.message || "İçe aktarma sırasında bir hata oluştu.");
        }
    }

    document.querySelectorAll("[data-open-import-business]").forEach(function (button) {
        button.addEventListener("click", openModal);
    });

    modal.querySelectorAll("[data-close-import]").forEach(function (element) {
        element.addEventListener("click", closeModal);
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && modal.classList.contains("is-open") && !isImporting) {
            closeModal();
        }
    });

    form.addEventListener("submit", submitImport);
})();
