import './bootstrap';

(function () {
    'use strict';

    // Mobile navigation toggle
    const navToggle = document.getElementById('nav-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (navToggle && mobileMenu) {
        navToggle.addEventListener('click', function () {
            const expanded = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', String(!expanded));
            mobileMenu.classList.toggle('open');
            document.body.classList.toggle('menu-open', !expanded);
        });
    }

    // Animated count-up for statistics
    function animateCountUp(el) {
        const target = parseInt(el.getAttribute('data-count-up'), 10);
        if (Number.isNaN(target)) return;

        const duration = 1300;
        const start = performance.now();

        function tick(now) {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(eased * target).toLocaleString('en-US');
            if (progress < 1) requestAnimationFrame(tick);
        }

        requestAnimationFrame(tick);
    }

    const statValues = document.querySelectorAll('.stat-value[data-count-up]');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                animateCountUp(entry.target);
                obs.unobserve(entry.target);
            });
        }, { threshold: 0.4 });

        statValues.forEach(function (el) { observer.observe(el); });
    } else {
        statValues.forEach(animateCountUp);
    }

    // Footer year
    const yearEl = document.querySelector('[data-year]');
    if (yearEl) {
        const currentYear = new Date().getFullYear();
        const founding = parseInt(yearEl.getAttribute('data-founding') || String(currentYear), 10);
        const org = yearEl.getAttribute('data-org') || '';
        const years = founding && founding !== currentYear
            ? founding + '\u2013' + currentYear
            : String(currentYear);
        yearEl.textContent = '\u00A9 ' + years + ' ' + org + '. جميع الحقوق محفوظة.';
    }
})();
