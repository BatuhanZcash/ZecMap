(function () {
    const donationBox = document.querySelector("[data-donation-address]");
    const copyDonationBtn = document.querySelector("[data-copy-donation]");
    const copyPageLinkBtn = document.querySelector("[data-copy-page-link]");

    async function copyText(text, button, successLabel) {
        if (!text || !button) {
            return;
        }

        const originalLabel = button.textContent;

        try {
            await navigator.clipboard.writeText(text.trim());
            button.textContent = successLabel;
            window.setTimeout(function () {
                button.textContent = originalLabel;
            }, 2000);
        } catch (error) {
            button.textContent = "Copy failed";
            window.setTimeout(function () {
                button.textContent = originalLabel;
            }, 2000);
        }
    }

    if (copyDonationBtn && donationBox) {
        copyDonationBtn.addEventListener("click", function () {
            copyText(donationBox.textContent || "", copyDonationBtn, "Copied!");
        });
    }

    if (copyPageLinkBtn) {
        copyPageLinkBtn.addEventListener("click", function () {
            copyText(window.location.href, copyPageLinkBtn, "Copied!");
        });
    }
})();
