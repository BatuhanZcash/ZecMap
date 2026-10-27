(function () {
    const drawer = document.getElementById("site-edit-drawer");
    const form = document.getElementById("site-edit-form");
    const dataEl = document.getElementById("accept-zec-sites-data");
    const updateUrlTemplate = document.getElementById("site-update-url-template");
    const storeUrlEl = document.getElementById("site-store-url");
    const methodInput = document.getElementById("site-form-method");
    const titleEl = document.getElementById("site-edit-title");

    if (!drawer || !form || !dataEl || !updateUrlTemplate || !storeUrlEl || !methodInput || !titleEl) {
        return;
    }

    const sites = JSON.parse(dataEl.textContent || "[]");
    const siteMap = new Map(sites.map((item) => [String(item.id), item]));

    const logoPreview = document.getElementById("site-logo-preview");
    const logoPlaceholder = document.getElementById("site-logo-placeholder");
    const logoInput = document.getElementById("site-logo-input");
    const removeLogoInput = document.getElementById("site-remove-logo");

    function field(id) {
        return document.getElementById(id);
    }

    function resetLogoPreview(logoUrl) {
        removeLogoInput.checked = false;
        logoInput.value = "";

        if (logoUrl) {
            logoPreview.src = logoUrl;
            logoPreview.hidden = false;
            logoPlaceholder.hidden = true;
        } else {
            logoPreview.hidden = true;
            logoPreview.removeAttribute("src");
            logoPlaceholder.hidden = false;
        }
    }

    function openCreateDrawer() {
        form.action = storeUrlEl.dataset.url;
        methodInput.value = "POST";
        titleEl.textContent = "Yeni Site";

        field("field-name").value = "";
        field("field-description").value = "";
        field("field-website_url").value = "";
        field("field-sort_order").value = "0";
        field("field-status").value = "active";
        resetLogoPreview(null);

        drawer.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function openEditDrawer(siteId) {
        const site = siteMap.get(String(siteId));
        if (!site) {
            return;
        }

        form.action = updateUrlTemplate.dataset.url.replace("__ID__", site.id);
        methodInput.value = "PUT";
        titleEl.textContent = "Düzenle: " + site.name;

        field("field-name").value = site.name || "";
        field("field-description").value = site.description || "";
        field("field-website_url").value = site.website_url || "";
        field("field-sort_order").value = String(site.sort_order ?? 0);
        field("field-status").value = site.status || "active";
        resetLogoPreview(site.logo_url || null);

        drawer.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function closeDrawer() {
        drawer.classList.remove("is-open");
        drawer.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
    }

    document.querySelectorAll("[data-open-create]").forEach((button) => {
        button.addEventListener("click", openCreateDrawer);
    });

    document.querySelectorAll("[data-edit-site]").forEach((button) => {
        button.addEventListener("click", function () {
            openEditDrawer(button.dataset.id);
        });
    });

    document.querySelectorAll("[data-close-drawer]").forEach((button) => {
        button.addEventListener("click", closeDrawer);
    });

    document.querySelectorAll("[data-delete-site]").forEach((deleteForm) => {
        deleteForm.addEventListener("submit", function (event) {
            if (!window.confirm("Bu siteyi silmek istediğinize emin misiniz?")) {
                event.preventDefault();
            }
        });
    });

    logoInput.addEventListener("change", function () {
        const file = logoInput.files && logoInput.files[0];
        if (!file) {
            return;
        }

        const reader = new FileReader();
        reader.onload = function () {
            logoPreview.src = reader.result;
            logoPreview.hidden = false;
            logoPlaceholder.hidden = true;
            removeLogoInput.checked = false;
        };
        reader.readAsDataURL(file);
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && drawer.classList.contains("is-open")) {
            closeDrawer();
        }
    });
})();
