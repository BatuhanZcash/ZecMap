(function () {
    const root = document.documentElement;
    const THEME_STORAGE_KEY = "zecmap-theme";

    function readStoredTheme() {
        try {
            return localStorage.getItem(THEME_STORAGE_KEY);
        } catch (error) {
            return null;
        }
    }

    function saveTheme(isDark) {
        try {
            localStorage.setItem(THEME_STORAGE_KEY, isDark ? "dark" : "light");
        } catch (error) {}
    }

    function applyTheme(isDark) {
        root.classList.toggle("dark", isDark);
        saveTheme(isDark);
    }

    function syncThemeFromStorage() {
        const storedTheme = readStoredTheme();

        if (storedTheme === "dark") {
            root.classList.add("dark");
        } else if (storedTheme === "light") {
            root.classList.remove("dark");
        }
    }

    syncThemeFromStorage();

    document
        .querySelectorAll(".header-theme-button")
        .forEach(function (button) {
            button.addEventListener("click", function () {
                applyTheme(!root.classList.contains("dark"));
            });
        });

    const drawer = document.querySelector(".header-drawer");
    const menuButton = document.querySelector(".header-mobile-menu-button");
    const drawerClose = document.querySelector(".header-drawer-close");
    const drawerBackdrop = document.querySelector(".header-drawer-backdrop");

    function setDrawerOpen(isOpen) {
        if (!drawer) {
            return;
        }

        drawer.classList.toggle("header-drawer--open", isOpen);
        drawer.setAttribute("aria-hidden", isOpen ? "false" : "true");

        if (menuButton) {
            menuButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
        }
    }

    if (menuButton) {
        menuButton.addEventListener("click", function () {
            setDrawerOpen(!drawer.classList.contains("header-drawer--open"));
        });
    }

    if (drawerClose) {
        drawerClose.addEventListener("click", function () {
            setDrawerOpen(false);
        });
    }

    if (drawerBackdrop) {
        drawerBackdrop.addEventListener("click", function () {
            setDrawerOpen(false);
        });
    }

    function closeAllLangDropdowns() {
        document.querySelectorAll("[data-lang-switcher-dropdown]").forEach(function (dropdown) {
            dropdown.hidden = true;
        });
        document.querySelectorAll("[data-lang-switcher-button]").forEach(function (button) {
            button.setAttribute("aria-expanded", "false");
        });
    }

    document.querySelectorAll("[data-lang-switcher]").forEach(function (switcher) {
        const button = switcher.querySelector("[data-lang-switcher-button]");
        const dropdown = switcher.querySelector("[data-lang-switcher-dropdown]");

        if (!button || !dropdown) {
            return;
        }

        button.addEventListener("click", function (event) {
            event.stopPropagation();
            const willOpen = dropdown.hidden;
            closeAllLangDropdowns();
            dropdown.hidden = !willOpen;
            button.setAttribute("aria-expanded", willOpen ? "true" : "false");
        });
    });

    document.addEventListener("click", closeAllLangDropdowns);

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeAllLangDropdowns();
            setDrawerOpen(false);
        }
    });
})();
