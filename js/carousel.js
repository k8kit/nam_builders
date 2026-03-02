// ── Clients Carousel ─────────────────────────────────────────────────────────
// PHP renders every client TWICE so the list loops seamlessly:
//   [A1 A2 A3 A4 | A1 A2 A3 A4]
// Animating translateX(0 → -halfWidth) brings us back to the visual start.

window.addEventListener('load', function () {
    var wrapper = document.getElementById('carouselWrapper');
    if (!wrapper) return;

    // Force layout recalculation before measuring
    wrapper.style.display = 'flex';

    var imgs = Array.from(wrapper.querySelectorAll('img'));

    function initCarousel() {
        // Use setTimeout to ensure browser has finished layout after image loads
        setTimeout(function () {
            var halfWidth = wrapper.scrollWidth / 2;

            // Fallback: if scrollWidth is still wrong, compute manually
            if (halfWidth < 100) {
                var items = wrapper.querySelectorAll('.carousel-item');
                var itemCount = items.length / 2; // divided by 2 because we duplicate
                // 180px width + 2rem (32px) gap = 212px per item
                halfWidth = itemCount * (180 + 32);
            }

            halfWidth = Math.floor(halfWidth);

            // Remove any existing carousel style
            var existing = document.getElementById('_carouselStyle');
            if (existing) existing.parentNode.removeChild(existing);

            // Speed: 60px/s, minimum 10s
            var duration = Math.max(halfWidth / 60, 10);

            var style = document.createElement('style');
            style.id = '_carouselStyle';
            style.textContent =
                '@keyframes _cLoop{' +
                '  0%{transform:translateX(0)}' +
                '  100%{transform:translateX(-' + halfWidth + 'px)}' +
                '}' +
                '#carouselWrapper{' +
                '  animation:_cLoop ' + duration + 's linear infinite!important;' +
                '  will-change:transform;' +
                '}' +
                '#carouselWrapper:hover{animation-play-state:paused!important}';
            document.head.appendChild(style);
        }, 100);
    }

    if (imgs.length === 0) {
        initCarousel();
        return;
    }

    var loaded = 0;
    var total = imgs.length;

    function onImageSettled() {
        loaded++;
        if (loaded >= total) {
            initCarousel();
        }
    }

    imgs.forEach(function (img) {
        if (img.complete) {
            onImageSettled();
        } else {
            img.addEventListener('load', onImageSettled);
            img.addEventListener('error', onImageSettled);
        }
    });

    // Safety fallback: force start after 2 seconds regardless
    setTimeout(initCarousel, 2000);
});

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