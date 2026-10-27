(function () {
    const drawer = document.getElementById("business-edit-drawer");
    const form = document.getElementById("business-edit-form");
    const dataEl = document.getElementById("businesses-data");
    const updateUrlTemplate = document.getElementById("business-update-url-template");
    const storeUrlEl = document.getElementById("business-store-url");
    const methodInput = document.getElementById("business-form-method");

    if (
        !drawer ||
        !form ||
        !dataEl ||
        !updateUrlTemplate ||
        !storeUrlEl ||
        !methodInput
    ) {
        return;
    }

    const businesses = JSON.parse(dataEl.textContent || "[]");
    const businessMap = new Map(
        businesses.map((item) => [String(item.id), item]),
    );

    const titleEl = document.getElementById("business-edit-title");
    const logoPreview = document.getElementById("business-logo-preview");
    const logoPlaceholder = document.getElementById("business-logo-placeholder");
    const logoInput = document.getElementById("business-logo-input");
    const removeLogoInput = document.getElementById("business-remove-logo");
    const removeLogoBtn = document.getElementById("business-logo-remove-btn");

    let adminMap = null;
    let adminMarker = null;

    function field(id) {
        return document.getElementById(id);
    }

    function resetLogoPreview(logoUrl) {
        removeLogoInput.value = "0";
        logoInput.value = "";

        if (logoUrl) {
            logoPreview.src = logoUrl;
            logoPreview.style.display = "block";
            logoPlaceholder.style.display = "none";
            removeLogoBtn.style.display = "flex";
        } else {
            logoPreview.src = "";
            logoPreview.style.display = "none";
            logoPlaceholder.style.display = "flex";
            removeLogoBtn.style.display = "none";
        }
    }

    function resetFormFields() {
        field("field-status").value = "active";
        field("field-category_id").value = "";
        field("field-verified").checked = false;
        field("field-name").value = "";
        field("field-description").value = "";
        field("field-address").value = "";
        field("field-lat").value = "";
        field("field-lng").value = "";
        field("field-website").value = "";
        field("field-phone").value = "";
        field("field-zcash_address").value = "";
        field("field-social_twitter").value = "";
        field("field-payment_provider").value = "zcash";
        field("field-sort_order").value = "0";
        field("field-badge_key").value = "";
        field("field-badge_label").value = "";
        syncBadgeFields();
        resetLogoPreview(null);
    }

    function openDrawer() {
        drawer.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function openCreateDrawer() {
        form.action = storeUrlEl.dataset.url;
        methodInput.value = "POST";
        titleEl.textContent = "Yeni İşletme";
        resetFormFields();

        openDrawer();

        window.requestAnimationFrame(function () {
            initAdminMap(null, null);
            if (adminMap) {
                adminMap.resize();
            }
        });
    }

    function openEditDrawer(businessId) {
        const business = businessMap.get(String(businessId));
        if (!business) {
            return;
        }

        form.action = updateUrlTemplate.dataset.url.replace(
            "__ID__",
            business.id,
        );
        methodInput.value = "PUT";
        titleEl.textContent = "Düzenle: " + business.name;

        field("field-status").value = business.status || "active";
        field("field-category_id").value = business.category_id
            ? String(business.category_id)
            : "";
        field("field-verified").checked = Boolean(business.verified);
        field("field-name").value = business.name || "";
        field("field-description").value = business.description || "";
        field("field-address").value = business.address || "";
        field("field-lat").value = business.lat ?? "";
        field("field-lng").value = business.lng ?? "";
        field("field-website").value = business.website || "";
        field("field-phone").value = business.phone || "";
        field("field-zcash_address").value = business.zcash_address || "";
        field("field-social_twitter").value =
            business.social_links?.twitter || "";
        field("field-payment_provider").value =
            business.payment_provider || "zcash";
        field("field-sort_order").value = String(business.sort_order ?? 0);
        field("field-badge_key").value = business.badge_key || "";
        field("field-badge_label").value = business.badge_label || "";
        syncBadgeFields();
        resetLogoPreview(business.logo_url || null);

        openDrawer();

        window.requestAnimationFrame(function () {
            initAdminMap(business.lat, business.lng);
            if (adminMap) {
                adminMap.resize();
            }
        });
    }

    function closeDrawer() {
        drawer.classList.remove("is-open");
        drawer.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
        destroyAdminMap();
    }

    function destroyAdminMap() {
        if (adminMap) {
            adminMap.remove();
            adminMap = null;
            adminMarker = null;
        }
    }

    function setMarker(lng, lat) {
        if (!adminMap || typeof maplibregl === "undefined") {
            return;
        }

        if (adminMarker) {
            adminMarker.remove();
        }

        adminMarker = new maplibregl.Marker({ color: "#FFC107" })
            .setLngLat([lng, lat])
            .addTo(adminMap);
    }

    function initAdminMap(lat, lng) {
        const container = document.getElementById("admin-business-map");
        if (!container || typeof maplibregl === "undefined") {
            return;
        }

        destroyAdminMap();
        container.innerHTML = "";

        const hasCoords =
            lat != null && lng != null && lat !== "" && lng !== "";
        const centerLng = hasCoords ? Number(lng) : 10;
        const centerLat = hasCoords ? Number(lat) : 25;
        const zoom = hasCoords ? 12 : 2;

        adminMap = new maplibregl.Map({
            container: container,
            style: "https://tiles.openfreemap.org/styles/liberty",
            center: [centerLng, centerLat],
            zoom: zoom,
        });

        adminMap.addControl(new maplibregl.NavigationControl(), "top-right");

        adminMap.on("load", function () {
            if (hasCoords) {
                setMarker(centerLng, centerLat);
            }
        });

        adminMap.on("click", function (event) {
            const clickLng = event.lngLat.lng;
            const clickLat = event.lngLat.lat;
            field("field-lat").value = clickLat.toFixed(6);
            field("field-lng").value = clickLng.toFixed(6);
            setMarker(clickLng, clickLat);
        });
    }

    function syncMapFromInputs() {
        const lat = parseFloat(field("field-lat").value);
        const lng = parseFloat(field("field-lng").value);

        if (!adminMap || Number.isNaN(lat) || Number.isNaN(lng)) {
            return;
        }

        setMarker(lng, lat);
        adminMap.easeTo({
            center: [lng, lat],
            zoom: Math.max(adminMap.getZoom(), 10),
        });
    }

    document.querySelectorAll("[data-open-create-business]").forEach(function (button) {
        button.addEventListener("click", openCreateDrawer);
    });

    document.querySelectorAll(".btn-edit-business").forEach(function (button) {
        button.addEventListener("click", function () {
            openEditDrawer(button.dataset.id);
        });
    });

    drawer.querySelectorAll("[data-close-drawer]").forEach(function (element) {
        element.addEventListener("click", closeDrawer);
    });

    document.querySelectorAll("[data-delete-business]").forEach(function (deleteForm) {
        deleteForm.addEventListener("submit", function (event) {
            if (
                !window.confirm(
                    "Bu işletmeyi silmek istediğinize emin misiniz?",
                )
            ) {
                event.preventDefault();
            }
        });
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && drawer.classList.contains("is-open")) {
            closeDrawer();
        }
    });

    logoInput.addEventListener("change", function () {
        const file = logoInput.files?.[0];
        if (!file) {
            return;
        }

        const reader = new FileReader();
        reader.onload = function () {
            logoPreview.src = reader.result;
            logoPreview.style.display = "block";
            logoPlaceholder.style.display = "none";
            removeLogoBtn.style.display = "flex";
            removeLogoInput.value = "0";
        };
        reader.readAsDataURL(file);
    });

    removeLogoBtn.addEventListener("click", function () {
        logoInput.value = "";
        logoPreview.src = "";
        logoPreview.style.display = "none";
        logoPlaceholder.style.display = "flex";
        removeLogoBtn.style.display = "none";
        removeLogoInput.value = "1";
    });

    field("field-lat").addEventListener("change", syncMapFromInputs);
    field("field-lng").addEventListener("change", syncMapFromInputs);

    function syncBadgeFields() {
        const badgeKey = field("field-badge_key")?.value || "";
        const labelWrap = document.getElementById("field-badge-label-wrap");
        const labelInput = field("field-badge_label");

        if (!labelWrap) {
            return;
        }

        const isCustom = badgeKey === "custom";
        labelWrap.style.display = isCustom ? "block" : "none";

        if (labelInput) {
            labelInput.required = isCustom;
            if (!isCustom) {
                labelInput.value = "";
            }
        }
    }

    field("field-badge_key")?.addEventListener("change", syncBadgeFields);
    syncBadgeFields();
})();
