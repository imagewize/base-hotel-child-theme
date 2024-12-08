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

// Import styles
import '../scss/main.scss';

// Initialize lazy load fallback
initLazyLoadFallback();

