<?php 

/**
 * Enqueue parent and child theme styles
 * This function ensures proper loading order of stylesheets
 */
function base_hotel_child_enqueue_styles() {
    // Define parent theme's style handle
    $parent_style = 'base-hotel-style';
    
    // Enqueue parent theme's stylesheet first
    wp_enqueue_style($parent_style, 
        get_template_directory_uri() . '/style.css'  // Path to parent theme's style.css
    );
    
    // Enqueue child theme's stylesheet with parent dependency
    wp_enqueue_style('base-hotel-child-style',
        get_stylesheet_directory_uri() . '/style.css',  // Path to child theme's style.css
        array($parent_style),  // Make child style dependent on parent style
        wp_get_theme()->get('Version')  // Use child theme version for cache busting
    );
}

// Hook the enqueue function into WordPress
add_action('wp_enqueue_scripts', 'base_hotel_child_enqueue_styles');


/**
 * Add DNS prefetch and preconnect for external domains
 * Improves initial connection time for external resources
 */
add_action('wp_head', function () {
    $domains = [
        'cdn-cookieyes.com',
        'directory.cookieyes.com',
        'log.cookieyes.com'
    ];
    
    foreach ($domains as $domain) {
        printf(
            '<link rel="dns-prefetch" href="//%1$s">'."\n".
            '<link rel="preconnect" href="https://%1$s" crossorigin>'."\n",
            esc_attr($domain)
        );
    }
}, 1);