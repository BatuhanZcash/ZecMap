(function () {
    const editButton = document.querySelector("[data-profile-edit]");
    const displayView = document.querySelector("[data-profile-display]");
    const editForm = document.querySelector("[data-profile-edit-form]");
    const cancelButton = document.querySelector("[data-profile-cancel]");
    const bioTextarea = document.getElementById("profile-bio");
    const charCount = document.querySelector("[data-profile-bio-count]");
    const avatarInput = document.getElementById("profile-avatar-input");
    const avatarPreview = document.querySelector("[data-profile-avatar-preview]");
    const avatarInitials = document.querySelector("[data-profile-avatar-initials]");

    if (!editForm) {
        return;
    }

    function setEditMode(isEditing) {
        editForm.classList.toggle("is-open", isEditing);

        if (displayView) {
            displayView.classList.toggle("is-hidden", isEditing);
        }
    }

    function updateCharCount() {
        if (!bioTextarea || !charCount) {
            return;
        }

        charCount.textContent = bioTextarea.value.length + "/280";
    }

    editButton?.addEventListener("click", function () {
        setEditMode(true);
        updateCharCount();
    });

    cancelButton?.addEventListener("click", function () {
        setEditMode(false);
    });

    bioTextarea?.addEventListener("input", updateCharCount);

    avatarInput?.addEventListener("change", function () {
        const file = avatarInput.files && avatarInput.files[0];
        if (!file || !avatarPreview) {
            return;
        }

        const reader = new FileReader();
        reader.onload = function () {
            avatarPreview.src = reader.result;
            avatarPreview.hidden = false;

            if (avatarInitials) {
                avatarInitials.hidden = true;
            }
        };
        reader.readAsDataURL(file);
    });

    if (editForm.classList.contains("is-open")) {
        updateCharCount();
    }
})();
