// ── Clients Carousel ─────────────────────────────────────────────────────────
// Pure CSS marquee — no JS measurement needed.
// PHP renders every client TWICE so the list loops seamlessly.
// The animation is defined in the HTML <style> block and just runs.

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