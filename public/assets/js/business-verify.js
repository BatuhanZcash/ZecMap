(function () {
    const modal = document.getElementById("biz-verify-modal");

    if (!modal) {
        return;
    }

    function openModal() {
        modal.hidden = false;
        modal.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }

    function closeModal() {
        modal.hidden = true;
        modal.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
    }

    document.querySelectorAll("[data-open-verify-modal]").forEach(function (button) {
        button.addEventListener("click", openModal);
    });

    modal.querySelectorAll("[data-close-verify-modal]").forEach(function (element) {
        element.addEventListener("click", closeModal);
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && !modal.hidden) {
            closeModal();
        }
    });

    const howVerifiedField = document.getElementById("verify-how-verified");
    if (howVerifiedField && howVerifiedField.value.trim() !== "") {
        openModal();
    }
})();
