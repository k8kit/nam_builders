// ── Clients Carousel ─────────────────────────────────────────────────────────
// Uses CSS animation injected after layout is ready.
// PHP renders every client TWICE for seamless infinite loop.

(function () {
    function initCarousel() {
        var wrapper = document.getElementById('carouselWrapper');
        if (!wrapper) return;

        var items = wrapper.querySelectorAll('.carousel-item');
        if (!items.length) return;

        // Force layout styles directly on the wrapper and items via JS
        // This bypasses any CSS cascade issues entirely
        wrapper.style.display        = 'flex';
        wrapper.style.flexWrap       = 'nowrap';
        wrapper.style.alignItems     = 'center';
        wrapper.style.gap            = '2rem';
        wrapper.style.width          = 'max-content';
        wrapper.style.willChange     = 'transform';

        items.forEach(function (item) {
            item.style.flex        = '0 0 180px';
            item.style.flexShrink  = '0';
            item.style.minWidth    = '180px';
            item.style.height      = '120px';
            item.style.display     = 'flex';
            item.style.alignItems  = 'center';
            item.style.justifyContent = 'center';
            item.style.overflow    = 'hidden';
            item.style.borderRadius = '8px';
            item.style.background  = '#F0F4FA';

            var img = item.querySelector('img');
            if (img) {
                img.style.width         = '100%';
                img.style.height        = '100%';
                img.style.objectFit     = 'contain';
                img.style.padding       = '1rem';
                img.style.display       = 'block';
                img.style.flexShrink    = '0';
            }
        });

        // Wait for all images to load/error before measuring
        var imgs = Array.from(wrapper.querySelectorAll('img'));
        Promise.all(
            imgs.map(function (img) {
                return new Promise(function (resolve) {
                    if (img.complete) return resolve();
                    img.addEventListener('load',  resolve);
                    img.addEventListener('error', resolve);
                });
            })
        ).then(function () {
            // Give browser one more frame to finish layout
            requestAnimationFrame(function () {
                var totalWidth = wrapper.scrollWidth;
                var halfWidth  = Math.floor(totalWidth / 2);

                if (halfWidth < 10) return; // nothing to animate

                // px/s speed — slower = smoother feel
                var duration = Math.max(halfWidth / 60, 8);

                var style = document.createElement('style');
                style.textContent =
                    '@keyframes _cScroll {' +
                    '  from { transform: translateX(0); }' +
                    '  to   { transform: translateX(-' + halfWidth + 'px); }' +
                    '}' +
                    '#carouselWrapper {' +
                    '  animation: _cScroll ' + duration + 's linear infinite;' +
                    '}' +
                    '#carouselWrapper:hover {' +
                    '  animation-play-state: paused;' +
                    '}';
                document.head.appendChild(style);
            });
        });
    }

    // Run after full page load (images included)
    if (document.readyState === 'complete') {
        initCarousel();
    } else {
        window.addEventListener('load', initCarousel);
    }
}());

// ── Smooth anchor scrolling ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var target = this.getAttribute('href');
            if (target && target !== '#' && document.querySelector(target)) {
                e.preventDefault();
                document.querySelector(target).scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});

// ── Auto-dismiss Bootstrap alerts ────────────────────────────────────────────
window.addEventListener('load', function () {
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            var btn = alert.querySelector('.btn-close');
            if (btn) btn.click();
        }, 5000);
    });
});