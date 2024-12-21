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
    // Enqueue parent theme stylesheet first
    wp_enqueue_style(
        'base-hotel-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme('base-hotel')->get('Version')
    );
    
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
            array('base-hotel-style'), // Make sure our custom CSS loads after parent theme
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
 * Dequeue backgrounds.css from parent theme
 */
function dequeue_parent_backgrounds() {
    // Note: The handle is base_hotel_backgrounds (with underscore)
    wp_dequeue_style('base_hotel_backgrounds');
    wp_deregister_style('base_hotel_backgrounds');
}
add_action('wp_enqueue_scripts', 'dequeue_parent_backgrounds', 999);

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
 * Adds loading="lazy" attribute to supported HTML elements in content
 * 
 * WordPress core handles lazy loading for post content images by default.
 * This function extends lazy loading support to:
 * - iframe elements for embedded content
 * - Elements with background-image CSS properties
 * 
 * Note: Image lazy loading for <img> tags is not used as WordPress 6.3+ handles this natively
 * through the core functionality, especially for above-the-fold content like logos.
 * @see https://make.wordpress.org/core/2023/07/13/image-performance-enhancements-in-wordpress-6-3/
 * @see https://make.wordpress.org/core/2020/07/14/lazy-loading-images-in-5-5/
 * 
 * @param string $content The HTML content to be processed
 * @return string Modified content with added lazy loading attributes
 */
function add_lazy_loading($content) {
    if (is_admin() || is_feed() || is_preview()) {
        return $content;
    }

    $patterns = array(
        '/<iframe(?![^>]*loading=["\'])(.*?)src=/is' => '<iframe$1loading="lazy" src=',
        '/style="([^"]*?)background-image:\s*url\([\'"]?(.*?)[\'"]?\)([^"]*?)"/is' 
            => 'style="$1background-image: url($2)$3" loading="lazy"'
    );

    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement);
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

/**
 * Filter slider HTML to add responsive background images
 */
function modify_slider_background_urls($content) {
    if (is_admin()) return $content;
    
    $pattern = '/(<div[^>]*class="item"[^>]*style="[^"]*background-image:\s*url\([\'"]?)(.*?)([\'"]?\).*?")(.*?>)/i';
    
    // use ($log) removed from method below  before curly brace opening
    $modified = preg_replace_callback($pattern, function($matches) {
        $original_url = $matches[2];
        
        $base_url = preg_replace('/\.(jpg|webp)$/', '', $original_url);
        $mobile_url = wp_is_mobile() ? 
            $base_url . '-750x400.jpg' : 
            $original_url;
        
        return $matches[1] . $mobile_url . $matches[3] . $matches[4];
    }, $content);
    
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

/**
 * Dequeue WordPress block library CSS since we're not using blocks
 */
function dequeue_block_library_css() {
    if (!is_admin()) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
    }
}
add_action('wp_enqueue_scripts', 'dequeue_block_library_css', 100);

// Only keep the content filter for removing inline scripts
function remove_tiqets_inline_scripts($content) {
    return preg_replace('/<script[^>]*widgets\.tiqets\.com\/loader\.js[^>]*><\/script>/', '', $content);
}
add_filter('the_content', 'remove_tiqets_inline_scripts', 10);

/**
 * Registers an Advanced Custom Fields (ACF) field group for Hero sections
 * 
 * Creates a set of custom fields that allows users to customize hero sections on pages:
 * - Hero Image: Full-width background image (1920x1080px recommended)
 * - Hero Title: Main heading text
 * - Hero Tagline: Optional subtitle text
 * - CTA Button: Optional call-to-action with customizable text and URL
 * 
 * The field group appears on all pages and uses a seamless interface style for better integration
 * with the WordPress admin. All fields are organized in a single group for easier management
 * and better user experience.
 * 
 * @since 1.0.0
 * @uses acf_add_local_field_group() ACF function to register field groups programmatically
 * @return void
 */
function add_hero_field_group() {
    if(function_exists('acf_add_local_field_group')):
        acf_add_local_field_group(array(
            'key' => 'group_hero',
            'title' => 'Hero Options',
            'fields' => array(
                array(
                    'key' => 'field_hero_image',
                    'label' => 'Hero Image',
                    'name' => 'hero_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'required' => 1,
                    'instructions' => 'Select an image (recommended: 1920x1080px)'
                ),
                array(
                    'key' => 'field_hero_title',
                    'label' => 'Hero Title',
                    'name' => 'hero_title',
                    'type' => 'text',
                    'instructions' => 'Main title to display on hero image'
                ),
                array(
                    'key' => 'field_hero_tagline',
                    'label' => 'Hero Tagline',
                    'name' => 'hero_tagline',
                    'type' => 'text',
                    'instructions' => 'Optional subtitle/tagline'
                ),
                array(
                    'key' => 'field_hero_cta_title',
                    'label' => 'CTA Button Text',
                    'name' => 'hero_cta_title',
                    'type' => 'text',
                    'instructions' => 'Enter the text for the call-to-action button',
                    'required' => 0,
                    'wrapper' => array(
                        'width' => '50',
                        'class' => '',
                        'id' => '',
                    ),
                ),
                array(
                    'key' => 'field_hero_cta_url',
                    'label' => 'CTA Button URL',
                    'name' => 'hero_cta_url',
                    'type' => 'url',
                    'instructions' => 'Enter the URL for the call-to-action button',
                    'required' => 0,
                    'wrapper' => array(
                        'width' => '50',
                        'class' => '',
                        'id' => '',
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page'
                    )
                )
            ),
            'style' => 'seamless'
        ));
    endif;
}
add_action('acf/init', 'add_hero_field_group');