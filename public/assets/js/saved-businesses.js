(function () {
    const favorites = window.ZecMapFavorites;
    const config = window.ZECMAP_FAVORITES || {};

    if (!favorites) {
        return;
    }

    function bindRemoveButtons() {
        document.querySelectorAll("[data-saved-remove]").forEach(function (button) {
            button.addEventListener("click", async function () {
                const slug = button.getAttribute("data-saved-remove");
                if (!slug) {
                    return;
                }

                button.disabled = true;

                try {
                    await favorites.toggle(slug);
                    const row = button.closest("[data-saved-slug]");
                    if (row) {
                        row.remove();
                    }

                    const list = document.getElementById("saved-business-list");
                    if (list && list.children.length === 0) {
                        window.location.reload();
                    }
                } catch {
                    button.disabled = false;
                }
            });
        });
    }

    favorites.init(config).then(function () {
        bindRemoveButtons();
    });
})();
