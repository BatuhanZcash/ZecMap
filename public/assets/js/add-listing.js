(function () {
    const latInput = document.getElementById("ab-lat");
    const lngInput = document.getElementById("ab-lng");
    const locationStatus = document.getElementById("ab-location-status");
    const useLocationBtn = document.getElementById("ab-use-location");
    const paymentSelect = document.getElementById("ab-payment-provider");
    const form = document.querySelector(".add-business-form");
    const mapContainer = document.getElementById("add-business-map");

    const styleUrlLight =
        "https://basemaps.cartocdn.com/gl/positron-gl-style/style.json";
    const styleUrlDark =
        "https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json";
    const pinColors = {
        zcash: "#FFC109",
        flexa: "#9333EA",
    };

    let map = null;
    let marker = null;
    let selectedLngLat = null;
    const pinUrlCache = {};

    function isDarkTheme() {
        return document.documentElement.classList.contains("dark");
    }

    function getStyleUrl() {
        return isDarkTheme() ? styleUrlDark : styleUrlLight;
    }

    function getPinColor() {
        return paymentSelect?.value === "flexa"
            ? pinColors.flexa
            : pinColors.zcash;
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

    function getPinUrl() {
        const color = getPinColor();

        if (!pinUrlCache[color]) {
            pinUrlCache[color] = createPinSvgDataUrl(color);
        }

        return pinUrlCache[color];
    }

    function updateLocationStatus(lat, lng) {
        if (!locationStatus) {
            return;
        }

        locationStatus.textContent = lat.toFixed(4) + ", " + lng.toFixed(4);
        locationStatus.classList.remove("add-business-location-empty");
        locationStatus.classList.add("add-business-location-coords");
    }

    function updateMarker(lng, lat) {
        if (!map) {
            return;
        }

        if (marker) {
            marker.remove();
        }

        const button = document.createElement("button");
        button.type = "button";
        button.className = "add-business-map-pin-button";
        button.setAttribute("aria-label", "Selected location");
        button.style.padding = "0";
        button.style.border = "none";
        button.style.background = "transparent";
        button.style.cursor = "pointer";
        button.style.lineHeight = "0";

        const img = document.createElement("img");
        img.src = getPinUrl();
        img.alt = "";
        img.className = "add-business-map-pin";
        button.appendChild(img);

        marker = new maplibregl.Marker({ element: button, anchor: "bottom" })
            .setLngLat([lng, lat])
            .addTo(map);
    }

    function setLocation(lng, lat, options) {
        const opts = options || {};

        if (!latInput || !lngInput) {
            return;
        }

        selectedLngLat = { lng: lng, lat: lat };
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        updateLocationStatus(lat, lng);
        updateMarker(lng, lat);

        if (opts.flyTo && map) {
            map.easeTo({
                center: [lng, lat],
                zoom: Math.max(map.getZoom(), 14),
                duration: 450,
            });
        }
    }

    window.zecmapAddListing = {
        setLocation: setLocation,
    };

    if (mapContainer && typeof maplibregl !== "undefined") {
        function initMap() {
            if (map) {
                map.remove();
                map = null;
                marker = null;
            }

            const oldLat = latInput?.value ? parseFloat(latInput.value) : NaN;
            const oldLng = lngInput?.value ? parseFloat(lngInput.value) : NaN;
            const hasCoords = !Number.isNaN(oldLat) && !Number.isNaN(oldLng);

            map = new maplibregl.Map({
                container: mapContainer,
                style: getStyleUrl(),
                center: hasCoords ? [oldLng, oldLat] : [10, 25],
                zoom: hasCoords ? 12 : 2,
                minZoom: 1,
                maxZoom: 20,
            });

            map.addControl(
                new maplibregl.NavigationControl({ showCompass: false }),
                "top-right",
            );

            map.on("load", function () {
                map.resize();

                if (hasCoords) {
                    setLocation(oldLng, oldLat);
                }
            });

            map.on("click", function (event) {
                setLocation(event.lngLat.lng, event.lngLat.lat);
            });
        }

        initMap();

        paymentSelect?.addEventListener("change", function () {
            if (selectedLngLat) {
                updateMarker(selectedLngLat.lng, selectedLngLat.lat);
            }
        });

        useLocationBtn?.addEventListener("click", function () {
            if (!navigator.geolocation) {
                window.alert("Geolocation is not supported in this browser.");
                return;
            }

            useLocationBtn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    setLocation(
                        position.coords.longitude,
                        position.coords.latitude,
                        { flyTo: true },
                    );
                    useLocationBtn.disabled = false;
                },
                function () {
                    useLocationBtn.disabled = false;
                    window.alert(
                        "Could not get your location. Please pin the map manually.",
                    );
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                },
            );
        });

        const themeObserver = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (
                    mutation.type === "attributes" &&
                    mutation.attributeName === "class"
                ) {
                    const current = selectedLngLat;
                    initMap();

                    if (current) {
                        map.once("load", function () {
                            setLocation(current.lng, current.lat);
                        });
                    }
                }
            });
        });

        themeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ["class"],
        });

        window.addEventListener("resize", function () {
            if (map) {
                map.resize();
            }
        });
    }

    form?.addEventListener("submit", function (event) {
        if (!latInput?.value || !lngInput?.value) {
            event.preventDefault();
            window.alert("Please select a location on the map.");
            mapContainer?.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
        }
    });

    initLogoUpload();
    initAddressAutocomplete();
})();

function initLogoUpload() {
    const input = document.getElementById("ab-logo-input");
    const previewWrap = document.getElementById("ab-logo-preview");
    const previewImage = document.getElementById("ab-logo-preview-image");
    const removeBtn = document.getElementById("ab-logo-remove");
    const uploadLabel = document.getElementById("ab-logo-upload-label");
    const uploadText = document.getElementById("ab-logo-upload-text");
    const defaultUploadText = "Upload logo (max 4 MB)";
    const maxLogoBytes = 4 * 1024 * 1024;

    if (!input || !previewWrap || !previewImage) {
        return;
    }

    let objectUrl = null;

    function revokeObjectUrl() {
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }
    }

    function clearPreview() {
        revokeObjectUrl();
        input.value = "";
        previewImage.removeAttribute("src");
        previewWrap.hidden = true;
        uploadLabel?.classList.remove("has-file");

        if (uploadText) {
            uploadText.textContent = defaultUploadText;
        }
    }

    function showPreview(file) {
        revokeObjectUrl();
        objectUrl = URL.createObjectURL(file);
        previewImage.src = objectUrl;
        previewWrap.hidden = false;
        uploadLabel?.classList.add("has-file");

        if (uploadText) {
            uploadText.textContent = file.name;
        }
    }

    input.addEventListener("change", function () {
        const file = input.files?.[0];

        if (!file) {
            clearPreview();
            return;
        }

        if (!file.type.startsWith("image/")) {
            window.alert("Please select an image file (JPG, PNG, GIF, or WebP).");
            clearPreview();
            return;
        }

        if (file.size > maxLogoBytes) {
            window.alert("Logo must be 4 MB or smaller.");
            clearPreview();
            return;
        }

        showPreview(file);
    });

    removeBtn?.addEventListener("click", function (event) {
        event.preventDefault();
        event.stopPropagation();
        clearPreview();
    });

    window.addEventListener("pagehide", revokeObjectUrl);
}

function initAddressAutocomplete() {
    const addressInput = document.getElementById("ab-addr");
    const dropdown = document.getElementById("ab-address-suggestions");
    const suggestUrlEl = document.getElementById("ab-address-suggest-url");

    if (!addressInput || !dropdown || !suggestUrlEl) {
        return;
    }

    const suggestUrl = suggestUrlEl.dataset.url;
    const addressField = addressInput.closest(".add-business-field");
    let timer = null;
    let requestId = 0;
    let activeIndex = -1;
    let currentItems = [];

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }

    function hideSuggestions() {
        dropdown.hidden = true;
        dropdown.innerHTML = "";
        activeIndex = -1;
        currentItems = [];
        addressInput.setAttribute("aria-expanded", "false");
        addressField?.classList.remove("is-address-suggestions-open");
    }

    function highlightActiveOption() {
        dropdown.querySelectorAll(".add-business-address-option").forEach(function (button, index) {
            button.classList.toggle("is-active", index === activeIndex);
        });
    }

    function selectSuggestion(item) {
        addressInput.value = item.label;
        hideSuggestions();

        if (window.zecmapAddListing?.setLocation) {
            window.zecmapAddListing.setLocation(item.lng, item.lat, {
                flyTo: true,
            });
        }
    }

    function renderSuggestions(items) {
        currentItems = items;

        if (items.length === 0) {
            hideSuggestions();
            return;
        }

        dropdown.hidden = false;
        addressInput.setAttribute("aria-expanded", "true");
        addressField?.classList.add("is-address-suggestions-open");
        dropdown.innerHTML = items
            .map(function (item, index) {
                const subtitle = item.city
                    ? escapeHtml(item.city)
                    : "Suggested location";

                return (
                    '<button type="button" class="add-business-address-option" role="option" data-index="' +
                    index +
                    '">' +
                    '<span class="add-business-address-option-main">' +
                    escapeHtml(item.label) +
                    "</span>" +
                    '<span class="add-business-address-option-sub">' +
                    subtitle +
                    "</span>" +
                    "</button>"
                );
            })
            .join("");

        dropdown.querySelectorAll(".add-business-address-option").forEach(function (button) {
            button.addEventListener("click", function () {
                const item = currentItems[Number(button.dataset.index)];
                if (item) {
                    selectSuggestion(item);
                }
            });
        });
    }

    function fetchSuggestions(query) {
        const currentRequestId = ++requestId;
        const url = suggestUrl + "?q=" + encodeURIComponent(query);

        fetch(url, {
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error("Address suggest failed");
                }
                return response.json();
            })
            .then(function (data) {
                if (currentRequestId !== requestId) {
                    return;
                }

                activeIndex = -1;
                renderSuggestions(data.suggestions || []);
            })
            .catch(function () {
                if (currentRequestId !== requestId) {
                    return;
                }

                hideSuggestions();
            });
    }

    addressInput.addEventListener("input", function () {
        const query = addressInput.value.trim();

        if (timer) {
            clearTimeout(timer);
        }

        if (query.length < 3) {
            requestId++;
            hideSuggestions();
            return;
        }

        timer = window.setTimeout(function () {
            fetchSuggestions(query);
        }, 280);
    });

    addressInput.addEventListener("keydown", function (event) {
        if (dropdown.hidden || currentItems.length === 0) {
            return;
        }

        if (event.key === "ArrowDown") {
            event.preventDefault();
            activeIndex = Math.min(activeIndex + 1, currentItems.length - 1);
            highlightActiveOption();
        } else if (event.key === "ArrowUp") {
            event.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            highlightActiveOption();
        } else if (event.key === "Enter" && activeIndex >= 0) {
            event.preventDefault();
            selectSuggestion(currentItems[activeIndex]);
        } else if (event.key === "Escape") {
            hideSuggestions();
        }
    });

    document.addEventListener("click", function (event) {
        if (
            !addressInput.contains(event.target) &&
            !dropdown.contains(event.target)
        ) {
            hideSuggestions();
        }
    });
}
