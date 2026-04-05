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
                qsa("[data-oculus-slot]", scope).forEach(function (slot) {
                    var slotIdx = slot.getAttribute("data-oculus-slot");
                    var isActive = slotIdx === idx;
                    slot.classList.toggle("is-active", isActive);
                    if (!isActive) {
                        var v = qs("video.xr-oculus-video", slot);
                        if (v) {
                            v.pause();
                            try {
                                v.currentTime = 0;
                            } catch (e) {}
                        }
                        var oLay = qs("[data-oculus-overlay]", slot);
                        if (oLay) oLay.hidden = false;
                        var ytFrame = qs("[data-youtube-frame]", slot);
                        if (ytFrame && !ytFrame.hidden) {
                            ytFrame.innerHTML = "";
                            ytFrame.hidden = true;
                            var mask = qs(".xr-yt-mask", slot);
                            if (mask) mask.style.display = "";
                        }
                    }
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

    function initOculusMp4Play() {
        qsa("[data-oculus-play]").forEach(function (btn) {
            btn.addEventListener("click", function () {
                var slot = btn.closest(".xr-tabs-media__oculus-slot");
                var screen = slot ? qs(".xr-tabs-media__vision-screen", slot) : null;
                var v = screen ? qs("video", screen) : null;
                var o = btn.closest("[data-oculus-overlay]");
                if (v) {
                    v.play().catch(function () {});
                }
                if (o) {
                    o.hidden = true;
                }
            });
        });
    }

    function initYoutubeLoad() {
        qsa("[data-youtube-load]").forEach(function (btn) {
            btn.addEventListener("click", function () {
                var id = btn.getAttribute("data-youtube-load");
                if (!id) return;
                var stage =
                    btn.closest(".xr-tabs-media__vision-screen") ||
                    btn.closest(".xr-headset-fo-body") ||
                    btn.closest(".xr-product-tabs__stage") ||
                    btn.closest(".xr-pt-slider__video-stage") ||
                    btn.closest(".xr-tabs-media__stage");
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

    function initHologramStories() {
        qsa("[data-stories-root]").forEach(function (root) {
            var jsonEl = qs("[data-stories-json]", root);
            if (!jsonEl) return;
            var stories;
            try { stories = JSON.parse(jsonEl.textContent || "[]"); } catch (e) { return; }
            if (!stories.length) return;

            var carousel  = qs("[data-stories-carousel]", root);
            var track     = qs("[data-stories-track]", root);
            var modal     = qs("[data-stories-modal]", root);
            var mIcon     = qs("[data-m-icon]", root);
            var mTag      = qs("[data-m-tag]", root);
            var mTitle    = qs("[data-m-title]", root);
            var mBody     = qs("[data-m-body]", root);
            var mFooter   = qs("[data-m-footer]", root);
            var offset = 0, openIdx = -1, autoTimer = null;
            var CLONE = 3;

            function buildCards() {
                track.innerHTML = "";
                stories.concat(stories.slice(0, CLONE)).forEach(function (s, i) {
                    var ri = i % stories.length;
                    var card = document.createElement("div");
                    card.className = "xr-stories__card";
                    var tagsHtml = Array.isArray(s.tags) ? s.tags.join("<br>") : "";
                    card.innerHTML =
                        '<div class="xr-stories__card-icon">' + (s.icon || "") + "</div>" +
                        '<div class="xr-stories__card-text">' + (s.summary || "") + "</div>" +
                        '<button type="button" class="xr-stories__readmore" data-ridx="' + ri + '">Read More</button>' +
                        '<div class="xr-stories__card-tags">' + tagsHtml + "</div>" +
                        '<div class="xr-stories__card-stars" aria-hidden="true">★★★★★</div>';
                    track.appendChild(card);
                });
                qsa("[data-ridx]", track).forEach(function (btn) {
                    btn.addEventListener("click", function () { openModal(parseInt(btn.getAttribute("data-ridx"), 10)); });
                });
            }

            function cardW() {
                var c = qs(".xr-stories__card", track);
                return c ? c.offsetWidth + 16 : 260;
            }

            function moveTo(idx, animate) {
                track.style.transition = animate ? "transform 0.65s cubic-bezier(0.4,0,0.2,1)" : "none";
                track.style.transform  = "translateX(-" + (idx * cardW()) + "px)";
            }

            function startAuto() {
                clearInterval(autoTimer);
                autoTimer = setInterval(function () {
                    offset++;
                    if (offset >= stories.length) {
                        moveTo(offset, true);
                        setTimeout(function () { offset = 0; moveTo(0, false); }, 680);
                    } else {
                        moveTo(offset, true);
                    }
                }, 3200);
            }

            function openModal(idx) {
                openIdx = idx;
                fillModal(idx);
                if (carousel) carousel.hidden = true;
                if (modal)    modal.hidden    = false;
                clearInterval(autoTimer);
            }

            function closeModal() {
                if (modal)    modal.hidden    = true;
                if (carousel) carousel.hidden = false;
                openIdx = -1;
                startAuto();
            }

            function fillModal(idx) {
                var s = stories[idx];
                if (!s) return;
                if (mIcon)   mIcon.innerHTML = s.icon || "";
                if (mTag)    mTag.innerHTML    = Array.isArray(s.tags) ? s.tags.join("<br>") : "";
                if (mTitle)  mTitle.textContent = s.title || "";
                if (mBody) {
                    mBody.innerHTML = "";
                    var body = Array.isArray(s.body) ? s.body : [s.body || ""];
                    body.forEach(function (para) {
                        if (!para) return;
                        var p = document.createElement("p");
                        p.textContent = para;
                        mBody.appendChild(p);
                    });
                }
                if (mFooter) mFooter.textContent = s.footer || "";
            }

            var closeBtn = qs("[data-stories-close]", root);
            var prevBtn  = qs("[data-stories-prev]",  root);
            var nextBtn  = qs("[data-stories-next]",  root);
            if (closeBtn) closeBtn.addEventListener("click", closeModal);
            if (prevBtn)  prevBtn.addEventListener("click",  function () { openIdx = (openIdx - 1 + stories.length) % stories.length; fillModal(openIdx); });
            if (nextBtn)  nextBtn.addEventListener("click",  function () { openIdx = (openIdx + 1) % stories.length; fillModal(openIdx); });

            root.addEventListener("mouseenter", function () { if (openIdx === -1) clearInterval(autoTimer); });
            root.addEventListener("mouseleave", function () { if (openIdx === -1) startAuto(); });

            buildCards();
            setTimeout(function () { moveTo(0, false); startAuto(); }, 60);
        });
    }

    function initDetailSubNav() {
        qsa("[data-sub-items]").forEach(function (wrap) {
            var items;
            try { items = JSON.parse(wrap.getAttribute("data-sub-items") || "[]"); } catch (e) { items = []; }
            var btns    = qsa("[data-sub-idx]", wrap);
            var content = qs(".xr-dtabs__subcontent", wrap);
            if (!content || btns.length === 0) return;
            btns.forEach(function (btn) {
                btn.addEventListener("click", function () {
                    btns.forEach(function (b) {
                        b.classList.remove("is-active");
                        b.setAttribute("aria-selected", "false");
                    });
                    btn.classList.add("is-active");
                    btn.setAttribute("aria-selected", "true");
                    var idx = parseInt(btn.getAttribute("data-sub-idx"), 10);
                    if (items[idx] !== undefined) {
                        content.style.opacity = "0";
                        setTimeout(function () {
                            content.textContent = items[idx];
                            content.style.opacity = "1";
                        }, 130);
                    }
                });
            });
        });
    }

    function initProductSlider() {
        qsa("[data-pt-slider]").forEach(function (root) {
            var track    = qs("[data-pt-track]", root);
            var dots     = qsa("[data-pt-dot]", root);
            var navLinks = qsa("[data-pt-nav]", root);
            var prevBtn  = qs("[data-pt-prev]", root);
            var nextBtn  = qs("[data-pt-next]", root);
            var total    = Math.max(dots.length, 3);
            var current  = 0;
            var timer;
            var ytPlaying = false; // true while YouTube iframe is open in this slider

            function stopYtInSlider() {
                // Remove any active YouTube iframes inside this slider and restore masks
                qsa("[data-youtube-frame]", root).forEach(function (frame) {
                    if (!frame.hidden) {
                        frame.innerHTML = "";
                        frame.hidden = true;
                        var mask = frame.closest(".xr-pt-slider__video-stage")
                                && qs(".xr-yt-mask", frame.closest(".xr-pt-slider__video-stage"));
                        if (mask) mask.style.display = "";
                    }
                });
                ytPlaying = false;
            }

            function goTo(idx) {
                stopYtInSlider();
                current = (idx + total) % total;
                if (track) track.style.transform = "translateX(-" + (current * 100) + "%)";
                dots.forEach(function (d, i) { d.classList.toggle("is-active", i === current); });
                navLinks.forEach(function (n) {
                    n.classList.toggle("is-active", parseInt(n.getAttribute("data-pt-nav"), 10) === current);
                });
            }

            function resetTimer() {
                clearInterval(timer);
                if (!ytPlaying) {
                    timer = setInterval(function () { goTo(current + 1); }, 5000);
                }
            }

            // Intercept YouTube play button clicks inside this slider
            root.addEventListener("click", function (e) {
                var btn = e.target.closest("[data-youtube-load]");
                if (btn && root.contains(btn)) {
                    ytPlaying = true;
                    clearInterval(timer);
                }
            }, true);

            // Also pause on native <video> play events (for future MP4 slides)
            root.addEventListener("play", function () {
                ytPlaying = true;
                clearInterval(timer);
            }, true);
            root.addEventListener("pause", function () {
                ytPlaying = false;
                resetTimer();
            }, true);
            root.addEventListener("ended", function () {
                ytPlaying = false;
                resetTimer();
            }, true);

            if (prevBtn) prevBtn.addEventListener("click", function () { goTo(current - 1); resetTimer(); });
            if (nextBtn) nextBtn.addEventListener("click", function () { goTo(current + 1); resetTimer(); });
            dots.forEach(function (d) {
                d.addEventListener("click", function () { goTo(parseInt(d.getAttribute("data-pt-dot"), 10)); resetTimer(); });
            });
            navLinks.forEach(function (n) {
                n.addEventListener("click", function () { goTo(parseInt(n.getAttribute("data-pt-nav"), 10)); resetTimer(); });
            });

            root.addEventListener("mouseenter", function () { clearInterval(timer); });
            root.addEventListener("mouseleave", function () { if (!ytPlaying) resetTimer(); });

            resetTimer();
        });
    }

    function initShowcaseTabs() {
        qsa("[data-showcase-tabs]").forEach(function (root) {
            var tabs       = qsa("[data-xr-tab='product-sc']", root);
            var cards      = qsa("[data-sc-card]", root);
            var cardsWrap  = qs("[data-sc-cards]", root);
            var featPanels = qsa("[data-sc-feature-panel]", root);
            var n          = cards.length;
            if (tabs.length === 0) return;

            function activate(i) {
                var isFeature = tabs[i] && tabs[i].getAttribute("data-sc-feature") === "1";

                /* Update tab active state */
                tabs.forEach(function (t, ti) {
                    t.classList.toggle("is-active", ti === i);
                    t.setAttribute("aria-selected", ti === i ? "true" : "false");
                });

                if (isFeature) {
                    /* Hide photo-card pair, show the matching feature panel */
                    if (cardsWrap) cardsWrap.hidden = true;
                    featPanels.forEach(function (fp) {
                        var idx = parseInt(fp.getAttribute("data-sc-feature-panel"), 10);
                        fp.hidden = idx !== i;
                    });
                } else {
                    /* Show photo-card pair, hide all feature panels */
                    if (cardsWrap) cardsWrap.hidden = false;
                    featPanels.forEach(function (fp) { fp.hidden = true; });

                    /* Pair visibility: tab 0 → [0,1]; else → [i-1, i] */
                    var left  = i === 0 ? 0 : i - 1;
                    var right = i === 0 ? Math.min(n - 1, 1) : Math.min(n - 1, i);
                    cards.forEach(function (c) {
                        var ci = parseInt(c.getAttribute("data-sc-card"), 10);
                        var vis = ci === left || ci === right;
                        c.classList.toggle("is-visible", vis);
                        c.classList.toggle("is-active", ci === i);
                    });
                }
            }

            tabs.forEach(function (t, i) {
                t.addEventListener("click", function () { activate(i); });
            });
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        initTabs();
        initDetailSubNav();
        initHologramStories();
        initProductSlider();
        initShowcaseTabs();
        initCarousels();
        initVideoFreeze();
        initYoutubeLoad();
        initOculusMp4Play();
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
