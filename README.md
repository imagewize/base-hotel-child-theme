# Base Hotel Child Theme

A child theme for the Base Hotel WordPress theme with performance optimizations.

## Core Features

### Style Enhancements
- Custom styling for featured item titles using local Poly font
- Responsive design optimizations:
  - Mobile-friendly slider title text sizing
  - Adaptive cookie consent container layout
- GPU-accelerated animations and transitions
- Performance-focused CSS containment strategies
- Hardware-accelerated transformations
- Reduced paint areas and layout updates

### Performance Features
- DNS prefetch and preconnect optimizations
- Local font serving with WOFF2 format
- Enhanced lazy loading capabilities
- Responsive image handling
- Resource optimization and deduplication
- Build system with asset optimization

## Technical Implementation

### Font Optimization
- Local WOFF2 font files replace Google Fonts:
  - Open Sans (weights: 300, 400, 500, 600, 700)
  - Poly (weight: 400)
- Font-display: swap for optimal loading

### Network Optimizations
- DNS prefetch and preconnect for external services:
  - cdn-cookieyes.com
  - directory.cookieyes.com
  - log.cookieyes.com
  - cdn.tiqets.com

### Lazy Loading System
- Enhanced native WordPress lazy loading functionality
- Extends lazy loading support to:
  - ACF image fields
  - Template parts
  - Widget areas
  - Background images
  - iframes
  - Dynamically loaded content
- Includes polyfill for older browsers

### Responsive Images
- Optimized slider background images for mobile devices
- Automatically serves smaller (750x400) images on mobile
- Reduces bandwidth usage and improves load times on mobile devices

### Resource Optimization
- Removed duplicate Font Awesome CSS from WP Post and Blog Designer plugin
- Dequeued WordPress blocks CSS library to prevent unnecessary CSS loading
- Consolidated Font Awesome loading to prevent redundant requests
- Optimized Tiqets widget loading with lazy loading and script deduplication

### Tiqets Widget Optimization
- Implements lazy loading for Tiqets booking widgets
- Only loads Tiqets script when widgets come into viewport
- Prevents duplicate loader script inclusions
- Uses Intersection Observer for efficient viewport detection
- Adds preconnect for cdn.tiqets.com domain

### CookieYes Optimizations
- Performance-optimized consent container
- GPU-accelerated animations
- Reduced repaints and reflows
- Contained layout updates
- Responsive design optimizations
- Mobile-specific positioning and sizing

### Build System
- Webpack-based build system for asset optimization
- Asset versioning with content hashes for cache busting
- SCSS compilation with modern CSS features
- JavaScript bundling and minification
- Font file optimization and management
- Manifest-based asset versioning

### Asset Optimization
- Concatenation of CSS and JavaScript files
- Minification of CSS and JavaScript in production
- Source maps in development mode
- Automated font file handling with WOFF2 optimization
- Cache busting through content hash versioning
- Asset manifest generation for reliable file referencing

### Technical Details
- Version: 1.4.0
- Parent Theme: Base Hotel
- Author: Imagewize (https://imagewize.com)

## Multisite Setup

To use this child theme in a WordPress Multisite environment:

1. Ensure the parent theme (`Base Hotel`) is installed and network-enabled.
2. Install and network-enable the child theme (`Base Hotel Child Theme`).
3. Activate the child theme on the desired site via `Network Admin` > `Sites` > `Edit` > `Themes`.

## Server Configuration

### WP-CLI Installation
- Added WP-CLI for efficient WordPress management:
  ```bash
  curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
  chmod +x wp-cli.phar
  sudo mv wp-cli.phar /usr/local/bin/wp
  ```
- Configured proper permissions and aliases:
  ```bash
  sudo mkdir -p /var/www/.wp-cli
  sudo chown -R www-data:www-data /var/www/.wp-cli
  echo 'alias wp="sudo -u www-data wp"' >> ~/.bashrc
  ```

### Apache Optimizations
- Enabled Apache modules for improved caching:
  - mod_expires for browser caching control
  - mod_headers for custom HTTP headers
  ```bash
  a2enmod expires
  a2enmod headers
  systemctl restart apache2
  ```

### Security Measures
- Suggested proper file permissions to be implemented by server admin:
  ```bash
  sudo find /var/www/site.com/public_html -type d -exec chmod 775 {} \;
  sudo find /var/www/ -type f -exec chmod 644 {} \;
  ```
  - Directories: 775 (proper access while maintaining security)
  - Files: 644 (readable but protected from unauthorized writes)