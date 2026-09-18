(function () {
    'use strict';

    /* ── Department Submenu Toggle (tap has no :hover) ─ */
    document.querySelectorAll('.has-submenu > a[href="#"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            link.parentElement.classList.toggle('open');
        });
    });

    /* ── Product Image Gallery Slider ──────────────── */
    // Scroll-snap does the swiping natively; this only syncs the dots/arrows.
    document.querySelectorAll('[data-gallery]').forEach(function (gallery) {
        var track = gallery.querySelector('.product-gallery-track');
        var dots = gallery.querySelectorAll('.product-gallery-dot');
        var prev = gallery.querySelector('.product-gallery-prev');
        var next = gallery.querySelector('.product-gallery-next');
        if (!track || !dots.length) return;

        function slideTo(index) {
            index = Math.max(0, Math.min(dots.length - 1, index));
            track.scrollTo({ left: track.clientWidth * index, behavior: 'smooth' });
        }

        function currentIndex() {
            return Math.round(track.scrollLeft / track.clientWidth);
        }

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () { slideTo(i); });
        });

        if (prev) prev.addEventListener('click', function () { slideTo(currentIndex() - 1); });
        if (next) next.addEventListener('click', function () { slideTo(currentIndex() + 1); });

        track.addEventListener('scroll', function () {
            var index = currentIndex();
            dots.forEach(function (dot, i) { dot.classList.toggle('active', i === index); });
        });
    });

    /* ── Image Lazy Loading Fallback ──────────────── */
    if ('loading' in HTMLImageElement.prototype) {
        document.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
            img.src = img.dataset.src || img.src;
        });
    } else {
        var lazyImages = document.querySelectorAll('img[loading="lazy"]');
        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.src = img.dataset.src || img.src;
                        img.removeAttribute('loading');
                        observer.unobserve(img);
                    }
                });
            });
            lazyImages.forEach(function (img) {
                observer.observe(img);
            });
        }
    }

    /* ── Smooth Scroll for Anchor Links ────────────── */
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;
            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ── Form Validation Enhancement ───────────────── */
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
            var isValid = true;
            inputs.forEach(function (input) {
                if (!input.value.trim()) {
                    input.style.borderColor = 'var(--err)';
                    isValid = false;
                } else {
                    input.style.borderColor = '';
                }
            });
            var csrf = form.querySelector('input[name="csrf_token"]');
            if (!csrf || !csrf.value) {
                console.warn('CSRF token missing in form');
            }
        });

        form.querySelectorAll('input, textarea, select').forEach(function (input) {
            input.addEventListener('input', function () {
                this.style.borderColor = '';
            });
        });
    });

    /* ── Alert Auto-Dismiss ────────────────────────── */
    // .alert also styles permanent controls (cart +/-, Edit/Delete) - only flash messages should fade away.
    document.querySelectorAll('.alert:not(a):not(button)').forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            setTimeout(function () {
                if (alert.parentNode) alert.parentNode.removeChild(alert);
            }, 300);
        }, 5000);
    });

    /* ── Set Current Year in Footer ────────────────── */
    var yearEl = document.querySelector('footer p');
    if (yearEl && yearEl.textContent.indexOf('Developed by') !== -1) {
        yearEl.innerHTML = 'Developed by David Iglesias&copy; ' + new Date().getFullYear();
    }
})();
