(function () {
    const drawer = document.getElementById("community-edit-drawer");
    const form = document.getElementById("community-edit-form");
    const dataEl = document.getElementById("communities-data");
    const updateUrlTemplate = document.getElementById("community-update-url-template");
    const storeUrlEl = document.getElementById("community-store-url");
    const methodInput = document.getElementById("community-form-method");
    const titleEl = document.getElementById("community-edit-title");

    if (!drawer || !form || !dataEl || !updateUrlTemplate || !storeUrlEl || !methodInput || !titleEl) {
        return;
    }

    const communities = JSON.parse(dataEl.textContent || "[]");
    const communityMap = new Map(communities.map((item) => [String(item.id), item]));

    function field(id) {
        return document.getElementById(id);
    }

    function openCreateDrawer() {
        form.action = storeUrlEl.dataset.url;
        methodInput.value = "POST";
        titleEl.textContent = "+ Yeni Topluluk";

        field("field-name").value = "";
        field("field-slug").value = "";
        field("field-flag_emoji").value = "";
        field("field-description").value = "";

        drawer.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function openEditDrawer(communityId) {
        const community = communityMap.get(String(communityId));
        if (!community) {
            return;
        }

        form.action = updateUrlTemplate.dataset.url.replace("__ID__", community.id);
        methodInput.value = "PUT";
        titleEl.textContent = "Düzenle: " + community.name;

        field("field-name").value = community.name || "";
        field("field-slug").value = community.slug || "";
        field("field-flag_emoji").value = community.flag_emoji || "";
        field("field-description").value = community.description || "";

        drawer.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function closeDrawer() {
        drawer.classList.remove("is-open");
        drawer.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
    }

    document.querySelectorAll("[data-open-create-community]").forEach((button) => {
        button.addEventListener("click", openCreateDrawer);
    });

    document.querySelectorAll("[data-edit-community]").forEach((button) => {
        button.addEventListener("click", function () {
            openEditDrawer(button.dataset.id);
        });
    });

    document.querySelectorAll("[data-close-drawer]").forEach((button) => {
        button.addEventListener("click", closeDrawer);
    });

    document.querySelectorAll("[data-delete-community]").forEach((deleteForm) => {
        deleteForm.addEventListener("submit", function (event) {
            if (!window.confirm("Bu topluluğu silmek istediğinize emin misiniz?")) {
                event.preventDefault();
            }
        });
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && drawer.classList.contains("is-open")) {
            closeDrawer();
        }
    });
})();
