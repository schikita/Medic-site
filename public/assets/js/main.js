(function () {
    var toggle = document.querySelector(".nav-toggle");
    var nav = document.getElementById("primary-nav");
    if (!toggle || !nav) return;

    var docEl = document.documentElement;
    var body = document.body;
    var header = document.querySelector(".site-header");
    var savedScroll = 0;

    function positionNav() {
        if (!header) return;
        var rect = header.getBoundingClientRect();
        nav.style.top = rect.bottom + "px";
    }

    function setNavOpen(open) {
        if (open) {
            savedScroll = window.scrollY || window.pageYOffset;
            positionNav();
            body.style.position = "fixed";
            body.style.top = "-" + savedScroll + "px";
            body.style.left = "0";
            body.style.right = "0";
        } else {
            body.style.position = "";
            body.style.top = "";
            body.style.left = "";
            body.style.right = "";
            window.scrollTo(0, savedScroll);
        }
        nav.classList.toggle("is-open", open);
        toggle.setAttribute("aria-expanded", open ? "true" : "false");
        toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
        docEl.classList.toggle("xr-nav-open", open);
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

(function () {
    var btn = document.getElementById("xr-back-top");
    if (!btn) return;

    btn.removeAttribute("hidden");

    window.addEventListener("scroll", function () {
        btn.classList.toggle("is-visible", window.scrollY > 400);
    }, { passive: true });

    btn.addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
})();
