<?php 

// Remove the BASEHOTEL_CHILD_VERSION constant definition as it's no longer needed

/**
 * Get entry from manifest file
 */
function get_manifest_entry($entry) {
    $manifest_path = get_stylesheet_directory() . '/public/manifest.json';
    
    if (file_exists($manifest_path)) {
        $manifest = json_decode(file_get_contents($manifest_path), true);
        return isset($manifest[$entry]) ? $manifest[$entry] : null;
    }
    
    return null;
}

/**
 * Enqueue parent and child theme styles
 * This function ensures proper loading order of stylesheets
 */
function base_hotel_child_enqueue_styles() {
    $js_entry = get_manifest_entry('app.js');
    $css_entry = get_manifest_entry('app.css');
    
    if ($js_entry) {
        wp_enqueue_script(
            'custom-js',
            get_stylesheet_directory_uri() . '/public/' . $js_entry,
            array(),
            null,
            true
        );
    }
    
    if ($css_entry) {
        wp_enqueue_style(
            'custom-css',
            get_stylesheet_directory_uri() . '/public/' . $css_entry,
            array(),
            null
        );
    }
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

function base_hotel_child_dequeue_google_fonts() {
    wp_dequeue_style('base_hotel_fonts');
}
add_action('wp_enqueue_scripts', 'base_hotel_child_dequeue_google_fonts', 20);

/**
 * Enhances WordPress's native lazy loading functionality
 * 
 * WordPress core only handles lazy loading for post content images,
 * but misses images in custom templates, ACF fields, widgets, and dynamic content.
 * This implementation extends lazy loading to cover:
 * - ACF image fields
 * - Template parts
 * - Widget areas
 * - Background images
 * - iframes
 * - Dynamically loaded content
 * 
 * @since 1.0.6
 */
function enhance_lazy_loading() {
    // Run earlier for better performance
    add_action('wp', function() {
        // Filter main content
        add_filter('the_content', 'add_lazy_loading', 99);
        
        // Filter ACF fields - both formatted and raw image fields
        add_filter('acf_the_content', 'add_lazy_loading', 99);
        add_filter('acf/format_value/type=image', 'add_lazy_loading_acf_image', 20, 3);
        
        // Filter gallery output
        add_filter('post_gallery', 'add_lazy_loading', 99);
        
        // Filter widget content
        add_filter('widget_text_content', 'add_lazy_loading', 99);
        
        // Filter template parts via output buffer
        ob_start('add_lazy_loading');
    });
}

/**
 * Adds loading="lazy" attribute to HTML elements that support it
 * 
 * Processes content and adds lazy loading to:
 * - img tags without existing loading attribute
 * - iframe elements
 * - elements with background-image CSS
 * 
 * @param string $content The content to be filtered
 * @return string Modified content with lazy loading attributes
 */
function add_lazy_loading($content) {
    if (is_admin() || is_feed() || is_preview()) {
        return $content;
    }

    $patterns = array(
        // Images
        '/<img(?![^>]*loading=["\'])(.*?)src=/is' => '<img$1loading="lazy" src=',
        // iframes
        '/<iframe(?![^>]*loading=["\'])(.*?)src=/is' => '<iframe$1loading="lazy" src=',
        // Background images (optional)
        '/style="([^"]*?)background-image:\s*url\([\'"]?(.*?)[\'"]?\)([^"]*?)"/is' 
            => 'style="$1background-image: url($2)$3" loading="lazy"'
    );

    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }

    return $content;
}

/**
 * Adds lazy loading support for ACF image fields
 * 
 * ACF image fields return arrays with image data. This function
 * adds the loading="lazy" attribute to the image array.
 * 
 * @param array|mixed $value The field value
 * @param int $post_id The post ID where the value was loaded from
 * @param array $field The field array containing all settings
 * @return array|mixed Modified field value with lazy loading
 */
function add_lazy_loading_acf_image($value, $post_id, $field) {
    if (!is_array($value) || empty($value['url'])) {
        return $value;
    }

    // Add loading attribute to ACF image array
    $value['loading'] = 'lazy';
    
    return $value;
}

// Initialize lazy loading
add_action('init', 'enhance_lazy_loading', 5);

// Optional: Add modern lazy load fallback
function add_lazy_load_fallback() {
    if (!is_admin()) {
        wp_enqueue_script(
            'lazy-load-fallback',
            get_stylesheet_directory_uri() . '/js/lazy-load-fallback.js',
            array(),
            wp_get_theme()->get('Version'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'add_lazy_load_fallback');

/**
 * Filter slider HTML to add responsive background images
 */
function modify_slider_background_urls($content) {
    if (is_admin()) return $content;
    
    // Debug code - commented out
    /*
    // Force debug to screen for testing
    echo "<!-- DEBUG START -->\n";
    echo "<!-- Content length: " . strlen($content) . " -->\n";
    echo "<!-- Is Mobile: " . (wp_is_mobile() ? 'yes' : 'no') . " -->\n";
    
    // Direct file logging
    $log = fopen(WP_CONTENT_DIR . '/slider-debug.log', 'a');
    fwrite($log, "\n=== " . date('Y-m-d H:i:s') . " ===\n");
    fwrite($log, "Content length: " . strlen($content) . "\n");
    fwrite($log, "Is Mobile: " . (wp_is_mobile() ? 'yes' : 'no') . "\n");
    */
    
    $pattern = '/(<div[^>]*class="item"[^>]*style="[^"]*background-image:\s*url\([\'"]?)(.*?)([\'"]?\).*?")(.*?>)/i';
    
    // use ($log) removed from method below  before curly brace opening
    $modified = preg_replace_callback($pattern, function($matches) {
        $original_url = $matches[2];
        /* Debug code - commented out
        fwrite($log, "Found URL: " . $original_url . "\n");
        echo "<!-- Found URL: " . $original_url . " -->\n";
        */
        
        $base_url = preg_replace('/\.(jpg|webp)$/', '', $original_url);
        $mobile_url = wp_is_mobile() ? 
            $base_url . '-750x400.jpg' : 
            $original_url;
            
        /* Debug code - commented out
        fwrite($log, "Modified to: " . $mobile_url . "\n");
        echo "<!-- Modified to: " . $mobile_url . " -->\n";
        */
        
        return $matches[1] . $mobile_url . $matches[3] . $matches[4];
    }, $content);
    
    /* Debug code - commented out
    fwrite($log, "=== END ===\n");
    fclose($log);
    echo "<!-- DEBUG END -->\n";
    */
    
    return $modified;
}

// Modify hooks to ensure we catch the content
remove_all_filters('base_hotel_featured_content');
add_filter('base_hotel_featured_content', 'modify_slider_background_urls', 1);
add_filter('the_content', 'modify_slider_background_urls', 1);

// Remove output buffering approach as it might interfere
remove_action('wp_head', function() { ob_start('modify_slider_background_urls'); }, 1);
remove_action('wp_footer', function() { ob_end_flush(); }, 99);

// Add filter to more hooks to catch the slider content
add_filter('the_content', 'modify_slider_background_urls', 20);
add_filter('base_hotel_featured_content', 'modify_slider_background_urls', 20);
add_filter('base_hotel_slider_html', 'modify_slider_background_urls', 20);

// Add output buffer to catch all content
add_action('wp_head', function() {
    ob_start('modify_slider_background_urls');
}, 1);
add_action('wp_footer', function() {
    ob_end_flush();
}, 99);

/**
 * Dequeue duplicate Font Awesome CSS from WP Post and Blog Designer plugin
 */
function dequeue_duplicate_font_awesome() {
    wp_dequeue_style('wpoh-fontawesome-css');
    wp_deregister_style('wpoh-fontawesome-css');
}
add_action('wp_enqueue_scripts', 'dequeue_duplicate_font_awesome', 20);
