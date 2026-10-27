(function () {
    const form = document.querySelector("[data-business-reviews-form]");
    if (!form) {
        return;
    }

    const ratingInput = form.querySelector("[data-review-rating-input]");
    const bodyInput = form.querySelector("[data-review-body]");
    const charCount = form.querySelector("[data-review-char-count]");
    const submitButton = form.querySelector("[data-review-submit]");
    const starButtons = form.querySelectorAll("[data-star-value]");
    const imageInput = form.querySelector("[data-review-image-input]");
    const uploadRow = form.querySelector(".biz-reviews-upload-row");

    let selectedRating = parseInt(ratingInput?.value || "0", 10) || 0;
    let previewObjectUrl = null;
    let imagePreviewWrap = null;
    let imagePreview = null;
    let imageRemoveButton = null;

    function getOrCreateImagePreviewWrap() {
        if (imagePreviewWrap) {
            return imagePreviewWrap;
        }

        if (!uploadRow) {
            return null;
        }

        imagePreviewWrap = document.createElement("div");
        imagePreviewWrap.className = "biz-reviews-image-preview-wrap";
        imagePreviewWrap.dataset.reviewImagePreviewWrap = "";

        imagePreview = document.createElement("img");
        imagePreview.className = "biz-reviews-image-preview";
        imagePreview.dataset.reviewImagePreview = "";
        imagePreview.alt = "Review photo preview";

        imageRemoveButton = document.createElement("button");
        imageRemoveButton.type = "button";
        imageRemoveButton.className = "biz-reviews-image-remove";
        imageRemoveButton.dataset.reviewImageRemove = "";
        imageRemoveButton.setAttribute("aria-label", "Remove photo");
        imageRemoveButton.innerHTML =
            '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>';
        imageRemoveButton.addEventListener("click", clearImagePreview);

        imagePreviewWrap.appendChild(imagePreview);
        imagePreviewWrap.appendChild(imageRemoveButton);
        uploadRow.appendChild(imagePreviewWrap);

        return imagePreviewWrap;
    }

    function destroyImagePreviewWrap() {
        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
            previewObjectUrl = null;
        }

        if (imagePreviewWrap) {
            imagePreviewWrap.remove();
            imagePreviewWrap = null;
            imagePreview = null;
            imageRemoveButton = null;
        }
    }

    function updateStars() {
        starButtons.forEach((button) => {
            const value = parseInt(button.dataset.starValue || "0", 10);
            const star = button.querySelector(".biz-reviews-star");
            if (star) {
                star.classList.toggle(
                    "biz-reviews-star--active",
                    value <= selectedRating,
                );
            }
        });
    }

    function updateCharCount() {
        if (!bodyInput || !charCount) {
            return;
        }

        const length = bodyInput.value.length;
        charCount.textContent = `${length}/1000`;
    }

    function updateSubmitState() {
        if (!submitButton) {
            return;
        }

        const bodyLength = bodyInput?.value.trim().length || 0;
        submitButton.disabled = selectedRating < 1 || bodyLength < 10;
    }

    starButtons.forEach((button) => {
        button.addEventListener("click", () => {
            selectedRating = parseInt(button.dataset.starValue || "0", 10);
            if (ratingInput) {
                ratingInput.value = String(selectedRating);
            }
            updateStars();
            updateSubmitState();
        });
    });

    bodyInput?.addEventListener("input", () => {
        updateCharCount();
        updateSubmitState();
    });

    function clearImagePreview() {
        if (imageInput) {
            imageInput.value = "";
        }

        destroyImagePreviewWrap();
    }

    imageInput?.addEventListener("change", () => {
        const file = imageInput.files?.[0];
        if (!file) {
            clearImagePreview();
            return;
        }

        destroyImagePreviewWrap();
        const wrap = getOrCreateImagePreviewWrap();
        if (!wrap || !imagePreview) {
            return;
        }

        previewObjectUrl = URL.createObjectURL(file);
        imagePreview.src = previewObjectUrl;
    });

    updateStars();
    updateCharCount();
    updateSubmitState();
})();
