(function () {
    function qs(sel, root) {
        return (root || document).querySelector(sel);
    }
    function qsa(sel, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(sel));
    }

    /* Tabs: data-xr-tab / data-xr-panel share data-xr-tab value as group name */
    function initTabs() {
        qsa("[data-xr-tab]").forEach(function (btn) {
            btn.addEventListener("click", function () {
                var group = btn.getAttribute("data-xr-tab");
                var idx = btn.getAttribute("data-index");
                var scope = btn.closest(".xr-block") || btn.closest("main") || document;
                qsa('[data-xr-tab="' + group + '"]', scope).forEach(function (b) {
                    b.classList.toggle("is-active", b === btn);
                    b.setAttribute("aria-selected", b === btn ? "true" : "false");
                });
                qsa('[data-xr-panel="' + group + '"]', scope).forEach(function (p) {
                    p.classList.toggle("is-active", p.getAttribute("data-index") === idx);
                });
                document.dispatchEvent(new CustomEvent("xr-tabs-changed"));
            });
        });
    }

    function clearInnerCarousels() {
        qsa("[data-inner-carousel]").forEach(function (root) {
            if (root._xrIct) {
                clearTimeout(root._xrIct);
                root._xrIct = null;
            }
        });
    }

    function initInnerCarousels() {
        clearInnerCarousels();
        qsa("[data-inner-carousel]").forEach(function (root) {
            var panel = root.closest("[data-xr-panel]");
            if (panel && !panel.classList.contains("is-active")) {
                return;
            }
            var slides = qsa("[data-inner-slide]", root);
            if (slides.length < 2) {
                return;
            }
            var cur = 0;

            function show(i) {
                cur = (i + slides.length) % slides.length;
                slides.forEach(function (s, j) {
                    s.classList.toggle("is-active", j === cur);
                    var v = s.querySelector("video");
                    if (v) {
                        if (j === cur) {
                            v.play().catch(function () {});
                        } else {
                            v.pause();
                        }
                    }
                });
            }

            function scheduleNext() {
                var active = slides[cur];
                var ms = parseInt(active.getAttribute("data-interval"), 10) || 5000;
                root._xrIct = setTimeout(function () {
                    show(cur + 1);
                    scheduleNext();
                }, ms);
            }

            show(0);
            scheduleNext();
        });
    }

    function initBeforeAfter() {
        qsa("[data-before-after]").forEach(function (root) {
            var clip = qs("[data-ba-clip]", root);
            var range = qs("[data-ba-range]", root);
            var handle = qs("[data-ba-handle]", root);
            if (!clip || !range) return;

            function setPct(pct) {
                clip.style.clipPath = "inset(0 " + (100 - pct) + "% 0 0)";
                range.value = String(pct);
                if (handle) {
                    handle.style.left = pct + "%";
                    handle.setAttribute("aria-valuenow", String(pct));
                }
            }

            setPct(50);
            range.addEventListener("input", function () {
                setPct(parseInt(range.value, 10));
            });
            if (handle) {
                var drag = false;
                handle.addEventListener("mousedown", function () {
                    drag = true;
                });
                document.addEventListener("mouseup", function () {
                    drag = false;
                });
                root.querySelector(".xr-ba__stage").addEventListener("mousemove", function (e) {
                    if (!drag) return;
                    var r = root.querySelector(".xr-ba__stage").getBoundingClientRect();
                    var x = ((e.clientX - r.left) / r.width) * 100;
                    setPct(Math.max(0, Math.min(100, x)));
                });
            }
        });
    }

    function initProgressBars() {
        if (!("IntersectionObserver" in window)) {
            qsa("[data-pbar-fill]").forEach(function (el) {
                var t = parseInt(el.getAttribute("data-target"), 10) || 0;
                el.style.width = t + "%";
            });
            return;
        }
        var io = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (en) {
                    if (!en.isIntersecting) return;
                    var el = en.target;
                    var t = parseInt(el.getAttribute("data-target"), 10) || 0;
                    el.style.width = t + "%";
                    io.unobserve(el);
                });
            },
            { threshold: 0.2 }
        );
        qsa("[data-pbar-fill]").forEach(function (el) {
            el.style.width = "0%";
            io.observe(el);
        });
    }

    function initUniformPageReveal(selector) {
        var main = qs(selector);
        if (!main) return;
        qsa("h1, h2, h3, h4, p, li, figcaption", main).forEach(function (el) {
            if (el.closest("button, .xr-modal, .nav-toggle, .site-nav__cta, .xr-marquee__card")) return;
            if (el.closest(".xr-planks--stagger .xr-planks__item")) return;
            if (el.classList.contains("xr-reveal") || el.classList.contains("xr-burst-text")) return;
            el.classList.add("xr-reveal");
        });
    }

    function initCarousels() {
        qsa("[data-carousel]").forEach(function (root) {
            var slides = qsa("[data-carousel-slide]", root);
            var dots = qsa("[data-carousel-dot]", root);
            if (!slides.length) return;
            var interval = parseInt(root.getAttribute("data-interval"), 10) || 5000;
            var cur = 0;

            function show(i) {
                cur = (i + slides.length) % slides.length;
                slides.forEach(function (s, j) {
                    s.classList.toggle("is-active", j === cur);
                });
                dots.forEach(function (d, j) {
                    d.classList.toggle("is-active", j === cur);
                });
            }

            dots.forEach(function (d, j) {
                d.addEventListener("click", function () {
                    show(j);
                });
            });

            if (slides.length > 1) {
                setInterval(function () {
                    show(cur + 1);
                }, interval);
            }
        });
    }

    function initVideoFreeze() {
        qsa("[data-video-freeze]").forEach(function (v) {
            v.addEventListener("ended", function () {
                try {
                    var t = v.duration - 0.05;
                    if (t > 0) v.currentTime = t;
                } catch (e) {}
                v.pause();
            });
            v.play().catch(function () {});
        });
    }

    function initYoutubeLoad() {
        qsa("[data-youtube-load]").forEach(function (btn) {
            btn.addEventListener("click", function () {
                var id = btn.getAttribute("data-youtube-load");
                if (!id) return;
                var stage = btn.closest(".xr-tabs-media__stage");
                if (!stage) return;
                var mask = qs(".xr-yt-mask", stage);
                var frame = qs("[data-youtube-frame]", stage);
                if (!frame) return;
                frame.innerHTML =
                    '<iframe src="https://www.youtube.com/embed/' +
                    id +
                    '?autoplay=1&rel=0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                frame.hidden = false;
                if (mask) mask.style.display = "none";
            });
        });
    }

    function starfield(canvas, opts) {
        if (!canvas || !canvas.getContext) return;
        var ctx = canvas.getContext("2d");
        var w, h, stars, raf;
        var dense = (opts && opts.dense) || 1;

        function resize() {
            var rect = canvas.getBoundingClientRect();
            w = canvas.width = Math.max(400, Math.floor(rect.width * (window.devicePixelRatio || 1)));
            h = canvas.height = Math.max(300, Math.floor(rect.height * (window.devicePixelRatio || 1)));
            stars = [];
            var n = Math.floor((w * h) / (8000 / dense));
            for (var i = 0; i < n; i++) {
                stars.push({
                    x: Math.random() * w,
                    y: Math.random() * h,
                    r: Math.random() * 1.2 + 0.2,
                    tw: Math.random() * Math.PI * 2,
                });
            }
        }

        function tick() {
            ctx.clearRect(0, 0, w, h);
            stars.forEach(function (s) {
                s.tw += 0.02 + Math.random() * 0.01;
                var a = 0.35 + Math.sin(s.tw) * 0.35;
                ctx.fillStyle = "rgba(200,220,255," + a + ")";
                ctx.beginPath();
                ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
                ctx.fill();
            });
            raf = requestAnimationFrame(tick);
        }

        resize();
        tick();
        window.addEventListener("resize", function () {
            cancelAnimationFrame(raf);
            resize();
            tick();
        });
    }

    function initStarfields() {
        qsa("canvas[data-starfield]").forEach(function (c) {
            starfield(c, { dense: 1.2 });
        });
        qsa("canvas[data-starfield-cta]").forEach(function (c) {
            starfield(c, { dense: 0.8 });
        });
    }

    function initTwinkle() {
        qsa("canvas[data-twinkle-overlay]").forEach(function (canvas) {
            if (!canvas.getContext) return;
            var ctx = canvas.getContext("2d");
            var w, h, dots;

            function resize() {
                var rect = canvas.parentElement.getBoundingClientRect();
                w = canvas.width = Math.floor(rect.width * (window.devicePixelRatio || 1));
                h = canvas.height = Math.floor(rect.height * (window.devicePixelRatio || 1));
                dots = [];
                for (var i = 0; i < 80; i++) {
                    dots.push({
                        x: Math.random() * w,
                        y: Math.random() * h,
                        ph: Math.random() * Math.PI * 2,
                    });
                }
            }

            function tick() {
                ctx.clearRect(0, 0, w, h);
                var t = Date.now() / 400;
                dots.forEach(function (d) {
                    var a = 0.2 + Math.sin(t + d.ph) * 0.25;
                    ctx.fillStyle = "rgba(255,255,255," + a + ")";
                    ctx.fillRect(d.x, d.y, 2, 2);
                });
                requestAnimationFrame(tick);
            }
            resize();
            tick();
            window.addEventListener("resize", resize);
        });
    }

    function initFloatPlank() {
        qsa("[data-float-plank]").forEach(function (rail) {
            var card = qs(".xr-float-plank__card", rail);
            if (!card) return;
            var t0 = Date.now();
            function loop() {
                var t = (Date.now() - t0) / 1000;
                var y = Math.sin(t * 0.7) * 14;
                card.style.transform = "translateY(" + y + "px)";
                requestAnimationFrame(loop);
            }
            loop();
        });
    }

    function initReviews() {
        qsa("[data-review-json]").forEach(function (script) {
            var blockId = script.getAttribute("data-review-for");
            var modal = blockId ? document.getElementById("xr-review-" + blockId) : qs("[data-review-modal]");
            if (!modal) return;
            var items;
            try {
                items = JSON.parse(script.textContent || "[]");
            } catch (e) {
                items = [];
            }
            var body = qs("[data-review-body]", modal);
            var cur = 0;

            function render() {
                if (!items[cur] || !body) return;
                var it = items[cur];
                body.innerHTML =
                    "<p><strong>" +
                    escapeHtml(it.author || "") +
                    "</strong> — " +
                    escapeHtml(it.role || "") +
                    "</p><p>" +
                    escapeHtml(it.quote || "") +
                    "</p>";
            }

            function escapeHtml(s) {
                var d = document.createElement("div");
                d.textContent = s;
                return d.innerHTML;
            }

            var root = script.closest(".xr-block") || document;
            qsa("[data-review-open]", root).forEach(function (btn) {
                btn.addEventListener("click", function () {
                    cur = parseInt(btn.getAttribute("data-review-open"), 10) || 0;
                    render();
                    modal.hidden = false;
                });
            });
            qsa("[data-review-close]", modal).forEach(function (el) {
                el.addEventListener("click", function () {
                    modal.hidden = true;
                });
            });
            var prev = qs("[data-review-prev]", modal);
            var next = qs("[data-review-next]", modal);
            if (prev && items.length) {
                prev.addEventListener("click", function () {
                    cur = (cur - 1 + items.length) % items.length;
                    render();
                });
            }
            if (next && items.length) {
                next.addEventListener("click", function () {
                    cur = (cur + 1) % items.length;
                    render();
                });
            }
        });
    }

    function initRotatingHeadlines() {
        qsa("[data-rotating-head]").forEach(function (root) {
            var script = qs("[data-rotating-words]", root);
            var target = qs("[data-rotating-target]", root);
            if (!script || !target) return;
            var words;
            try {
                words = JSON.parse(script.textContent || "[]");
            } catch (e) {
                words = [];
            }
            if (!words.length) return;
            var interval = parseInt(root.getAttribute("data-interval"), 10) || 2800;
            var i = 0;
            setInterval(function () {
                i = (i + 1) % words.length;
                target.style.opacity = "0";
                setTimeout(function () {
                    target.textContent = words[i];
                    target.style.opacity = "1";
                }, 200);
            }, interval);
        });
    }

    function initReveal() {
        var els = qsa(".xr-reveal, .xr-burst-text");
        if (!els.length || !("IntersectionObserver" in window)) {
            els.forEach(function (el) {
                el.classList.add("is-inview");
            });
            return;
        }
        var io = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (en) {
                    if (en.isIntersecting) {
                        en.target.classList.add("is-inview");
                    }
                });
            },
            { rootMargin: "0px 0px -8% 0px", threshold: 0.12 }
        );
        els.forEach(function (el) {
            io.observe(el);
        });
    }

    function initHubspot() {
        var cfgEl = document.getElementById("xr-hubspot-config");
        var cfg = {};
        if (cfgEl) {
            try {
                cfg = JSON.parse(cfgEl.textContent || "{}");
            } catch (e) {}
        }
        var modal = document.querySelector("[data-hubspot-modal]");
        var iframe = modal ? modal.querySelector("[data-hubspot-iframe]") : null;
        var fb = modal ? modal.querySelector("[data-hubspot-fallback]") : null;
        var title = modal ? modal.querySelector(".xr-hubspot-modal__title") : null;

        function openUrl(url, label) {
            if (!modal || !iframe) return;
            if (url && url.indexOf("http") === 0) {
                iframe.hidden = false;
                if (fb) fb.hidden = true;
                iframe.src = url;
            } else {
                iframe.hidden = true;
                if (fb) fb.hidden = false;
            }
            if (title) title.textContent = label || "Form";
            modal.hidden = false;
        }

        qsa(".js-hubspot-trigger").forEach(function (a) {
            a.addEventListener("click", function (e) {
                var kind = a.getAttribute("data-hubspot-kind");
                var url = kind === "whitepaper" ? cfg.whitepaper_url : cfg.demo_url;
                if (!url || url.indexOf("http") !== 0) {
                    e.preventDefault();
                    openUrl("", kind === "whitepaper" ? "White Paper" : "Request Demo");
                    return;
                }
                e.preventDefault();
                openUrl(url, kind === "whitepaper" ? "White Paper" : "Request Demo");
            });
        });

        if (modal) {
            qsa("[data-hubspot-close]", modal).forEach(function (el) {
                el.addEventListener("click", function () {
                    modal.hidden = true;
                    if (iframe) iframe.src = "about:blank";
                });
            });
        }
    }

    function initMarqueePause() {
        qsa(".xr-marquee").forEach(function (m) {
            var tr = qs(".xr-marquee__track", m);
            if (!tr) return;
            m.addEventListener("mouseenter", function () {
                tr.style.animationPlayState = "paused";
            });
            m.addEventListener("mouseleave", function () {
                tr.style.animationPlayState = "running";
            });
        });
        qsa(".xr-cards4").forEach(function (m) {
            var tr = qs(".xr-cards4__track", m);
            if (!tr) return;
            m.addEventListener("mouseenter", function () {
                tr.style.animationPlayState = "paused";
            });
            m.addEventListener("mouseleave", function () {
                tr.style.animationPlayState = "running";
            });
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        initTabs();
        initCarousels();
        initVideoFreeze();
        initYoutubeLoad();
        initStarfields();
        initTwinkle();
        initFloatPlank();
        initReviews();
        initRotatingHeadlines();
        initBeforeAfter();
        initProgressBars();
        initUniformPageReveal(".xr-page-institutions");
        initUniformPageReveal(".xr-page-blog");
        initUniformPageReveal(".xr-page-partners");
        initReveal();
        initInnerCarousels();
        document.addEventListener("xr-tabs-changed", function () {
            initInnerCarousels();
        });
        initHubspot();
        initMarqueePause();
    });
})();
