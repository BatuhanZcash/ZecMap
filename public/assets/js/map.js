(function () {
    const mapContainer = document.getElementById("zecmap-map");

    if (
        !mapContainer ||
        typeof maplibregl === "undefined" ||
        typeof Supercluster === "undefined"
    ) {
        return;
    }

    const config = window.ZECMAP_MAP || {};
    const businessUrlTemplate = config.businessUrlTemplate || "";
    let pins = (config.pins || []).filter(function (pin) {
        return (
            pin.lat != null &&
            pin.lng != null &&
            !Number.isNaN(Number(pin.lat)) &&
            !Number.isNaN(Number(pin.lng))
        );
    });

    const businessById = new Map(
        pins.map(function (pin) {
            return [String(pin.id), pin];
        }),
    );
    const businessFetchPromises = new Map();

    const pinColors = config.pinColors || {
        zcash: "#FFC109",
        flexa: "#9333EA",
    };
    const pinUrlCache = {};

    function getPinColor(paymentProvider) {
        return paymentProvider === "flexa" ? pinColors.flexa : pinColors.zcash;
    }

    function createPinSvgDataUrl(color) {
        const svg =
            '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="36" viewBox="0 0 28 36">' +
            '<filter id="pin-shadow" x="-30%" y="-10%" width="160%" height="140%">' +
            '<feDropShadow dx="0" dy="2" stdDeviation="2" flood-opacity="0.4" />' +
            "</filter>" +
            '<path filter="url(#pin-shadow)" d="M14 0C7.4 0 2 5.4 2 12c0 7.8 12 24 12 24S26 19.8 26 12C26 5.4 20.6 0 14 0z" fill="' +
            color +
            '" />' +
            '<circle cx="14" cy="12" r="5" fill="rgba(0,0,0,0.3)" />' +
            "</svg>";

        return (
            "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(svg)
        );
    }

    function getPinUrlForBusiness(business) {
        const color = getPinColor(business?.payment_provider);

        if (!pinUrlCache[color]) {
            pinUrlCache[color] = createPinSvgDataUrl(color);
        }

        return pinUrlCache[color];
    }

    const styleUrlLight =
        config.styleUrlLight ||
        "https://basemaps.cartocdn.com/gl/positron-gl-style/style.json";
    const styleUrlDark =
        config.styleUrlDark ||
        "https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json";
    const superclusterOptions = { radius: 60, maxZoom: 18 };

    const sidePanel = document.getElementById("map-side-panel");
    const sidePanelClose = document.getElementById("map-side-panel-close");
    const mapSearch = document.querySelector(".map-search");

    let clusterIndex = null;
    let markers = [];
    let initialBoundsFit = false;
    let markersScheduled = false;
    let markerInteractionUntil = 0;
    let moveEndTimer = null;
    const focusBusinessId = config.focusBusinessId
        ? String(config.focusBusinessId)
        : null;

    /** iOS/Android: click alone is unreliable on MapLibre HTML markers — use tap detection. */
    function bindMarkerActivate(element, onActivate) {
        let touchStart = null;
        let suppressClick = false;
        const MOVE_THRESHOLD = 14;

        function activate(event) {
            markerInteractionUntil = Date.now() + 500;
            event.preventDefault();
            event.stopPropagation();
            onActivate();
        }

        element.addEventListener(
            "touchstart",
            function (event) {
                if (event.touches.length === 1) {
                    touchStart = {
                        x: event.touches[0].clientX,
                        y: event.touches[0].clientY,
                    };
                    markerInteractionUntil = Date.now() + 500;
                } else {
                    touchStart = null;
                }
            },
            { passive: true },
        );

        element.addEventListener(
            "touchend",
            function (event) {
                if (!touchStart) {
                    return;
                }

                const touch = event.changedTouches[0];
                if (!touch) {
                    touchStart = null;
                    return;
                }

                const dx = Math.abs(touch.clientX - touchStart.x);
                const dy = Math.abs(touch.clientY - touchStart.y);
                touchStart = null;

                if (dx <= MOVE_THRESHOLD && dy <= MOVE_THRESHOLD) {
                    suppressClick = true;
                    activate(event);
                    window.setTimeout(function () {
                        suppressClick = false;
                    }, 450);
                }
            },
            { passive: false },
        );

        element.addEventListener("click", function (event) {
            if (suppressClick) {
                event.preventDefault();
                event.stopPropagation();
                return;
            }

            activate(event);
        });
    }

    function isDarkTheme() {
        return document.documentElement.classList.contains("dark");
    }

    function getStyleUrl() {
        return isDarkTheme() ? styleUrlDark : styleUrlLight;
    }

    function pinToPoint(pin) {
        return {
            type: "Feature",
            properties: {
                cluster: false,
                biz: pin,
            },
            geometry: {
                type: "Point",
                coordinates: [pin.lng, pin.lat],
            },
        };
    }

    function rebuildClusterIndex() {
        clusterIndex = new Supercluster(superclusterOptions);
        clusterIndex.load(pins.map(pinToPoint));
    }

    function clearMarkers() {
        markers.forEach(function (marker) {
            marker.remove();
        });
        markers = [];
    }

    function clusterMarkerSize(pointCount) {
        return Math.min(52, 20 + pointCount * 2);
    }

    function handleClusterClick(clusterId, lng, lat) {
        if (!clusterIndex) {
            return;
        }

        const expansionZoom = Math.min(
            clusterIndex.getClusterExpansionZoom(clusterId),
            20,
        );

        map.easeTo({
            center: [lng, lat],
            zoom: expansionZoom,
            duration: 550,
        });
    }

    function updateMarkers() {
        if (!clusterIndex) {
            return;
        }

        clearMarkers();

        let bounds;
        try {
            bounds = map.getBounds();
        } catch (error) {
            return;
        }

        const bbox = [
            bounds.getWest(),
            bounds.getSouth(),
            bounds.getEast(),
            bounds.getNorth(),
        ];
        const zoom = Math.round(map.getZoom());
        const clusters = clusterIndex.getClusters(bbox, zoom);

        clusters.forEach(function (feature) {
            const coords = feature.geometry.coordinates;
            const lng = coords[0];
            const lat = coords[1];
            const props = feature.properties;

            if (props.cluster) {
                const el = document.createElement("button");
                const size = clusterMarkerSize(props.point_count);

                el.type = "button";
                el.className = "map-cluster-marker";
                el.style.width = size + "px";
                el.style.height = size + "px";
                el.textContent = String(props.point_count);
                el.setAttribute(
                    "aria-label",
                    props.point_count + " businesses",
                );

                bindMarkerActivate(el, function () {
                    handleClusterClick(props.cluster_id, lng, lat);
                });

                markers.push(
                    new maplibregl.Marker({ element: el, anchor: "center" })
                        .setLngLat([lng, lat])
                        .addTo(map),
                );
                return;
            }

            const biz = props.biz;
            if (!biz) {
                return;
            }

            const el = document.createElement("button");
            el.type = "button";
            el.className = "map-pin-marker-button";
            el.setAttribute("aria-label", biz.name);

            const img = document.createElement("img");
            img.className = "map-pin-marker";
            img.src = getPinUrlForBusiness(biz);
            img.alt = "";
            el.appendChild(img);

            bindMarkerActivate(el, function () {
                openSidePanel(biz.id);
            });

            markers.push(
                new maplibregl.Marker({ element: el, anchor: "bottom" })
                    .setLngLat([lng, lat])
                    .addTo(map),
            );
        });
    }

    function scheduleMarkerUpdate() {
        if (Date.now() < markerInteractionUntil) {
            window.setTimeout(scheduleMarkerUpdate, 120);
            return;
        }

        if (markersScheduled) {
            return;
        }

        markersScheduled = true;
        requestAnimationFrame(function () {
            markersScheduled = false;
            if (Date.now() < markerInteractionUntil) {
                return;
            }
            updateMarkers();
        });
    }

    function scheduleMarkerUpdateDebounced(delay) {
        if (moveEndTimer) {
            clearTimeout(moveEndTimer);
        }

        moveEndTimer = window.setTimeout(scheduleMarkerUpdate, delay);
    }

    const map = new maplibregl.Map({
        container: mapContainer,
        style: getStyleUrl(),
        center: config.center || [10, 25],
        zoom: config.zoom || 1.8,
        minZoom: 1,
        maxZoom: 20,
        attributionControl: false,
        fadeDuration: 0,
    });

    map.addControl(
        new maplibregl.AttributionControl({ compact: true }),
        "bottom-right",
    );
    map.addControl(
        new maplibregl.NavigationControl({ showCompass: false }),
        "bottom-right",
    );

    function fitMapToPins() {
        if (pins.length === 0) {
            return;
        }

        if (pins.length === 1) {
            map.easeTo({
                center: [pins[0].lng, pins[0].lat],
                zoom: 12,
                duration: 0,
            });
            return;
        }

        const bounds = new maplibregl.LngLatBounds();
        pins.forEach(function (pin) {
            bounds.extend([pin.lng, pin.lat]);
        });

        map.fitBounds(bounds, {
            padding: 72,
            maxZoom: 3,
            duration: 0,
        });
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }

    function contactItem(type, value, iconPath, isLink) {
        const valueHtml = isLink
            ? '<a class="map-side-panel-contact-link" href="' +
              escapeHtml(value) +
              '" target="_blank" rel="noopener noreferrer">' +
              escapeHtml(value) +
              "</a>"
            : '<p class="map-side-panel-contact-value">' +
              escapeHtml(value) +
              "</p>";

        return (
            '<li class="map-side-panel-contact-item">' +
            '<span class="map-side-panel-contact-meta">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
            iconPath +
            "</svg>" +
            '<span class="map-side-panel-contact-type">' +
            escapeHtml(type) +
            "</span>" +
            "</span>" +
            valueHtml +
            "</li>"
        );
    }

    function registerBusiness(business) {
        businessById.set(String(business.id), business);
    }

    function businessDetailUrl(businessId) {
        return businessUrlTemplate.replace("__ID__", String(businessId));
    }

    function hasBusinessDetails(business) {
        return Boolean(
            business &&
                (business.detail_url ||
                    business.description ||
                    business.address ||
                    business.phone ||
                    business.website ||
                    business.logo_url),
        );
    }

    function fetchBusinessDetails(businessId) {
        const key = String(businessId);
        const cached = businessById.get(key);

        if (cached && hasBusinessDetails(cached)) {
            return Promise.resolve(cached);
        }

        if (businessFetchPromises.has(key)) {
            return businessFetchPromises.get(key);
        }

        const promise = fetch(businessDetailUrl(businessId), {
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error("Business fetch failed");
                }
                return response.json();
            })
            .then(function (data) {
                const business = data.business;
                if (!business) {
                    throw new Error("Business missing");
                }

                registerBusiness(business);
                return business;
            })
            .finally(function () {
                businessFetchPromises.delete(key);
            });

        businessFetchPromises.set(key, promise);
        return promise;
    }

    function setSidePanelLoading(isLoading) {
        const loadingEl = document.getElementById("map-side-panel-loading");
        const descriptionEl = document.getElementById(
            "map-side-panel-description",
        );
        const contactsSection = document.getElementById(
            "map-side-panel-contacts",
        );
        const directionsEl = document.getElementById(
            "map-side-panel-directions",
        );
        const detailsEl = document.getElementById("map-side-panel-details");
        const card = sidePanel
            ? sidePanel.querySelector(".map-side-panel-card")
            : null;

        if (loadingEl) {
            loadingEl.hidden = !isLoading;
        }

        if (card) {
            card.classList.toggle("map-side-panel-card--loading", isLoading);
        }

        if (isLoading) {
            if (descriptionEl) {
                descriptionEl.hidden = true;
            }
            if (contactsSection) {
                contactsSection.hidden = true;
            }
            if (directionsEl) {
                directionsEl.hidden = true;
            }
            if (detailsEl) {
                detailsEl.hidden = true;
            }
        }
    }

    function businessDetailUrl(business) {
        if (business.detail_url) {
            return business.detail_url;
        }

        if (business.slug) {
            return (
                window.location.origin +
                "/business/" +
                encodeURIComponent(business.slug)
            );
        }

        return window.location.href;
    }

    function truncateShareText(text, maxLength) {
        if (!text) {
            return "";
        }

        const normalized = String(text).trim();
        if (normalized.length <= maxLength) {
            return normalized;
        }

        return normalized.slice(0, maxLength - 1) + "…";
    }

    function updateShareButton(business) {
        const shareBtn = document.getElementById("map-side-panel-share");
        if (!shareBtn) {
            return;
        }

        if (!business || !business.name) {
            shareBtn.setAttribute("aria-disabled", "true");
            return;
        }

        shareBtn.setAttribute("aria-disabled", "false");
        shareBtn.setAttribute("data-share-url", businessDetailUrl(business));
        shareBtn.setAttribute("data-share-title", business.name);
        shareBtn.setAttribute(
            "data-share-text",
            truncateShareText(business.description, 120),
        );
    }

    function updateSaveButton(business) {
        const saveBtn = document.getElementById("map-side-panel-save");
        if (!saveBtn) {
            return;
        }

        const slug = business?.slug;
        if (!slug) {
            saveBtn.setAttribute("aria-disabled", "true");
            saveBtn.setAttribute("aria-pressed", "false");
            saveBtn.classList.remove("map-side-panel-quick-action--active");
            saveBtn.removeAttribute("data-save-slug");
            return;
        }

        saveBtn.setAttribute("aria-disabled", "false");
        saveBtn.setAttribute("data-save-slug", slug);

        const favorites = window.ZecMapFavorites;
        const saved = favorites && favorites.isSaved(slug);
        saveBtn.setAttribute("aria-pressed", saved ? "true" : "false");
        saveBtn.classList.toggle("map-side-panel-quick-action--active", saved);
    }

    function bindSaveButton() {
        const saveBtn = document.getElementById("map-side-panel-save");
        if (!saveBtn || saveBtn.dataset.saveBound === "true") {
            return;
        }

        saveBtn.dataset.saveBound = "true";
        saveBtn.addEventListener("click", function () {
            if (saveBtn.getAttribute("aria-disabled") === "true") {
                return;
            }

            const slug = saveBtn.getAttribute("data-save-slug");
            const favorites = window.ZecMapFavorites;
            if (!slug || !favorites) {
                return;
            }

            saveBtn.disabled = true;
            favorites
                .toggle(slug)
                .then(function () {
                    updateSaveButton({ slug: slug });
                })
                .catch(function () {
                    // toast handled in favorites module
                })
                .finally(function () {
                    saveBtn.disabled = false;
                });
        });

        const favorites = window.ZecMapFavorites;
        if (favorites && typeof favorites.onChange === "function") {
            favorites.onChange(function () {
                const slug = saveBtn.getAttribute("data-save-slug");
                if (slug) {
                    updateSaveButton({ slug: slug });
                }
            });
        }
    }

    function setSidePanelLogo(logoUrl, businessName) {
        const imageEl = document.getElementById("map-side-panel-image");
        const placeholderEl = document.getElementById(
            "map-side-panel-placeholder",
        );

        if (!imageEl || !placeholderEl) {
            return;
        }

        if (!logoUrl) {
            imageEl.hidden = true;
            imageEl.removeAttribute("src");
            placeholderEl.hidden = false;
            return;
        }

        imageEl.onerror = function () {
            imageEl.hidden = true;
            imageEl.removeAttribute("src");
            placeholderEl.hidden = false;
        };

        imageEl.onload = function () {
            imageEl.hidden = false;
            placeholderEl.hidden = true;
        };

        imageEl.alt = businessName || "";
        imageEl.src = logoUrl;

        if (imageEl.complete && imageEl.naturalWidth > 0) {
            imageEl.hidden = false;
            placeholderEl.hidden = true;
        }
    }

    function renderSidePanel(business) {
        const categoryEl = document.getElementById("map-side-panel-category");
        const titleEl = document.getElementById("map-side-panel-title");
        const descriptionEl = document.getElementById(
            "map-side-panel-description",
        );
        const contactsSection = document.getElementById(
            "map-side-panel-contacts",
        );
        const contactsList = document.getElementById(
            "map-side-panel-contacts-list",
        );
        const directionsEl = document.getElementById(
            "map-side-panel-directions",
        );
        const detailsEl = document.getElementById("map-side-panel-details");

        setSidePanelLoading(false);

        setSidePanelLogo(business.logo_url, business.name);

        categoryEl.textContent = business.category || "Business";
        categoryEl.hidden = !business.category;
        titleEl.textContent = business.name;

        if (business.description) {
            descriptionEl.textContent = business.description;
            descriptionEl.hidden = false;
        } else {
            descriptionEl.textContent = "";
            descriptionEl.hidden = true;
        }

        const contacts = [];

        if (business.address) {
            contacts.push(
                contactItem(
                    "Address",
                    business.address,
                    '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle>',
                    false,
                ),
            );
        } else if (business.city) {
            contacts.push(
                contactItem(
                    "City",
                    business.city,
                    '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle>',
                    false,
                ),
            );
        }

        if (business.phone) {
            contacts.push(
                contactItem(
                    "Phone",
                    business.phone,
                    '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path>',
                    false,
                ),
            );
        }

        if (business.website) {
            const websiteUrl = business.website.startsWith("http")
                ? business.website
                : "https://" + business.website;
            contacts.push(
                contactItem(
                    "Website",
                    websiteUrl,
                    '<circle cx="12" cy="12" r="10"></circle><path d="M2 12h20"></path><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>',
                    true,
                ),
            );
        }

        if (contacts.length && contactsList && contactsSection) {
            contactsList.innerHTML = contacts.join("");
            contactsSection.hidden = false;
        } else if (contactsSection) {
            contactsList.innerHTML = "";
            contactsSection.hidden = true;
        }

        if (directionsEl) {
            if (business.lat != null && business.lng != null) {
                directionsEl.href =
                    "https://www.google.com/maps/dir/?api=1&destination=" +
                    encodeURIComponent(business.lat + "," + business.lng);
                directionsEl.hidden = false;
            } else {
                directionsEl.hidden = true;
            }
        }

        if (detailsEl) {
            const detailUrl =
                business.detail_url ||
                (business.slug
                    ? "/business/" + encodeURIComponent(business.slug)
                    : "");

            if (detailUrl) {
                detailsEl.href = detailUrl;
                detailsEl.hidden = false;
            } else {
                detailsEl.hidden = true;
            }
        }

        updateShareButton(business);
        updateSaveButton(business);
    }

    let activeSidePanelRequestId = 0;

    function openSidePanel(businessId) {
        const pin = businessById.get(String(businessId));
        if (!pin || !sidePanel) {
            return;
        }

        const requestId = ++activeSidePanelRequestId;
        const titleEl = document.getElementById("map-side-panel-title");
        const categoryEl = document.getElementById("map-side-panel-category");

        sidePanel.setAttribute("aria-label", pin.name);
        sidePanel.setAttribute("aria-hidden", "false");
        sidePanel.classList.add("map-side-panel--open");

        if (mapSearch) {
            mapSearch.classList.add("map-search--panel-open");
        }

        titleEl.textContent = pin.name;
        categoryEl.textContent = "Business";
        categoryEl.hidden = false;
        setSidePanelLogo(pin.logo_url, pin.name);
        setSidePanelLoading(true);
        updateShareButton({
            name: pin.name,
            slug: pin.slug,
            description: pin.description || "",
        });
        updateSaveButton(pin);

        if (pin.lat != null && pin.lng != null) {
            map.easeTo({
                center: [pin.lng, pin.lat],
                zoom: Math.max(map.getZoom(), 12),
                duration: 450,
            });
        }

        fetchBusinessDetails(businessId)
            .then(function (business) {
                if (requestId !== activeSidePanelRequestId) {
                    return;
                }

                renderSidePanel(business);
            })
            .catch(function () {
                if (requestId !== activeSidePanelRequestId) {
                    return;
                }

                setSidePanelLoading(false);
                renderSidePanel(pin);
            });
    }

    function closeSidePanel() {
        if (!sidePanel) {
            return;
        }

        sidePanel.classList.remove("map-side-panel--open");
        sidePanel.setAttribute("aria-hidden", "true");

        if (mapSearch) {
            mapSearch.classList.remove("map-search--panel-open");
        }
    }

    function onMapReady() {
        if (!clusterIndex) {
            rebuildClusterIndex();
        }

        if (!initialBoundsFit) {
            if (focusBusinessId && businessById.has(focusBusinessId)) {
                openSidePanel(focusBusinessId);
            } else {
                fitMapToPins();
            }

            initialBoundsFit = true;
        }

        scheduleMarkerUpdate();
    }

    rebuildClusterIndex();

    map.on("load", onMapReady);
    map.on("moveend", function () {
        scheduleMarkerUpdateDebounced(200);
    });
    map.on("zoomend", function () {
        scheduleMarkerUpdateDebounced(80);
    });

    scheduleMarkerUpdate();
    window.setTimeout(scheduleMarkerUpdate, 400);
    window.setTimeout(scheduleMarkerUpdate, 1500);

    if (map.loaded()) {
        onMapReady();
    }

    function applyThemeStyle() {
        clearMarkers();
        map.setStyle(getStyleUrl());
        map.once("load", function () {
            rebuildClusterIndex();
            scheduleMarkerUpdate();
        });
    }

    const themeObserver = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (
                mutation.type === "attributes" &&
                mutation.attributeName === "class"
            ) {
                applyThemeStyle();
            }
        });
    });

    themeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"],
    });

    if (sidePanelClose) {
        sidePanelClose.addEventListener("click", closeSidePanel);
    }

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeSidePanel();
            hideSearchResults();
        }
    });

    const searchInput = document.getElementById("map-search-input");
    const searchClear = document.getElementById("map-search-clear");
    const searchDropdown = document.getElementById("map-search-dropdown");
    const searchEmpty = document.getElementById("map-search-empty");
    const searchUrl = config.searchUrl || "/map/search";
    let searchTimer = null;
    let searchRequestId = 0;

    const pinIconSvg =
        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin map-search-option-icon" aria-hidden="true">' +
        '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>' +
        '<circle cx="12" cy="10" r="3"></circle></svg>';

    function hideSearchResults() {
        if (searchDropdown) {
            searchDropdown.hidden = true;
            searchDropdown.innerHTML = "";
        }
        if (searchEmpty) {
            searchEmpty.hidden = true;
        }
    }

    function updateClearButton() {
        if (!searchClear || !searchInput) {
            return;
        }

        searchClear.hidden = searchInput.value.trim() === "";
    }

    function renderSearchResults(items) {
        if (!searchDropdown || !searchEmpty) {
            return;
        }

        if (items.length === 0) {
            searchDropdown.hidden = true;
            searchDropdown.innerHTML = "";
            searchEmpty.hidden = false;
            return;
        }

        searchEmpty.hidden = true;
        searchDropdown.hidden = false;
        searchDropdown.innerHTML = items
            .map(function (business) {
                const subtitle =
                    business.address ||
                    business.city ||
                    business.category ||
                    "";
                const subtitleHtml = subtitle
                    ? '<p class="map-search-option-address">' +
                      escapeHtml(subtitle) +
                      "</p>"
                    : "";

                return (
                    '<button type="button" class="map-search-option" data-business-id="' +
                    escapeHtml(business.id) +
                    '">' +
                    pinIconSvg +
                    '<div class="map-search-option-content">' +
                    '<p class="map-search-option-name">' +
                    escapeHtml(business.name) +
                    "</p>" +
                    subtitleHtml +
                    "</div></button>"
                );
            })
            .join("");

        searchDropdown
            .querySelectorAll(".map-search-option")
            .forEach(function (button) {
                button.addEventListener("click", function () {
                    const businessId = button.dataset.businessId;
                    const business = items.find(function (item) {
                        return String(item.id) === String(businessId);
                    });

                    if (business) {
                        registerBusiness(business);
                        openSidePanel(business.id);
                        hideSearchResults();
                    }
                });
            });
    }

    function performSearch(query) {
        const requestId = ++searchRequestId;

        fetch(searchUrl + "?q=" + encodeURIComponent(query), {
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error("Search failed");
                }
                return response.json();
            })
            .then(function (data) {
                if (requestId !== searchRequestId) {
                    return;
                }

                const items = data.businesses || [];
                items.forEach(registerBusiness);
                renderSearchResults(items);
            })
            .catch(function () {
                if (requestId !== searchRequestId) {
                    return;
                }

                hideSearchResults();
            });
    }

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const query = searchInput.value.trim();
            updateClearButton();

            if (searchTimer) {
                clearTimeout(searchTimer);
            }

            if (query.length < 2) {
                searchRequestId++;
                hideSearchResults();
                return;
            }

            searchTimer = setTimeout(function () {
                performSearch(query);
            }, 280);
        });

        searchInput.addEventListener("focus", function () {
            const query = searchInput.value.trim();
            if (query.length >= 2) {
                performSearch(query);
            }
        });
    }

    if (searchClear && searchInput) {
        searchClear.addEventListener("click", function () {
            searchInput.value = "";
            updateClearButton();
            hideSearchResults();
            searchInput.focus();
        });
    }

    document.addEventListener("click", function (event) {
        const searchInner = document.querySelector(".map-search-inner");
        if (!searchInner || searchInner.contains(event.target)) {
            return;
        }

        hideSearchResults();
    });

    updateClearButton();

    bindSaveButton();

    const favoritesConfig = window.ZECMAP_FAVORITES || {};
    if (window.ZecMapFavorites) {
        window.ZecMapFavorites.init(favoritesConfig).then(function () {
            bindSaveButton();
            const saveBtn = document.getElementById("map-side-panel-save");
            const slug = saveBtn?.getAttribute("data-save-slug");
            if (slug) {
                updateSaveButton({ slug: slug });
            }
        });
    }
})();
