/**
 * RIMA Academy — Single Course JS (2026)
 * rima-single-course.js
 */
(function () {
    'use strict';

    /* ── DOM refs ────────────────────────────────────── */
    const mobileCta    = document.querySelector('.rsc-mobile-cta-bar');
    const purchaseCard = document.querySelector('.rsc-purchase-card');
    const anchorLinks  = document.querySelectorAll('.rsc-anchor-link');
    const bioEl        = document.querySelector('.rsc-instructor-bio');
    const bioToggle    = document.querySelector('.rsc-bio-toggle');
    const expandAllBtn = document.querySelector('.rsc-expand-all');

    /* ── Sticky mobile CTA bar ───────────────────────── */
    function initMobileCta() {
        if (!mobileCta) return;

        function checkScroll() {
            const scrollY = window.scrollY;
            // Show bar after 300px scroll
            if (scrollY > 300) {
                // Hide if purchase card is in view (only on desktop where card is hidden anyway)
                mobileCta.classList.add('rsc-bar-visible');
            } else {
                mobileCta.classList.remove('rsc-bar-visible');
            }

            // Hide when near footer
            const footer = document.querySelector('footer, .eltdf-footer-top-holder');
            if (footer) {
                const footerRect = footer.getBoundingClientRect();
                if (footerRect.top < window.innerHeight) {
                    mobileCta.classList.remove('rsc-bar-visible');
                }
            }
        }

        window.addEventListener('scroll', checkScroll, { passive: true });
        checkScroll();
    }

    /* ── Sticky sidebar IntersectionObserver ─────────── */
    function initStickySidebar() {
        const sidebar = document.querySelector('.rsc-sticky-sidebar');
        if (!sidebar) return;

        const footer = document.querySelector('footer, .eltdf-footer-top-holder');
        if (!footer) return;

        const obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    sidebar.style.position = 'relative';
                    sidebar.style.top = 'auto';
                } else {
                    sidebar.style.position = 'sticky';
                    sidebar.style.top = '24px';
                }
            });
        }, { rootMargin: '0px 0px -200px 0px', threshold: 0 });

        obs.observe(footer);
    }


    /* ── Instructor bio read more ────────────────────── */
    function initBioToggle() {
        if (!bioEl || !bioToggle) return;

        let truncated = true;
        bioEl.classList.add('rsc-bio-truncated');

        bioToggle.addEventListener('click', function () {
            truncated = !truncated;
            bioEl.classList.toggle('rsc-bio-truncated', truncated);
            const isRo = document.body.classList.contains('rima-lang-ro');
            bioToggle.textContent = truncated
                ? (isRo ? 'Citește mai mult ↓' : 'Read more ↓')
                : (isRo ? 'Arată mai puțin ↑' : 'Show less ↑');
        });
    }

    /* ── Gallery/hero play button ────────────────────── */
    function initPlayButton() {
        const playBtn = document.querySelector('.rsc-play-btn');
        if (!playBtn) return;

        const videoUrl = playBtn.dataset.video;
        if (!videoUrl) return;

        playBtn.addEventListener('click', function () {
            const iframe = document.createElement('iframe');
            iframe.src = videoUrl + '?autoplay=1';
            iframe.style.cssText = 'width:100%;height:100%;border:none;';
            iframe.allow = 'autoplay; encrypted-media';
            iframe.allowFullscreen = true;
            playBtn.parentNode.replaceChild(iframe, playBtn.previousElementSibling);
            playBtn.remove();
        });
    }

    /* ── Tab Rename (Curriculum -> Information) ──────── */
    function renameCurriculum() {
        // Find tab links
        var tabLinks = document.querySelectorAll('.eltdf-tabs-nav li a');
        tabLinks.forEach(function(link) {
            if (link.textContent.trim().toLowerCase() === 'curriculum') {
                link.textContent = 'Information';
            }
        });

        // Find internal titles if any
        var titles = document.querySelectorAll('.eltdf-curriculum-title, .rsc-section-title');
        titles.forEach(function(title) {
            if (title.textContent.includes('Curriculum')) {
                title.innerHTML = title.innerHTML.replace('Curriculum', 'Information');
            }
        });
    }

    /* ── Boot ────────────────────────────────────────── */
    function init() {
        renameCurriculum();
        initMobileCta();
        initStickySidebar();
        initBioToggle();
        initPlayButton();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
