(function () {
    const page = document.querySelector("[data-browse-page]");
    if (!page) {
        return;
    }

    const apiUrl = page.dataset.browseUrl;
    const siteTotalMerchants = Number(page.dataset.browseTotal || 0);
    const grid = document.getElementById("browse-grid");
    const emptyState = document.getElementById("browse-empty");
    const resultsCount = document.getElementById("browse-results-count");
    const loadMoreWrap = document.getElementById("browse-load-more-wrap");
    const loadMoreButton = document.getElementById("browse-load-more-btn");
    const searchInputs = page.querySelectorAll("[data-browse-search]");
    const categoryButtons = page.querySelectorAll("[data-browse-category]");
    const filterChips = page.querySelectorAll("[data-browse-chip]");
    const sortButtons = page.querySelectorAll("[data-browse-sort]");
    const sortSelect = document.getElementById("browse-sort-select");

    if (!apiUrl || !grid) {
        return;
    }

    let activeCategory = "";
    let activeSort = "newest";
    let searchQuery = "";
    let nextPage = page.dataset.browseNextPage
        ? Number(page.dataset.browseNextPage)
        : null;
    let hasMore = page.dataset.browseHasMore === "1";
    let isLoading = false;
    let searchTimer = null;

    function setActiveCategory(slug) {
        activeCategory = slug;

        categoryButtons.forEach(function (button) {
            const isActive = button.dataset.browseCategory === slug;
            button.classList.toggle("browse-category-button--active", isActive);
        });

        filterChips.forEach(function (chip) {
            const isActive = chip.dataset.browseChip === slug;
            chip.classList.toggle("browse-filter-chip--active", isActive);
        });
    }

    function setActiveSort(sort) {
        activeSort = sort;

        sortButtons.forEach(function (button) {
            button.classList.toggle(
                "browse-sort-button--active",
                button.dataset.browseSort === sort,
            );
        });

        if (sortSelect) {
            sortSelect.value = sort === "za" ? "az" : sort;
        }
    }

    function updateResultsCount(total) {
        if (!resultsCount) {
            return;
        }

        resultsCount.textContent =
            total + " Place" + (total === 1 ? "" : "s") + " Found";
    }

    function updateEmptyState(total, hasAnyMerchants) {
        if (!emptyState) {
            return;
        }

        emptyState.hidden = total > 0;
        grid.hidden = total === 0;

        const subtitle = emptyState.querySelector(".browse-empty-subtitle");
        if (subtitle) {
            subtitle.textContent = hasAnyMerchants
                ? "Try adjusting your search or category filter."
                : "There are no active merchants listed yet. Check back soon.";
        }
    }

    function setLoadMoreState(loading) {
        if (!loadMoreButton) {
            return;
        }

        loadMoreButton.disabled = loading;
        loadMoreButton.textContent = loading ? "Loading…" : "Show more";
    }

    function updateLoadMoreVisibility() {
        if (!loadMoreWrap) {
            return;
        }

        loadMoreWrap.hidden = !hasMore;
        setLoadMoreState(false);
    }

    function buildRequestUrl(pageNumber) {
        const params = new URLSearchParams();
        params.set("page", String(pageNumber));

        if (searchQuery.trim() !== "") {
            params.set("search", searchQuery.trim());
        }

        if (activeCategory !== "") {
            params.set("category", activeCategory);
        }

        if (activeSort !== "") {
            params.set("sort", activeSort);
        }

        return apiUrl + "?" + params.toString();
    }

    function appendHtml(html) {
        const template = document.createElement("template");
        template.innerHTML = html.trim();
        const fragment = document.createDocumentFragment();
        Array.from(template.content.children).forEach(function (node) {
            fragment.appendChild(node);
        });
        grid.appendChild(fragment);
    }

    function replaceGridHtml(html) {
        grid.innerHTML = html.trim();
    }

    async function fetchBusinesses(pageNumber, append) {
        if (isLoading) {
            return;
        }

        isLoading = true;
        setLoadMoreState(true);

        try {
            const response = await fetch(buildRequestUrl(pageNumber), {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error("Failed to load merchants.");
            }

            const payload = await response.json();

            if (append) {
                appendHtml(payload.html || "");
            } else {
                replaceGridHtml(payload.html || "");
            }

            hasMore = Boolean(payload.has_more);
            nextPage = payload.next_page ? Number(payload.next_page) : null;

            updateResultsCount(Number(payload.total) || 0);
            updateEmptyState(Number(payload.total) || 0, siteTotalMerchants > 0);
            updateLoadMoreVisibility();
        } catch (error) {
            if (loadMoreButton) {
                loadMoreButton.disabled = false;
                loadMoreButton.textContent = "Try again";
            }
            hasMore = true;
        } finally {
            isLoading = false;

            if (loadMoreButton && loadMoreButton.textContent === "Loading…") {
                setLoadMoreState(false);
            }
        }
    }

    function resetAndFetch() {
        nextPage = 1;
        hasMore = true;
        fetchBusinesses(1, false);
    }

    function scheduleSearchFetch() {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(resetAndFetch, 350);
    }

    function loadMore() {
        if (!hasMore || isLoading || nextPage === null) {
            return;
        }

        fetchBusinesses(nextPage, true);
    }

    if (loadMoreButton) {
        loadMoreButton.addEventListener("click", loadMore);
    }

    searchInputs.forEach(function (input) {
        input.addEventListener("input", function () {
            searchQuery = input.value;
            searchInputs.forEach(function (otherInput) {
                if (otherInput !== input) {
                    otherInput.value = searchQuery;
                }
            });
            scheduleSearchFetch();
        });
    });

    categoryButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            setActiveCategory(button.dataset.browseCategory || "");
            resetAndFetch();
        });
    });

    filterChips.forEach(function (chip) {
        chip.addEventListener("click", function () {
            setActiveCategory(chip.dataset.browseChip || "");
            resetAndFetch();
        });
    });

    sortButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            setActiveSort(button.dataset.browseSort || "newest");
            resetAndFetch();
        });
    });

    if (sortSelect) {
        sortSelect.addEventListener("change", function () {
            setActiveSort(sortSelect.value || "newest");
            resetAndFetch();
        });
    }

    updateLoadMoreVisibility();
})();
