<script>
    (function() {
        'use strict';

        const qs = (sel, ctx = document) => ctx.querySelector(sel);
        const qsa = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

        const topbar = qs('.tsms-topbar');
        const hero = qs('#tsms-hero');
        const toTop = qs('#toTop');
        const docQuick = qs('#docQuick');
        const phTimeEl = qs('#ph-time');
        const yearEl = qs('#year');
        const scrollArrow = qs('#scrollArrow');

        const getNavH = () => {
            const raw = getComputedStyle(document.documentElement).getPropertyValue('--nav-h');
            const val = parseInt(raw, 10);
            return Number.isFinite(val) ? val : 0;
        };

        const scrollToEl = (el) => {
            if (!el) return;
            const navH = getNavH();
            const top = window.scrollY + el.getBoundingClientRect().top - (navH + 12);
            window.scrollTo({
                top,
                behavior: prefersReducedMotion.matches ? 'auto' : 'smooth'
            });
        };

        function initTopbar() {
            if (!topbar || !hero) return;
            let io = null;
            const setupObserver = () => {
                if (io) io.disconnect();
                const navH = getNavH();
                io = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        const inView = entry.isIntersecting;
                        const visible = getComputedStyle(topbar).display !== 'none';
                        if (!visible) return;
                        topbar.classList.toggle('over-hero', inView);
                        topbar.classList.toggle('is-scrolled', !inView && window.scrollY > 6);
                    });
                }, {
                    root: null,
                    threshold: 0,
                    rootMargin: `-${navH}px 0px 0px 0px`
                });
                io.observe(hero);
            };

            setupObserver();
            let rAF = null;
            window.addEventListener('resize', () => {
                if (rAF) cancelAnimationFrame(rAF);
                rAF = requestAnimationFrame(setupObserver);
            });

            topbar.addEventListener('mouseenter', () => {
                const visible = getComputedStyle(topbar).display !== 'none';
                if (visible && topbar.classList.contains('over-hero')) topbar.classList.add('peek');
            });
            topbar.addEventListener('mouseleave', () => topbar.classList.remove('peek'));
        }

        function initScrollUI() {
            let scrollTicking = false;

            const updateScrollProgress = () => {
                if (!toTop) return;
                const doc = document.documentElement;
                const scrollTop = doc.scrollTop || document.body.scrollTop;
                const scrollHeight = doc.scrollHeight - doc.clientHeight;
                const progress = scrollHeight > 0 ? (scrollTop / scrollHeight) : 0;
                const clamped = Math.min(1, Math.max(0, progress));
                toTop.style.setProperty('--progress', `${clamped * 360}deg`);
            };

            const updateScrollUI = () => {
                const scrollY = window.scrollY || window.pageYOffset;
                if (topbar && getComputedStyle(topbar).display !== 'none') {
                    const overHero = topbar.classList.contains('over-hero');
                    if (!overHero) topbar.classList.toggle('is-scrolled', scrollY > 6);
                }
                if (toTop) {
                    toTop.classList.toggle('show', scrollY > 200);
                    updateScrollProgress();
                }
                if (docQuick) {
                    docQuick.classList.toggle('show', scrollY > 200);
                }
                scrollTicking = false;
            };

            window.addEventListener('scroll', () => {
                if (!scrollTicking) {
                    scrollTicking = true;
                    window.requestAnimationFrame(updateScrollUI);
                }
            });

            updateScrollUI();

            if (toTop) {
                toTop.addEventListener('click', (e) => {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: prefersReducedMotion.matches ? 'auto' : 'smooth'
                    });
                });
            }
        }

        function initTime() {
            if (yearEl) yearEl.textContent = (new Date()).getFullYear();
            if (!phTimeEl) return;
            const render = () => {
                const now = new Date();
                const dateStr = new Intl.DateTimeFormat('en-PH', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    timeZone: 'Asia/Manila'
                }).format(now);
                const timeStr = new Intl.DateTimeFormat('en-PH', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true,
                    timeZone: 'Asia/Manila'
                }).format(now);
                phTimeEl.textContent = `${dateStr} • ${timeStr} PHT`;
            };
            render();
            setInterval(render, 1000);
        }

        function initScrollArrow() {
            if (!scrollArrow) return;
            const target = qs('#start');
            const handle = () => scrollToEl(target);
            scrollArrow.addEventListener('click', handle);
            scrollArrow.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    handle();
                }
            });
        }

        function initSectionAnimations() {
            const sectionNodes = qsa('.section-block');
            if (!sectionNodes.length) return;

            const countNodes = qsa('.amount-pill, .total-box .val, .swa-pill');
            const prepareCountNode = (el) => {
                const raw = (el.textContent || '').trim();
                const numeric = Number(raw.replace(/[^0-9.-]/g, ''));
                if (!Number.isFinite(numeric)) return;

                const prefix = (raw.match(/^[^0-9-]*/) || [''])[0];
                const suffix = (raw.match(/[^0-9.]+$/) || [''])[0];
                const decimalMatch = raw.match(/\.(\d+)/);
                const decimals = decimalMatch ? decimalMatch[1].length : 0;

                el.dataset.countupReady = '1';
                el.dataset.countupValue = String(numeric);
                el.dataset.countupDecimals = String(decimals);
                el.dataset.countupPrefix = prefix;
                el.dataset.countupSuffix = suffix;
            };

            const animateCount = (el) => {
                if (!el || el.dataset.countupDone === '1') return;

                const target = Number(el.dataset.countupValue || '0');
                const decimals = Number.parseInt(el.dataset.countupDecimals || '0', 10) || 0;
                const prefix = el.dataset.countupPrefix || '';
                const suffix = el.dataset.countupSuffix || '';
                const duration = target >= 10000 ? 1200 : 850;
                const start = performance.now();
                const easeOut = (t) => 1 - Math.pow(1 - t, 3);

                const tick = (now) => {
                    const progress = Math.min(1, (now - start) / duration);
                    const eased = easeOut(progress);
                    const value = target * eased;
                    const formatted = value.toLocaleString('en-PH', {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals
                    });
                    el.textContent = `${prefix}${formatted}${suffix}`;

                    if (progress < 1) {
                        window.requestAnimationFrame(tick);
                    } else {
                        el.dataset.countupDone = '1';
                    }
                };

                window.requestAnimationFrame(tick);
            };

            countNodes.forEach(prepareCountNode);

            if (prefersReducedMotion.matches || !('IntersectionObserver' in window)) {
                qsa('[data-countup-ready="1"]').forEach((el) => {
                    el.dataset.countupDone = '1';
                });
                return;
            }

            document.body.classList.add('motion-ready');

            const revealSelector =
                '.section-head, .section-body, .divider, .hero-img, .panel, .tile, .documents-box, .dl-card, .gallery-item, .documents-bullets, .documents-steps, .merit-totals, .swa-eq, .req-callout, .req-ids, .req-footer-logos';

            sectionNodes.forEach((section) => {
                const uniqueNodes = [...new Set(qsa(revealSelector, section).filter(Boolean))];
                if (!uniqueNodes.length) uniqueNodes.push(section);

                uniqueNodes.forEach((node, index) => {
                    node.classList.add('js-reveal');
                    node.style.setProperty('--reveal-delay', `${Math.min(index * 60, 420)}ms`);
                });

                section.__revealNodes = uniqueNodes;
            });

            const sectionObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;

                    const section = entry.target;
                    const revealNodes = section.__revealNodes || [];
                    revealNodes.forEach((node) => node.classList.add('is-revealed'));

                    qsa('[data-countup-ready="1"]:not([data-countup-done])', section)
                        .forEach(animateCount);

                    observer.unobserve(section);
                });
            }, {
                root: null,
                threshold: 0.2,
                rootMargin: '0px 0px -10% 0px'
            });

            sectionNodes.forEach((section) => sectionObserver.observe(section));
        }

        function initSearch() {
            const navSearch = qs('#navSearch');
            const searchToggle = qs('#searchToggle');
            const searchForm = qs('#searchForm');
            const searchInput = qs('#searchInput');
            const searchMeta = qs('#searchMeta');
            const hitCount = qs('#hitCount');
            const nextHitBtn = qs('#nextHit');
            const prevHitBtn = qs('#prevHit');
            const clearBtn = qs('#clearSearch');

            if (!navSearch || !searchInput || !window.Mark) return;

            const mark = new Mark(document.body);
            let hits = [];
            let idx = -1;

            const updateCounter = () => {
                const total = hits.length;
                const current = total > 0 ? (idx + 1) : 0;
                if (hitCount) hitCount.textContent = `${current}/${total}`;
            };

            const clearHighlights = () => {
                mark.unmark({
                    element: 'mark',
                    className: 'search-hit search-current',
                    done: () => {
                        hits = [];
                        idx = -1;
                        if (hitCount) hitCount.textContent = '0/0';
                        if (searchMeta) searchMeta.style.display = '';
                        navSearch.classList.remove('has-results');
                    }
                });
            };

            const scrollToHit = (i) => {
                if (!hits[i]) return;
                hits.forEach(h => h.classList.remove('search-current'));
                const el = hits[i];
                el.classList.add('search-current');
                scrollToEl(el);
                updateCounter();
            };

            const performSearch = (q) => {
                if (!q || !q.trim()) {
                    clearHighlights();
                    return;
                }
                clearHighlights();
                mark.mark(q, {
                    separateWordSearch: true,
                    caseSensitive: false,
                    diacritics: true,
                    acrossElements: true,
                    exclude: ['nav', '.tsms-topbar', '.tsms-search', '#searchForm', '#searchMeta',
                        'script', 'style',
                        'footer', '#toTop', '#docQuick', '#reqQuickModal', '#lightboxModal'
                    ],
                    element: 'mark',
                    className: 'search-hit',
                    done: () => {
                        hits = qsa('mark.search-hit');
                        if (hits.length > 0) {
                            navSearch.classList.add('has-results');
                            idx = 0;
                            scrollToHit(idx);
                        } else {
                            navSearch.classList.remove('has-results');
                            idx = -1;
                            updateCounter();
                        }
                    }
                });
            };

            const openSearch = () => {
                navSearch.classList.add('open');
                searchInput.focus();
            };

            const closeSearch = () => {
                navSearch.classList.remove('open', 'has-results');
                searchInput.value = '';
                clearHighlights();
            };

            if (searchToggle) {
                searchToggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    navSearch.classList.contains('open') ? closeSearch() : openSearch();
                });
            }

            if (searchForm) {
                searchForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    performSearch(searchInput.value);
                });
            }

            let typeTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(typeTimer);
                typeTimer = setTimeout(() => performSearch(searchInput.value), 220);
            });

            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeSearch();
                    return;
                }
                if (e.key === 'Enter' && hits.length) {
                    e.preventDefault();
                    idx = (idx + 1) % hits.length;
                    scrollToHit(idx);
                }
            });

            if (nextHitBtn) {
                nextHitBtn.addEventListener('click', () => {
                    if (!hits.length) return;
                    idx = (idx + 1) % hits.length;
                    scrollToHit(idx);
                });
            }

            if (prevHitBtn) {
                prevHitBtn.addEventListener('click', () => {
                    if (!hits.length) return;
                    idx = (idx - 1 + hits.length) % hits.length;
                    scrollToHit(idx);
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', () => closeSearch());
            }

            document.addEventListener('click', (e) => {
                if (!navSearch.contains(e.target) && navSearch.classList.contains('open')) {
                    if (!searchInput.value.trim()) {
                        closeSearch();
                    } else {
                        navSearch.classList.remove('open');
                    }
                }
            });
        }

        function initLightbox() {
            const galleryButtons = qsa('.gallery-item');
            const lbModalEl = qs('#lightboxModal');
            const lbImg = qs('#lbImg');
            const lbCaption = qs('#lbCaption');
            const lbCounter = qs('#lbCounter');
            const lbPrev = qs('#lbPrev');
            const lbNext = qs('#lbNext');

            if (!galleryButtons.length || !lbModalEl || !lbImg || !lbCaption || !lbCounter || !lbPrev || !lbNext ||
                !window.bootstrap) return;

            const galleryData = {};
            galleryButtons.forEach((btn) => {
                const group = btn.dataset.group;
                const full = btn.dataset.full || '';
                const cap = btn.dataset.caption || btn.getAttribute('aria-label') || '';
                if (!group) return;
                if (!galleryData[group]) galleryData[group] = [];
                const position = galleryData[group].push({
                    src: full,
                    caption: cap
                }) - 1;
                btn.setAttribute('aria-label', cap ? `Open image: ${cap}` : 'Open image');
                btn.addEventListener('click', () => openLightbox(group, position));
            });

            const lbModal = new bootstrap.Modal(lbModalEl, {
                keyboard: true
            });
            let currentGroup = null;
            let currentIndex = 0;

            const renderLightbox = () => {
                const items = galleryData[currentGroup] || [];
                if (!items.length) return;
                const item = items[currentIndex];
                lbImg.src = item.src;
                lbImg.alt = item.caption || '';
                lbCaption.textContent = item.caption || '';
                lbCounter.textContent = `${currentIndex + 1}/${items.length}`;
            };

            const openLightbox = (group, index) => {
                currentGroup = group;
                currentIndex = index;
                renderLightbox();
                lbModal.show();
            };

            const nextImg = () => {
                const items = galleryData[currentGroup] || [];
                if (!items.length) return;
                currentIndex = (currentIndex + 1) % items.length;
                renderLightbox();
            };

            const prevImg = () => {
                const items = galleryData[currentGroup] || [];
                if (!items.length) return;
                currentIndex = (currentIndex - 1 + items.length) % items.length;
                renderLightbox();
            };

            lbNext.addEventListener('click', nextImg);
            lbPrev.addEventListener('click', prevImg);

            const keyHandler = (e) => {
                if (e.key === 'ArrowRight') nextImg();
                if (e.key === 'ArrowLeft') prevImg();
            };

            lbModalEl.addEventListener('shown.bs.modal', () => document.addEventListener('keydown', keyHandler));
            lbModalEl.addEventListener('hidden.bs.modal', () => document.removeEventListener('keydown',
                keyHandler));
        }

        initTopbar();
        initScrollUI();
        initTime();
        initScrollArrow();
        initSectionAnimations();
        initSearch();
        initLightbox();
    })();
</script>
