if (!('loading' in HTMLImageElement.prototype)) {
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('img[loading="lazy"]');
        const options = {
            root: null,
            rootMargin: '50px',
            threshold: 0
        };

        const loadImage = (img) => {
            img.src = img.getAttribute('src');
            if (img.getAttribute('srcset')) {
                img.srcset = img.getAttribute('srcset');
            }
            img.removeAttribute('loading');
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    try {
                        loadImage(img);
                    } catch (error) {
                        console.error('Error loading image:', error);
                    }
                    observer.unobserve(img);
                }
            });
        }, options);

        images.forEach(img => observer.observe(img));
    });
}