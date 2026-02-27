// ── Clients Carousel ─────────────────────────────────────────────────────────
// PHP renders every client TWICE so the list loops seamlessly:
//   [A1 A2 A3 A4 | A1 A2 A3 A4]
// Animating translateX(0 → -halfWidth) brings us back to the visual start.
//
// KEY: we measure scrollWidth ONLY after window 'load' (all images decoded).
// That guarantees every item is fully laid out before we compute the distance.

window.addEventListener('load', function () {
    var wrapper = document.getElementById('carouselWrapper');
    if (!wrapper) return;

    var halfWidth = wrapper.scrollWidth / 2;
    if (halfWidth < 1) return; // nothing to animate

    // Speed: 80 px/s feels natural; minimum 8 s total
    var duration = Math.max(halfWidth / 80, 8);

    var style = document.createElement('style');
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