(function () {
    const drawer = document.getElementById("review-moderation-drawer");
    const dataEl = document.getElementById("review-moderation-data");
    const approveUrlTemplate = document.getElementById("review-approve-url-template");
    const rejectUrlTemplate = document.getElementById("review-reject-url-template");
    const approveForm = document.getElementById("review-approve-form");
    const rejectForm = document.getElementById("review-reject-form");
    const rejectBtn = document.getElementById("review-reject-btn");

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

    const reviews = JSON.parse(dataEl.textContent || "[]");
    const reviewMap = new Map(reviews.map((item) => [String(item.id), item]));

    const businessNameEl = document.getElementById("review-business-name");
    const businessSlugEl = document.getElementById("review-business-slug");
    const userNameEl = document.getElementById("review-user-name");
    const userEmailEl = document.getElementById("review-user-email");
    const ratingEl = document.getElementById("review-rating");
    const communityWrapEl = document.getElementById("review-community-wrap");
    const communityEl = document.getElementById("review-community");
    const bodyEl = document.getElementById("review-body");
    const imageWrapEl = document.getElementById("review-image-wrap");
    const imageEl = document.getElementById("review-image");
    const reviewedMetaEl = document.getElementById("review-reviewed-meta");
    const reviewedAtEl = document.getElementById("review-reviewed-at");
    const adminNoteEl = document.getElementById("review-admin-note");
    const rejectNoteEl = document.getElementById("review-reject-note");

    function setText(el, value, fallback) {
        if (!el) {
            return;
        }

        el.textContent = value && String(value).trim() !== "" ? value : fallback || "—";
    }

    function renderStars(rating) {
        const safeRating = Math.max(0, Math.min(5, Number(rating) || 0));

        return "★".repeat(safeRating) + "☆".repeat(5 - safeRating);
    }

    function openDrawer(reviewId) {
        const item = reviewMap.get(String(reviewId));
        if (!item) {
            return;
        }

        setText(businessNameEl, item.business?.name, "—");
        setText(
            businessSlugEl,
            item.business?.slug ? "/" + item.business.slug : "",
            "—",
        );
        setText(userNameEl, item.user?.name, "—");
        setText(userEmailEl, item.user?.email, "—");
        setText(ratingEl, renderStars(item.rating), "—");
        setText(bodyEl, item.body, "—");

        if (item.community?.name) {
            communityWrapEl.hidden = false;
            const flag = item.community.flag_emoji ? item.community.flag_emoji + " " : "";
            communityEl.textContent = flag + item.community.name;
        } else {
            communityWrapEl.hidden = true;
        }

        if (item.image_url) {
            imageWrapEl.hidden = false;
            imageEl.src = item.image_url;
        } else {
            imageWrapEl.hidden = true;
            imageEl.removeAttribute("src");
        }

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

    document.querySelectorAll(".btn-review-moderation").forEach(function (button) {
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
