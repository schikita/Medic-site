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
        var wasOpen = nav.classList.contains("is-open");
        if (wasOpen === open) return;
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
            if (wasOpen) {
                window.scrollTo(0, savedScroll);
            }
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
            if (window.matchMedia("(min-width: 1311px)").matches && nav.classList.contains("is-open")) {
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

    var ticking = false;
    function updateVisibility() {
        btn.classList.toggle("is-visible", window.scrollY > 400);
        ticking = false;
    }

    window.addEventListener("scroll", function () {
        if (ticking) return;
        ticking = true;
        window.requestAnimationFrame(updateVisibility);
    }, { passive: true });

    btn.addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
})();

(function () {
    var root = document.querySelector("[data-xr-reality]");
    if (!root) return;
    var slides = Array.prototype.slice.call(root.querySelectorAll("[data-slide]"));
    if (!slides.length) return;

    var interval = parseInt(root.getAttribute("data-interval") || "3500", 10);
    if (!isFinite(interval) || interval < 1200) interval = 3500;

    var idx = 0;
    var timer = null;
    function show(i) {
        slides.forEach(function (s, si) {
            s.classList.toggle("is-active", si === i);
        });
        idx = i;
    }

    function start() {
        if (timer !== null) return;
        timer = window.setInterval(function () {
            show((idx + 1) % slides.length);
        }, interval);
    }

    function stop() {
        if (timer === null) return;
        window.clearInterval(timer);
        timer = null;
    }

    document.addEventListener("visibilitychange", function () {
        if (document.hidden) {
            stop();
        } else {
            start();
        }
    });

    start();
})();

(function () {
    var root = document.querySelector("[data-equip]");
    if (!root) return;
    var play = root.querySelector("[data-equip-play]");
    var box = root.querySelector("[data-equip-video]");
    if (!play || !box) return;

    // Make sure nothing blocks clicks on the play button
    play.style.pointerEvents = "auto";
    play.style.cursor = "pointer";

    function build() {
        var yt = play.getAttribute("data-yt") || "";
        var mp4 = play.getAttribute("data-mp4") || "";
        var poster = play.getAttribute("data-poster") || "";

        box.innerHTML = "";
        if (yt) {
            var ifr = document.createElement("iframe");
            ifr.className = "xr-equip__iframe";
            ifr.src =
                "https://www.youtube-nocookie.com/embed/" +
                yt +
                "?autoplay=1&mute=1&controls=1&rel=0&modestbranding=1";
            ifr.allow = "autoplay; encrypted-media; fullscreen; picture-in-picture";
            ifr.setAttribute("allowfullscreen", "");
            ifr.setAttribute("title", "Video");
            ifr.setAttribute("loading", "lazy");
            box.appendChild(ifr);
        } else if (mp4) {
            var v = document.createElement("video");
            v.className = "xr-equip__player";
            v.controls = true;
            v.playsInline = true;
            v.muted = true;
            if (poster) v.poster = poster;
            v.src = mp4;
            box.appendChild(v);
            try { v.play(); } catch (e) {}
        }
    }

    function openEquipVideo() {
        if (!box.hidden) return;
        play.hidden = true;
        box.hidden = false;
        build();
    }

    play.addEventListener("click", openEquipVideo);

    // Fallback: allow clicking anywhere on frame to start video
    root.addEventListener("click", function (e) {
        if (!box.hidden) return;
        var target = e.target;
        if (target && target.closest("[data-equip-play]")) return;
        openEquipVideo();
    });

    // Keyboard accessibility on play
    play.addEventListener("keydown", function (e) {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            openEquipVideo();
        }
    });
})();

(function () {
    var root = document.querySelector("[data-assist-tabs]");
    if (!root) return;
    var tabs = Array.prototype.slice.call(root.querySelectorAll("[data-assist-tab]"));
    var panels = Array.prototype.slice.call(root.querySelectorAll("[data-assist-panel]"));
    if (!tabs.length || !panels.length) return;

    function activate(index) {
        tabs.forEach(function (t, i) {
            var on = i === index;
            t.classList.toggle("is-active", on);
            t.setAttribute("aria-selected", on ? "true" : "false");
        });
        panels.forEach(function (p) {
            var on = parseInt(p.getAttribute("data-index") || "-1", 10) === index;
            p.classList.toggle("is-active", on);
        });
    }

    tabs.forEach(function (t, i) {
        t.addEventListener("click", function () { activate(i); });
    });
})();

(function () {
    var root = document.querySelector("[data-nextgen]");
    if (!root) return;
    var tabs = Array.prototype.slice.call(root.querySelectorAll("[data-nextgen-tab]"));
    var panels = Array.prototype.slice.call(root.querySelectorAll("[data-nextgen-panel]"));
    if (!tabs.length || !panels.length) return;

    function activate(index) {
        tabs.forEach(function (tab, i) {
            tab.classList.toggle("is-active", i === index);
        });
        panels.forEach(function (panel) {
            var on = parseInt(panel.getAttribute("data-index") || "-1", 10) === index;
            panel.classList.toggle("is-active", on);
        });
    }

    tabs.forEach(function (tab, i) {
        tab.addEventListener("click", function () { activate(i); });
    });
})();

(function () {
    var root = document.querySelector("[data-afford]");
    if (!root) return;
    var tabs = Array.prototype.slice.call(root.querySelectorAll("[data-afford-tab]"));
    var panels = Array.prototype.slice.call(root.querySelectorAll("[data-afford-panel]"));
    if (!tabs.length || !panels.length) return;

    function activate(index) {
        tabs.forEach(function (tab, i) {
            var on = i === index;
            tab.classList.toggle("is-active", on);
            tab.setAttribute("aria-selected", on ? "true" : "false");
        });
        panels.forEach(function (panel) {
            var on = parseInt(panel.getAttribute("data-index") || "-1", 10) === index;
            panel.classList.toggle("is-active", on);
        });
    }

    tabs.forEach(function (tab, i) {
        tab.addEventListener("click", function () { activate(i); });
    });
})();
