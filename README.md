# Base Hotel Child Theme

A child theme for the Base Hotel WordPress theme, providing additional functionality and customizations.

## Features

### Style Enhancements
- Properly inherits all styles from the parent Base Hotel theme
- Allows for custom style overrides through child theme's style.css

### Performance Optimizations
- Implements DNS prefetch and preconnect for external domains:
  - cdn-cookieyes.com
  - directory.cookieyes.com
  - log.cookieyes.com

### Technical Details
- Version: 1.0.0
- Parent Theme: Base Hotel
- Author: Imagewize (https://imagewize.com)

## Multisite Setup

To use this child theme in a WordPress Multisite environment:

1. Ensure the parent theme (`Base Hotel`) is installed and network-enabled.
2. Install and network-enable the child theme (`Base Hotel Child Theme`).
3. Activate the child theme on the desired site via `Network Admin` > `Sites` > `Edit` > `Themes`.