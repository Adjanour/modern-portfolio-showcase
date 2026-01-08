# Changelog

All notable changes to the Modern Portfolio Showcase plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.0] - 2026-01-08

### Added
- **Settings Page**: New admin settings page (Portfolio → Settings) with tabbed interface
- **CSS Variables**: All styles now use CSS custom properties for easy theming
- **Color Customization**: Primary color, hover color, overlay, text colors
- **Dimension Controls**: Card width/height, border radius, grid gap
- **3D Effect Tuning**: Perspective, rotation angle, blur levels (side/far cards)
- **Animation Settings**: Transition speed, auto-slide delay, enable/disable auto-slide
- **Display Toggles**: Show/hide title bar, play button, nav buttons, view toggle
- **Shortcode Attributes**: Override settings per shortcode instance
- **Settings Reset**: One-click reset to defaults

### Changed
- Updated card title to always-visible title bar at bottom of cards
- Moved "View Details" button to bottom-right corner on hover
- Play button now shows on hover for all slides (not just active)
- Hover overlay is now semi-transparent with pointer-events passthrough
- JavaScript CONFIG now reads from WordPress settings

### Technical
- New `Portfolio_Settings` class for settings management
- CSS variables generated dynamically from PHP settings
- Settings passed to JavaScript via `wp_localize_script`
- All hardcoded colors/sizes replaced with CSS variables

## [2.0.0] - 2026-01-08

### Added
- 3D coverflow carousel with perspective transforms
- Video support (YouTube, Vimeo, direct uploads)
- Hover overlay on carousel cards
- Auto-slide with pause on hover
- Keyboard navigation (arrow keys)
- Comprehensive code comments throughout

### Changed
- Complete CSS refactor: 2008 lines → ~680 lines (66% reduction)
- Complete JS refactor: 543 lines → ~350 lines (35% reduction)
- New files: `frontend-clean.css`, `frontend-clean.js`
- Improved template structure with HTML comments
- Better responsive breakpoints

### Removed
- Legacy card stack code
- Duplicate auto-slide functions
- Unused `.portfolio-card` and `.card-stack-*` styles
- Redundant documentation files (consolidated into README.md)

### Documentation
- Consolidated docs into single README.md
- Kept detailed carousel tutorial in `docs/3d-carousel-tutorial.md`
- Removed: INDEX.md, QUICKSTART.md, DEVELOPMENT.md, ARCHITECTURE.md, CAROUSEL-CUSTOMIZATION.md

## [1.0.0] - 2026-01-05

### Added
- Initial release of Modern Portfolio Showcase plugin
- Portfolio management with categories and projects
- Slideshow and grid view display modes
- Category filtering functionality
- Rich text editor for project descriptions
- Image carousel support for multiple project images
- Responsive design for mobile devices
- Admin dashboard with statistics
- AJAX-powered interface for smooth interactions

### Changed
- Restructured repository following WordPress plugin best practices
- Separated concerns: UI (CSS/JS), PHP logic (classes), and templates
- Split monolithic plugin file into modular classes
- Organized files into logical directories:
  - `/admin/` - Admin-specific assets
  - `/assets/` - Frontend assets
  - `/includes/` - PHP classes and core logic
  - `/templates/` - Template files
  - `/examples/` - Example and preview files

### Technical
- Created `class-portfolio-database.php` for database operations
- Created `class-portfolio-admin.php` for admin functionality
- Created `class-portfolio-ajax.php` for AJAX handlers
- Created `class-portfolio-frontend.php` for frontend display
- Separated admin and frontend templates
- Added comprehensive documentation (README.md, DEVELOPMENT.md)
- Added .gitignore for proper version control

### Security
- Implemented nonce verification for all AJAX requests
- Added capability checks for admin operations
- Sanitized all user inputs
- Escaped all outputs
- Used WordPress core functions for secure data handling

[1.0.0]: https://github.com/Adjanour/modern-portfolio-showcase/releases/tag/v1.0.0
