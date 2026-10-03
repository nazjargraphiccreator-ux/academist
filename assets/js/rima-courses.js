/**
 * RIMA Academy — Our Courses Filter & AJAX (2026)
 * rima-courses.js
 */
(function () {
    'use strict';

    /* ── State ──────────────────────────────────────── */
    const state = {
        category: '',
        search:   '',
        sort:     'title_asc',
        page:     1,
        loading:  false,
        view:     localStorage.getItem('roc_view') || 'grid',
    };

    /* ── DOM refs ────────────────────────────────────── */
    const grid        = document.getElementById('roc-grid');
    const filterBar   = document.getElementById('roc-filter-bar');
    const pills       = document.querySelectorAll('.roc-pill');
    const searchInput = document.getElementById('roc-search');
    const sortSelect  = document.getElementById('roc-sort');
    const viewGrid    = document.getElementById('roc-view-grid');
    const viewList    = document.getElementById('roc-view-list');
    const loadMoreBtn = document.getElementById('roc-load-more');
    const emptyState  = document.getElementById('roc-empty-state');
    const resultsCount= document.getElementById('roc-results-count');
    const resetBtn    = document.getElementById('roc-reset-filters');
    const loadMoreWrap= document.getElementById('roc-load-more-wrap');

    const catContainer = document.getElementById('roc-categories-container');
    const coursesSection = document.getElementById('roc-courses-section');
    const heroSection  = document.getElementById('roc-hero-section');
    const langCards    = document.querySelectorAll('.roc-lang-card-btn');
    const btnBack      = document.getElementById('roc-btn-back');

    /* ── Init ────────────────────────────────────────── */
    function init() {
        applyView(state.view, false);
        readUrlState();
        bindEvents();
        stickyFilterBar();
    }

    /* ── Read URL params on load ─────────────────────── */
    function readUrlState() {
        const params = new URLSearchParams(window.location.search);
        if (params.get('cat'))    { state.category = params.get('cat'); }
        if (params.get('search')) { state.search   = params.get('search'); }
        if (params.get('sort'))   { state.sort      = params.get('sort'); }

        if (state.category) activatePill(state.category);
        if (state.search && searchInput) searchInput.value = state.search;
        if (state.sort   && sortSelect)  sortSelect.value  = state.sort;

        if (state.category) {
            activatePill(state.category);
            if (catContainer) catContainer.style.display = 'none';
            if (heroSection) heroSection.style.display = 'none';
            if (coursesSection) coursesSection.style.display = 'block';
            updateLevelExplanations(state.category);
        } else {
            if (catContainer) catContainer.style.display = 'block';
            if (heroSection) heroSection.style.display = 'flex';
            if (coursesSection) coursesSection.style.display = 'none';
        }

        // If any filter active, trigger AJAX on load
        if (state.category || state.search || state.sort !== 'title_asc') {
            fetchCourses(false);
        }
    }

    // Global function for Language Selection
    window.rocSelectLanguage = function(slug) {
        if (catContainer) catContainer.style.display = 'none';
        if (heroSection) heroSection.style.display = 'none';
        if (coursesSection) coursesSection.style.display = 'block';
        
        state.category = slug || '';
        state.page = 1;
        activatePill(state.category);
        updateLevelExplanations(state.category);
        fetchCourses(false);
        pushState();
        
        // Scroll smoothly to courses section
        if (coursesSection) {
            coursesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    /* ── Bind Events ─────────────────────────────────── */
    function bindEvents() {
        // Language Category Cards
        langCards.forEach(function (card) {
            card.addEventListener('click', function () {
                const slug = this.dataset.langSlug;
                window.rocSelectLanguage(slug);
            });
        });

        // Back to Languages button
        if (btnBack) {
            btnBack.addEventListener('click', function () {
                if (coursesSection) coursesSection.style.display = 'none';
                if (catContainer) catContainer.style.display = 'block';
                if (heroSection) heroSection.style.display = 'flex';
                
                state.category = '';
                state.page = 1;
                pushState();
                
                // Optional: clear the grid to save memory, or just leave it hidden
            });
        }

        // Category pills
        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                state.category = this.dataset.cat || '';
                state.page     = 1;
                activatePill(state.category);
                fetchCourses(false);
                pushState();
            });
        });

        // Search — debounced
        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    state.search = searchInput.value.trim();
                    state.page   = 1;
                    fetchCourses(false);
                    pushState();
                }, 400);
            });
        }

        // Sort
        if (sortSelect) {
            sortSelect.addEventListener('change', function () {
                state.sort = this.value;
                state.page = 1;
                fetchCourses(false);
                pushState();
            });
        }

        // View toggle
        if (viewGrid) viewGrid.addEventListener('click', function () { applyView('grid', true); });
        if (viewList) viewList.addEventListener('click', function () { applyView('list', true); });

        // Load More
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function () {
                state.page++;
                fetchCourses(true);
            });
        }

        // Reset filters
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                state.category = '';
                state.search   = '';
                state.sort     = 'newest';
                state.page     = 1;
                if (searchInput) searchInput.value = '';
                if (sortSelect)  sortSelect.value  = 'newest';
                activatePill('');
                fetchCourses(false);
                pushState();
            });
        }
    }

    /* ── Level Explanations ──────────────────────────── */
    function updateLevelExplanations(slug) {
        const parallaxSection = document.getElementById('roc-levels-parallax');
        const descEnglish = document.querySelector('.roc-desc-english');
        const descJapanese = document.querySelector('.roc-desc-japanese');
        const descRomanian = document.querySelector('.roc-desc-romanian');

        if (!parallaxSection) return;

        // Hide all initially
        if (descEnglish) descEnglish.style.display = 'none';
        if (descJapanese) descJapanese.style.display = 'none';
        if (descRomanian) descRomanian.style.display = 'none';

        // Show based on slug
        let found = false;
        if (slug.includes('english') || slug.includes('engleza')) {
            if (descEnglish) descEnglish.style.display = 'block';
            found = true;
        } else if (slug.includes('japanese') || slug.includes('japoneza')) {
            if (descJapanese) descJapanese.style.display = 'block';
            found = true;
        } else if (slug.includes('romanian') || slug.includes('romana')) {
            if (descRomanian) descRomanian.style.display = 'block';
            found = true;
        }

        // Only show parallax section if we found a matching language
        parallaxSection.style.display = found ? 'block' : 'none';
    }

    /* ── AJAX Fetch ──────────────────────────────────── */
    function fetchCourses(append) {
        if (state.loading) return;
        state.loading = true;

        if (!append) {
            grid.classList.add('roc-loading');
        } else if (loadMoreBtn) {
            loadMoreBtn.classList.add('roc-loading');
            loadMoreBtn.disabled = true;
        }

        const formData = new FormData();
        formData.append('action',   'rima_filter_courses');
        formData.append('nonce',    rimaCourses.nonce);
        formData.append('category', state.category);
        formData.append('search',   state.search);
        formData.append('sort',     state.sort);
        formData.append('page',     state.page);
        formData.append('per_page', 9);

        fetch(rimaCourses.ajaxUrl, {
            method: 'POST',
            body:   formData,
            credentials: 'same-origin',
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            state.loading = false;
            grid.classList.remove('roc-loading');
            if (loadMoreBtn) {
                loadMoreBtn.classList.remove('roc-loading');
                loadMoreBtn.disabled = false;
            }

            if (!data.success) {
                showEmpty();
                return;
            }

            const res = data.data;

            if (append) {
                // Append new cards
                const temp = document.createElement('div');
                temp.innerHTML = res.html;
                const newCards = temp.querySelectorAll('.roc-card');
                newCards.forEach(function (card, i) {
                    card.classList.add('roc-appear');
                    card.style.animationDelay = (i * 60) + 'ms';
                    grid.insertBefore(card, grid.querySelector('.roc-skeleton'));
                });
            } else {
                // Replace all cards
                // Remove existing non-skeleton cards
                grid.querySelectorAll('.roc-card:not(.roc-skeleton)').forEach(function (c) { c.remove(); });

                if (!res.html || res.found === 0) {
                    showEmpty();
                    updateResults(0, 0);
                    if (loadMoreWrap) loadMoreWrap.style.display = 'none';
                    return;
                }

                hideEmpty();
                const temp = document.createElement('div');
                temp.innerHTML = res.html;
                const newCards = temp.querySelectorAll('.roc-card');
                const firstSkeleton = grid.querySelector('.roc-skeleton');
                newCards.forEach(function (card, i) {
                    card.classList.add('roc-appear');
                    card.style.animationDelay = (i * 50) + 'ms';
                    if (firstSkeleton) {
                        grid.insertBefore(card, firstSkeleton);
                    } else {
                        grid.appendChild(card);
                    }
                });
            }

            // Update results count
            updateResults(res.found, res.total);

            // Update Load More button
            if (loadMoreWrap) {
                if (res.pages && state.page < res.pages) {
                    loadMoreWrap.style.display = 'block';
                    if (loadMoreBtn) {
                        loadMoreBtn.dataset.maxPages = res.pages;
                    }
                } else {
                    loadMoreWrap.style.display = 'none';
                }
            }
        })
        .catch(function () {
            state.loading = false;
            grid.classList.remove('roc-loading');
            if (loadMoreBtn) {
                loadMoreBtn.classList.remove('roc-loading');
                loadMoreBtn.disabled = false;
            }
        });
    }

    /* ── UI helpers ──────────────────────────────────── */
    function activatePill(slug) {
        pills.forEach(function (p) {
            const active = (p.dataset.cat || '') === slug;
            p.classList.toggle('roc-pill-active', active);
            p.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    }

    function applyView(mode, persist) {
        state.view = mode;
        grid.classList.toggle('roc-view-grid-mode', mode === 'grid');
        grid.classList.toggle('roc-view-list-mode',  mode === 'list');
        if (viewGrid) { viewGrid.classList.toggle('active', mode === 'grid'); viewGrid.setAttribute('aria-pressed', mode === 'grid' ? 'true' : 'false'); }
        if (viewList) { viewList.classList.toggle('active', mode === 'list'); viewList.setAttribute('aria-pressed', mode === 'list' ? 'true' : 'false'); }
        if (persist) localStorage.setItem('roc_view', mode);
    }

    function showEmpty() {
        if (emptyState) emptyState.style.display = 'block';
        grid.querySelectorAll('.roc-card:not(.roc-skeleton)').forEach(function (c) { c.remove(); });
    }
    function hideEmpty() {
        if (emptyState) emptyState.style.display = 'none';
    }

    function updateResults(found, total) {
        if (!resultsCount) return;
        const bodyHasRo = document.body.classList.contains('rima-lang-ro');
        if (bodyHasRo) {
            resultsCount.innerHTML = 'Se afișează <strong>' + found + '</strong> din <strong>' + total + '</strong> cursuri';
        } else {
            resultsCount.innerHTML = 'Showing <strong>' + found + '</strong> of <strong>' + total + '</strong> courses';
        }
    }

    function pushState() {
        const params = new URLSearchParams();
        if (state.category) params.set('cat', state.category);
        if (state.search)   params.set('search', state.search);
        if (state.sort !== 'newest') params.set('sort', state.sort);
        const qs = params.toString();
        const url = window.location.pathname + (qs ? '?' + qs : '');
        history.pushState({ cat: state.category, search: state.search, sort: state.sort }, '', url);
    }

    /* ── Sticky filter bar IntersectionObserver ──────── */
    function stickyFilterBar() {
        if (!filterBar) return;
        const sentinel = document.createElement('div');
        sentinel.style.cssText = 'position:absolute;top:0;left:0;width:1px;height:1px;';
        filterBar.parentNode.insertBefore(sentinel, filterBar);
        const obs = new IntersectionObserver(function (entries) {
            filterBar.classList.toggle('is-stuck', !entries[0].isIntersecting);
        }, { rootMargin: '0px', threshold: 0 });
        obs.observe(sentinel);
    }

    /* ── Handle browser back/forward ─────────────────── */
    window.addEventListener('popstate', function (e) {
        if (e.state) {
            state.category = e.state.cat    || '';
            state.search   = e.state.search || '';
            state.sort     = e.state.sort   || 'newest';
            state.page     = 1;
            activatePill(state.category);
            if (searchInput) searchInput.value = state.search;
            if (sortSelect)  sortSelect.value  = state.sort;
            
            if (state.category) {
                if (catContainer) catContainer.style.display = 'none';
                if (coursesSection) coursesSection.style.display = 'block';
                fetchCourses(false);
            } else {
                if (catContainer) catContainer.style.display = 'block';
                if (coursesSection) coursesSection.style.display = 'none';
            }
        }
    });

    /* ── Boot ────────────────────────────────────────── */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
