(function () {
    function initCarousel() {
        var wrapper = document.getElementById('carouselWrapper');
        if (!wrapper) return;

        var items = wrapper.querySelectorAll('.carousel-item');
        if (!items.length) return;

        wrapper.style.cssText = 'display:flex;flex-wrap:nowrap;align-items:center;gap:2rem;width:max-content;will-change:transform;';

        items.forEach(function (item) {
            item.style.cssText = 'flex:0 0 180px;flex-shrink:0;min-width:180px;width:180px;height:120px;display:flex;align-items:center;justify-content:center;overflow:hidden;border-radius:8px;background:#F0F4FA;';

            var img = item.querySelector('img');
            if (img) {
                img.style.cssText = 'width:100%;height:100%;object-fit:contain;padding:1rem;display:block;flex-shrink:0;max-width:none;';
            }

            var ph = item.querySelector('.carousel-placeholder');
            if (ph) {
                ph.style.cssText = 'width:100%;height:100%;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:0.5rem;';
            }
        });

        function startAnimation() {
            setTimeout(function () {
                var totalWidth = wrapper.scrollWidth;
                var halfWidth  = Math.floor(totalWidth / 2);

                console.log('[Carousel] scrollWidth=' + totalWidth + ', halfWidth=' + halfWidth + ', items=' + items.length);

                if (halfWidth < 50) {
                    console.warn('[Carousel] halfWidth too small (' + halfWidth + '), retrying in 500ms');
                    setTimeout(startAnimation, 500);
                    return;
                }

                var duration = Math.max(halfWidth / 60, 8);

                var old = document.getElementById('_carouselStyle');
                if (old) old.remove();

                var style = document.createElement('style');
                style.id = '_carouselStyle';
                style.textContent =
                    '@keyframes _cScroll{0%{transform:translateX(0)}100%{transform:translateX(-' + halfWidth + 'px)}}' +
                    '#carouselWrapper{animation:_cScroll ' + duration + 's linear infinite!important;}' +
                    '#carouselWrapper:hover{animation-play-state:paused!important;}';
                document.head.appendChild(style);
            }, 200);
        }

        var imgs = Array.from(wrapper.querySelectorAll('img'));
        if (!imgs.length) { startAnimation(); return; }

        var loaded = 0;
        function onDone() { if (++loaded >= imgs.length) startAnimation(); }
        imgs.forEach(function (img) {
            if (img.complete) onDone();
            else { img.addEventListener('load', onDone); img.addEventListener('error', onDone); }
        });
    }

    if (document.readyState === 'complete') initCarousel();
    else window.addEventListener('load', initCarousel);
}());

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

window.addEventListener('load', function () {
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            var btn = alert.querySelector('.btn-close');
            if (btn) btn.click();
        }, 5000);
    });
});