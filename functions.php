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
 * Add DNS prefetch for CookieYes domains before any scripts load
 */
function add_cookieyes_dns_prefetch() {
    $domains = [
        'cdn-cookieyes.com',
        'directory.cookieyes.com',
        'log.cookieyes.com'
    ];
    
    // Buffer output to ensure clean HTML
    ob_start();
    foreach ($domains as $domain) {
        printf(
            '<link rel="dns-prefetch" href="//%1$s">'."\n".
            '<link rel="preconnect" href="https://%1$s" crossorigin>'."\n",
            esc_attr($domain)
        );
    }
    $output = ob_get_clean();
    
    // Echo at the very start of head
    echo "<!-- DNS Prefetch for CookieYes -->\n" . $output;
}

// Add DNS prefetch as early as possible
add_action('wp_head', 'add_cookieyes_dns_prefetch', -1);

// Remove all other CookieYes customizations
remove_action('wp_head', 'optimize_cookieyes_loading', 1);
remove_action('wp_head', 'add_cookieyes_script', 2);
remove_action('wp_enqueue_scripts', 'modify_cookieyes_script', 999);

function disable_emoji_feature() {

    // Prevent Emoji from loading on the front-end
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );

    // Remove from admin area also
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );

    // Remove from RSS feeds also
    remove_filter( 'the_content_feed', 'wp_staticize_emoji');
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji');

    // Remove from Embeds
    remove_filter( 'embed_head', 'print_emoji_detection_script' );

    // Remove from emails
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

    // Disable from TinyMCE editor. Currently disabled in block editor by default
    add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );

    /** Finally, prevent character conversion too
         ** without this, emojis still work 
         ** if it is available on the user's device
     */

    add_filter( 'option_use_smilies', '__return_false' );

}

function disable_emojis_tinymce( $plugins ) {
    if( is_array($plugins) ) {
        $plugins = array_diff( $plugins, array( 'wpemoji' ) );
    }
    return $plugins;
}

add_action('init', 'disable_emoji_feature');