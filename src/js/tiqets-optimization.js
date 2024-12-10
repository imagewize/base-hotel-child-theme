
/**
 * Optimize Tiqets widget loading by:
 * 1. Using Intersection Observer to detect when widgets come into viewport
 * 2. Loading the Tiqets loader script only once when first widget becomes visible
 * 3. Removing duplicate loader scripts from content
 * 
 * Benefits:
 * - Reduces initial page load by deferring widget loading
 * - Prevents multiple loader.js downloads
 * - Improves performance by loading resources just-in-time
 * - Maintains widget functionality while optimizing load time
 */
export function initTiqetsOptimization() {
    // Remove individual loader scripts
    document.querySelectorAll('script[src*="widgets.tiqets.com/loader.js"]').forEach(script => {
        script.remove();
    });

    let isLoading = false;

    // Initialize Intersection Observer
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoading) {
                isLoading = true;
                
                // Create preconnect link for cdn.tiqets.com
                const preconnect = document.createElement('link');
                preconnect.rel = 'preconnect';
                preconnect.href = 'https://cdn.tiqets.com';
                preconnect.crossOrigin = 'anonymous';
                document.head.appendChild(preconnect);

                // Load main Tiqets script
                const script = document.createElement('script');
                script.src = 'https://widgets.tiqets.com/loader.js';
                script.defer = true;
                document.body.appendChild(script);
                
                // Stop observing all widgets once we start loading
                document.querySelectorAll('[data-tiqets-widget]').forEach(widget => {
                    observer.unobserve(widget);
                });
            }
        });
    }, {
        rootMargin: '50px 0px', // Start loading slightly before widgets come into view
        threshold: 0.1
    });

    // Observe all Tiqets widgets
    document.querySelectorAll('[data-tiqets-widget]').forEach(widget => {
        observer.observe(widget);
    });
}