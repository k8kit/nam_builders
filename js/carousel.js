// Carousel Auto-scroll Animation
document.addEventListener('DOMContentLoaded', function() {
    const carouselWrapper = document.getElementById('carouselWrapper');
    
    if (carouselWrapper) {
        // Get the total width of original items (before duplication)
        const originalItems = Array.from(carouselWrapper.children);
        const itemCount = originalItems.length / 2; // Since items are duplicated
        
        // Calculate the animation duration based on content width
        // This ensures smooth continuous scrolling
        const wrapperWidth = carouselWrapper.scrollWidth / 2;
        const animationDuration = (wrapperWidth / 100) * 1; // Adjust speed here (in seconds per 100px)
        
        // Set CSS animation dynamically
        const style = document.createElement('style');
        style.textContent = `
            @keyframes scroll-left {
                0% {
                    transform: translateX(0);
                }
                100% {
                    transform: translateX(-${wrapperWidth}px);
                }
            }
            
            #carouselWrapper {
                animation: scroll-left ${animationDuration}s linear infinite !important;
            }
            
            #carouselWrapper:hover {
                animation-play-state: paused !important;
            }
        `;
        document.head.appendChild(style);
    }
});

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#' && document.querySelector(href)) {
            e.preventDefault();
            document.querySelector(href).scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Alert auto-dismiss after 5 seconds
window.addEventListener('load', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        }, 5000);
    });
});
