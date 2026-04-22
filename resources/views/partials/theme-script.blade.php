<script nonce="{{ $nonce ?? '' }}">
    var defaultThemeMode = "system";
    var themeMode;


    if (document.documentElement) {
        if (document.documentElement.hasAttribute("data-bs-theme")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme");

            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }

            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }

        if (localStorage.getItem("data-bs-theme")) {
            if (localStorage.getItem("data-bs-theme") === "system") {
                window.matchMedia("(prefers-color-scheme: dark)").matches ? localStorage.setItem("data-bs-theme",
                    "dark") : localStorage.setItem("data-bs-theme", "light");
            }
            document.documentElement.setAttribute("data-bs-theme", localStorage.getItem("data-bs-theme"));
        }
    }
</script>
