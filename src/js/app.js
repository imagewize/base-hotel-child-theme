/**
 * The main js file for this theme.
 * You can add both internal and external dependencies.
 * Webpack will take care of the rest
 * Add your scripts here.
 */

/**
 * Internal Dependencies
 */
import { initLazyLoadFallback } from './lazy-load-fallback';
import { initTiqetsOptimization } from './tiqets-optimization';

// Import styles
import '../scss/main.scss';

// Initialize lazy load fallback
initLazyLoadFallback();

// Initialize Tiqets optimization
document.addEventListener('DOMContentLoaded', initTiqetsOptimization);

