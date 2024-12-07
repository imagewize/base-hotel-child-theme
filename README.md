# Base Hotel Child Theme

A child theme for the Base Hotel WordPress theme with performance optimizations.

## Features

### Style Enhancements
- Properly inherits all styles from the parent Base Hotel theme
- Allows for custom style overrides through child theme's style.css
- Responsive slider title text sizing for mobile devices
- Custom styling for featured item titles using local Poly font

### Performance Optimizations
- Implements DNS prefetch and preconnect for external domains:
  - cdn-cookieyes.com
  - directory.cookieyes.com
  - log.cookieyes.com

## Performance Optimizations

### Local Fonts
- Replaced Google Fonts with local WOFF2 font files
  - Open Sans (weights: 300, 400, 500, 600, 700)
  - Poly (weight: 400)
- Font files are served with font-display: swap for optimal loading

### Removed Features
- Disabled WordPress emoji support
  - Removed emoji CSS
  - Removed emoji JavaScript
  - Removed emoji DNS prefetch
  - Disabled emoji in TinyMCE editor

### DNS Prefetch
- Added DNS prefetch for external services
  - CookieYes domains

### Lazy Loading
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
- Consolidated Font Awesome loading to prevent redundant requests

### Technical Details
- Version: 1.2.3
- Parent Theme: Base Hotel
- Author: Imagewize (https://imagewize.com)

## Multisite Setup

To use this child theme in a WordPress Multisite environment:

1. Ensure the parent theme (`Base Hotel`) is installed and network-enabled.
2. Install and network-enable the child theme (`Base Hotel Child Theme`).
3. Activate the child theme on the desired site via `Network Admin` > `Sites` > `Edit` > `Themes`.