(function () {
    var toggle = document.querySelector(".nav-toggle");
    var nav = document.getElementById("primary-nav");
    if (!toggle || !nav) return;

    var docEl = document.documentElement;
    var body = document.body;

    function setNavOpen(open) {
        nav.classList.toggle("is-open", open);
        toggle.setAttribute("aria-expanded", open ? "true" : "false");
        toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
        docEl.classList.toggle("xr-nav-open", open);
        body.classList.toggle("xr-nav-open", open);
    }

    toggle.addEventListener("click", function () {
        setNavOpen(!nav.classList.contains("is-open"));
    });

    nav.querySelectorAll("a").forEach(function (link) {
        link.addEventListener("click", function () {
            setNavOpen(false);
        });
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && nav.classList.contains("is-open")) {
            setNavOpen(false);
        }
    });

    window.addEventListener(
        "resize",
        function () {
            /* Порог как в main.css: --nav-burger-max + 1px */
            if (window.matchMedia("(min-width: 1311px)").matches) {
                setNavOpen(false);
            }
        },
        { passive: true }
    );
})();
