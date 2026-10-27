document.addEventListener("DOMContentLoaded", function () {
    var textarea = document.getElementById("blog-content");
    var charCountEl = document.getElementById("blog-content-char-count");
    var errorEl = document.getElementById("blog-editor-error");

    if (!textarea) {
        return;
    }

    if (typeof ClassicEditor === "undefined") {
        showError("CKEditor kütüphanesi yüklenemedi.");
        return;
    }

    function showError(message) {
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.hidden = false;
        }
        console.error(message);
    }

    function updateCharCount(html) {
        if (!charCountEl) {
            return;
        }

        var text = html
            .replace(/<[^>]*>/g, " ")
            .replace(/\s+/g, " ")
            .trim();

        charCountEl.textContent = text.length + " karakter";
    }

    function Base64UploadAdapter(loader) {
        this.loader = loader;
    }

    Base64UploadAdapter.prototype.upload = function () {
        var loader = this.loader;

        return loader.file.then(function (file) {
            return new Promise(function (resolve, reject) {
                var reader = new FileReader();
                reader.onload = function () {
                    resolve({ default: reader.result });
                };
                reader.onerror = function () {
                    reject(reader.error);
                };
                reader.readAsDataURL(file);
            });
        });
    };

    Base64UploadAdapter.prototype.abort = function () {};

    function uploadAdapterPlugin(editor) {
        editor.plugins.get("FileRepository").createUploadAdapter = function (
            loader,
        ) {
            return new Base64UploadAdapter(loader);
        };
    }

    ClassicEditor.create(textarea, {
        extraPlugins: [uploadAdapterPlugin],
        language: "tr",
        toolbar: [
            "heading",
            "|",
            "bold",
            "italic",
            "|",
            "link",
            "uploadImage",
            "blockQuote",
            "insertTable",
            "|",
            "bulletedList",
            "numberedList",
            "|",
            "undo",
            "redo",
        ],
        heading: {
            options: [
                {
                    model: "paragraph",
                    title: "Paragraf",
                    class: "ck-heading_paragraph",
                },
                {
                    model: "heading2",
                    view: "h2",
                    title: "Başlık 2",
                    class: "ck-heading_heading2",
                },
                {
                    model: "heading3",
                    view: "h3",
                    title: "Başlık 3",
                    class: "ck-heading_heading3",
                },
            ],
        },
    })
        .then(function (editor) {
            window.blogContentEditor = editor;
            updateCharCount(editor.getData());

            editor.model.document.on("change:data", function () {
                var data = editor.getData();
                textarea.value = data;
                updateCharCount(data);
            });

            var form = textarea.closest("form");
            if (form) {
                form.addEventListener("submit", function () {
                    textarea.value = editor.getData();
                });
            }
        })
        .catch(function (error) {
            showError("Editör başlatılamadı. Sayfayı yenileyin.");
            console.error(error);
        });

    initFeaturedImageField();
});

function initFeaturedImageField() {
    var fileInput = document.getElementById("featured_image");
    var previewWrap = document.getElementById("blog-featured-image-preview-wrap");
    var previewImage = document.getElementById("blog-featured-image-preview");
    var removeButton = document.getElementById("blog-featured-image-remove");
    var removeField = document.getElementById("remove_featured_image");

    if (!fileInput || !previewWrap || !previewImage) {
        return;
    }

    var previewObjectUrl = null;

    function revokePreviewUrl() {
        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
            previewObjectUrl = null;
        }
    }

    function hidePreview() {
        revokePreviewUrl();
        previewImage.removeAttribute("src");
        previewWrap.hidden = true;
    }

    fileInput.addEventListener("change", function () {
        var file = fileInput.files && fileInput.files[0];

        if (!file) {
            if (removeField && removeField.value === "1") {
                hidePreview();
            }
            return;
        }

        if (removeField) {
            removeField.value = "0";
        }

        revokePreviewUrl();
        previewObjectUrl = URL.createObjectURL(file);
        previewImage.src = previewObjectUrl;
        previewWrap.hidden = false;
    });

    if (removeButton) {
        removeButton.addEventListener("click", function () {
            fileInput.value = "";
            hidePreview();

            if (removeField) {
                removeField.value = "1";
            }
        });
    }
}
