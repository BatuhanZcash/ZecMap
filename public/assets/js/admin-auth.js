(function () {
    const toggle = document.querySelector(".zlogin__pw-toggle");
    const passwordInput = document.getElementById("password");

    if (!toggle || !passwordInput) {
        return;
    }

    toggle.addEventListener("click", function () {
        const isPassword = passwordInput.type === "password";
        passwordInput.type = isPassword ? "text" : "password";
        toggle.setAttribute(
            "aria-label",
            isPassword ? "Şifreyi gizle" : "Şifreyi göster",
        );
    });
})();
