(function () {
    function bindCardToggles(root) {
        (root || document).querySelectorAll(".communities-card-toggle:not([data-bound])").forEach(function (button) {
            button.dataset.bound = "1";
            button.addEventListener("click", function () {
                const card = button.closest(".communities-card");
                if (!card) {
                    return;
                }

                const expanded = button.getAttribute("aria-expanded") === "true";
                button.setAttribute("aria-expanded", expanded ? "false" : "true");
                card.classList.toggle("communities-card--expanded", !expanded);

                const wrap = card.querySelector(".communities-card-scores-wrap");
                const chevron = card.querySelector(".communities-card-chevron");
                if (wrap) {
                    wrap.classList.toggle("communities-card-scores-wrap--open", !expanded);
                }
                if (chevron) {
                    chevron.classList.toggle("communities-card-chevron--open", !expanded);
                }
            });
        });
    }

    bindCardToggles();

    const loadMoreBtn = document.getElementById("communities-load-more");
    if (!loadMoreBtn) {
        return;
    }

    loadMoreBtn.addEventListener("click", async function () {
        const url = new URL(loadMoreBtn.dataset.url, window.location.origin);
        url.searchParams.set("period", loadMoreBtn.dataset.period);
        url.searchParams.set("offset", loadMoreBtn.dataset.offset);

        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = "Yükleniyor...";

        try {
            const response = await fetch(url.toString(), {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error("Request failed");
            }

            const data = await response.json();
            const list = document.getElementById("communities-list");

            if (list && data.html) {
                list.insertAdjacentHTML("beforeend", data.html);
                bindCardToggles(list);
            }

            if (data.hasMore) {
                loadMoreBtn.dataset.offset = String(data.nextOffset);
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = "Devamını gör";
            } else {
                document.getElementById("communities-load-more-wrap")?.remove();
            }
        } catch (error) {
            loadMoreBtn.disabled = false;
            loadMoreBtn.textContent = "Devamını gör";
        }
    });
})();
