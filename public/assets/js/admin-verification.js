(function () {
    const drawer = document.getElementById("verification-review-drawer");
    const dataEl = document.getElementById("verification-requests-data");
    const approveUrlTemplate = document.getElementById("verification-approve-url-template");
    const rejectUrlTemplate = document.getElementById("verification-reject-url-template");
    const approveForm = document.getElementById("verification-approve-form");
    const rejectForm = document.getElementById("verification-reject-form");
    const rejectBtn = document.getElementById("verification-reject-btn");

    if (
        !drawer ||
        !dataEl ||
        !approveUrlTemplate ||
        !rejectUrlTemplate ||
        !approveForm ||
        !rejectForm ||
        !rejectBtn
    ) {
        return;
    }

    const requests = JSON.parse(dataEl.textContent || "[]");
    const requestMap = new Map(requests.map((item) => [String(item.id), item]));

    const titleEl = document.getElementById("verification-review-title");
    const businessNameEl = document.getElementById("verification-business-name");
    const businessSlugEl = document.getElementById("verification-business-slug");
    const businessVerifiedEl = document.getElementById("verification-business-verified");
    const userNameEl = document.getElementById("verification-user-name");
    const outdatedInfoEl = document.getElementById("verification-outdated-info");
    const howVerifiedEl = document.getElementById("verification-how-verified");
    const reviewedMetaEl = document.getElementById("verification-reviewed-meta");
    const reviewedAtEl = document.getElementById("verification-reviewed-at");
    const adminNoteEl = document.getElementById("verification-admin-note");
    const rejectNoteEl = document.getElementById("verification-reject-note");

    function setText(el, value, fallback) {
        if (!el) {
            return;
        }

        el.textContent = value && String(value).trim() !== "" ? value : fallback || "—";
    }

    function openDrawer(requestId) {
        const item = requestMap.get(String(requestId));
        if (!item) {
            return;
        }

        titleEl.textContent = "Doğrulama İsteği";
        setText(businessNameEl, item.business?.name, "—");
        setText(
            businessSlugEl,
            item.business?.slug ? "/" + item.business.slug : "",
            "—",
        );

        if (item.business?.verified) {
            businessVerifiedEl.hidden = false;
        } else {
            businessVerifiedEl.hidden = true;
        }

        setText(userNameEl, item.user?.name, "—");
        setText(outdatedInfoEl, item.outdated_info, "Belirtilmedi");
        setText(howVerifiedEl, item.how_verified, "—");

        adminNoteEl.value = item.admin_note || "";
        rejectNoteEl.value = item.admin_note || "";

        const isPending = item.status === "pending";
        approveForm.hidden = !isPending;
        rejectBtn.hidden = !isPending;

        if (item.reviewed_at) {
            reviewedMetaEl.hidden = false;
            reviewedAtEl.textContent = item.reviewed_at;
        } else {
            reviewedMetaEl.hidden = true;
        }

        approveForm.action = approveUrlTemplate.dataset.url.replace("__ID__", item.id);
        rejectForm.action = rejectUrlTemplate.dataset.url.replace("__ID__", item.id);

        drawer.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function closeDrawer() {
        drawer.classList.remove("is-open");
        drawer.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
    }

    document.querySelectorAll(".btn-review-verification").forEach(function (button) {
        button.addEventListener("click", function () {
            openDrawer(button.dataset.id);
        });
    });

    drawer.querySelectorAll("[data-close-drawer]").forEach(function (element) {
        element.addEventListener("click", closeDrawer);
    });

    rejectBtn.addEventListener("click", function () {
        rejectNoteEl.value = adminNoteEl.value;
        rejectForm.submit();
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && drawer.classList.contains("is-open")) {
            closeDrawer();
        }
    });
})();
