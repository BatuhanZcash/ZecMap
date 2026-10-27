(function () {
    const drawer = document.getElementById("supporter-edit-drawer");
    const form = document.getElementById("supporter-edit-form");
    const dataEl = document.getElementById("supporters-data");
    const updateUrlTemplate = document.getElementById("supporter-update-url-template");
    const storeUrlEl = document.getElementById("supporter-store-url");
    const methodInput = document.getElementById("supporter-form-method");
    const titleEl = document.getElementById("supporter-edit-title");

    if (!drawer || !form || !dataEl || !updateUrlTemplate || !storeUrlEl || !methodInput || !titleEl) {
        return;
    }

    const supporters = JSON.parse(dataEl.textContent || "[]");
    const supporterMap = new Map(supporters.map((item) => [String(item.id), item]));

    const iconPreview = document.getElementById("supporter-icon-preview");
    const iconPlaceholder = document.getElementById("supporter-icon-placeholder");
    const iconInput = document.getElementById("supporter-icon-input");
    const removeIconInput = document.getElementById("supporter-remove-icon");

    function field(id) {
        return document.getElementById(id);
    }

    function resetIconPreview(iconUrl) {
        removeIconInput.checked = false;
        iconInput.value = "";

        if (iconUrl) {
            iconPreview.src = iconUrl;
            iconPreview.hidden = false;
            iconPlaceholder.hidden = true;
        } else {
            iconPreview.hidden = true;
            iconPreview.removeAttribute("src");
            iconPlaceholder.hidden = false;
        }
    }

    function openCreateDrawer() {
        form.action = storeUrlEl.dataset.url;
        methodInput.value = "POST";
        titleEl.textContent = "Yeni Destekleyen";

        field("field-name").value = "";
        field("field-description").value = "";
        field("field-link_label").value = "";
        field("field-link_url").value = "";
        field("field-sort_order").value = "0";
        field("field-status").value = "active";
        resetIconPreview(null);

        drawer.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function openEditDrawer(supporterId) {
        const supporter = supporterMap.get(String(supporterId));
        if (!supporter) {
            return;
        }

        form.action = updateUrlTemplate.dataset.url.replace("__ID__", supporter.id);
        methodInput.value = "PUT";
        titleEl.textContent = "Düzenle: " + supporter.name;

        field("field-name").value = supporter.name || "";
        field("field-description").value = supporter.description || "";
        field("field-link_label").value = supporter.link_label || "";
        field("field-link_url").value = supporter.link_url || "";
        field("field-sort_order").value = String(supporter.sort_order ?? 0);
        field("field-status").value = supporter.status || "active";
        resetIconPreview(supporter.icon_url || null);

        drawer.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function closeDrawer() {
        drawer.classList.remove("is-open");
        drawer.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
    }

    document.querySelectorAll("[data-open-create-supporter]").forEach((button) => {
        button.addEventListener("click", openCreateDrawer);
    });

    document.querySelectorAll("[data-edit-supporter]").forEach((button) => {
        button.addEventListener("click", function () {
            openEditDrawer(button.dataset.id);
        });
    });

    document.querySelectorAll("[data-close-drawer]").forEach((button) => {
        button.addEventListener("click", closeDrawer);
    });

    document.querySelectorAll("[data-delete-supporter]").forEach((deleteForm) => {
        deleteForm.addEventListener("submit", function (event) {
            if (!window.confirm("Bu destekleyeni silmek istediğinize emin misiniz?")) {
                event.preventDefault();
            }
        });
    });

    iconInput.addEventListener("change", function () {
        const file = iconInput.files && iconInput.files[0];
        if (!file) {
            return;
        }

        const reader = new FileReader();
        reader.onload = function () {
            iconPreview.src = reader.result;
            iconPreview.hidden = false;
            iconPlaceholder.hidden = true;
            removeIconInput.checked = false;
        };
        reader.readAsDataURL(file);
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && drawer.classList.contains("is-open")) {
            closeDrawer();
        }
    });
})();
