(function () {
    const drawer = document.getElementById("category-edit-drawer");
    const form = document.getElementById("category-edit-form");
    const dataEl = document.getElementById("categories-data");
    const updateUrlTemplate = document.getElementById("category-update-url-template");
    const storeUrlEl = document.getElementById("category-store-url");
    const methodInput = document.getElementById("category-form-method");
    const titleEl = document.getElementById("category-edit-title");
    const nameInput = document.getElementById("field-name");
    const slugInput = document.getElementById("field-slug");

    if (!drawer || !form || !dataEl || !updateUrlTemplate || !storeUrlEl || !methodInput || !titleEl) {
        return;
    }

    const categories = JSON.parse(dataEl.textContent || "[]");
    const categoryMap = new Map(categories.map((item) => [String(item.id), item]));
    let slugTouched = false;

    function slugify(value) {
        return value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, "")
            .replace(/\s+/g, "-")
            .replace(/-+/g, "-")
            .replace(/^-|-$/g, "");
    }

    function openCreateDrawer() {
        form.action = storeUrlEl.dataset.url;
        methodInput.value = "POST";
        titleEl.textContent = "+ Yeni Kategori";
        slugTouched = false;

        nameInput.value = "";
        slugInput.value = "";

        drawer.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function openEditDrawer(categoryId) {
        const category = categoryMap.get(String(categoryId));
        if (!category) {
            return;
        }

        form.action = updateUrlTemplate.dataset.url.replace("__ID__", category.id);
        methodInput.value = "PUT";
        titleEl.textContent = "Düzenle: " + category.name;
        slugTouched = true;

        nameInput.value = category.name || "";
        slugInput.value = category.slug || "";

        drawer.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function closeDrawer() {
        drawer.classList.remove("is-open");
        drawer.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
    }

    if (nameInput && slugInput) {
        nameInput.addEventListener("input", function () {
            if (!slugTouched) {
                slugInput.value = slugify(nameInput.value);
            }
        });

        slugInput.addEventListener("input", function () {
            slugTouched = slugInput.value.trim() !== "";
        });
    }

    document.querySelectorAll("[data-open-create-category]").forEach((button) => {
        button.addEventListener("click", openCreateDrawer);
    });

    document.querySelectorAll("[data-edit-category]").forEach((button) => {
        button.addEventListener("click", function () {
            openEditDrawer(button.dataset.id);
        });
    });

    document.querySelectorAll("[data-close-drawer]").forEach((button) => {
        button.addEventListener("click", closeDrawer);
    });

    document.querySelectorAll("[data-delete-category]").forEach((deleteForm) => {
        deleteForm.addEventListener("submit", function (event) {
            if (!window.confirm("Bu kategoriyi silmek istediğinize emin misiniz?")) {
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
