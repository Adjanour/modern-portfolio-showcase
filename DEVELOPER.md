# Developer Guide

Complete technical documentation for developers working on the Modern Portfolio Showcase plugin.

---

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Setup & Installation](#setup--installation)
3. [File Structure](#file-structure)
4. [Data Flow](#data-flow)
5. [PHP Classes](#php-classes)
6. [Frontend Architecture](#frontend-architecture)
7. [Database Schema](#database-schema)
8. [AJAX Endpoints](#ajax-endpoints)
9. [Extending the Plugin](#extending-the-plugin)
10. [Debugging](#debugging)

---

## Architecture Overview

### System Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     WordPress Environment                        │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │           modern-portfolio-showcase.php                     │ │
│  │                    (Entry Point)                            │ │
│  │                                                             │ │
│  │  • Plugin registration & activation                         │ │
│  │  • Requires all class files                                 │ │
│  │  • Initializes Modern_Portfolio_Plugin                      │ │
│  └──────────────────────┬──────────────────────────────────────┘ │
│                         │                                        │
│                         ▼                                        │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │              Modern_Portfolio_Plugin                        │ │
│  │                  (Main Controller)                          │ │
│  │                                                             │ │
│  │  Instantiates:                                              │ │
│  │  ├── Portfolio_Database  (data layer)                       │ │
│  │  ├── Portfolio_Admin     (admin UI)                         │ │
│  │  ├── Portfolio_Ajax      (API endpoints)                    │ │
│  │  └── Portfolio_Frontend  (public display)                   │ │
│  └──────────────────────┬──────────────────────────────────────┘ │
│                         │                                        │
│         ┌───────────────┼───────────────┬───────────────┐       │
│         ▼               ▼               ▼               ▼       │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐  ┌────────────┐ │
│  │  Database  │  │   Admin    │  │    AJAX    │  │  Frontend  │ │
│  │            │  │            │  │            │  │            │ │
│  │ • Tables   │  │ • Menus    │  │ • Save     │  │ • Shortcode│ │
│  │ • Schema   │  │ • Pages    │  │ • Delete   │  │ • Assets   │ │
│  │ • Queries  │  │ • Assets   │  │ • Get data │  │ • Template │ │
│  └────────────┘  └────────────┘  └────────────┘  └────────────┘ │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Design Principles

1. **Separation of Concerns**: Each class handles one responsibility
2. **Template/Logic Split**: PHP templates contain only display logic
3. **Asset Isolation**: Admin and frontend assets are separate
4. **WordPress Standards**: Uses WP APIs for database, AJAX, enqueuing

---

## Setup & Installation

### Development Environment

**Requirements:**
- WordPress 5.0+
- PHP 7.4+
- MySQL 5.6+

**Recommended Tools:**
- VS Code with PHP Intelephense
- Local by Flywheel or XAMPP
- Browser DevTools

### Installation Steps

```bash
# Clone into plugins directory
cd wp-content/plugins/
git clone <repository-url> modern-portfolio-showcase

# Activate plugin
# Go to WordPress Admin → Plugins → Activate "Modern Portfolio Showcase"
```

### First Run

On activation, the plugin:
1. Creates database tables (`portfolio_items`, `portfolio_categories`)
2. Registers admin menu
3. Registers `[modern_portfolio]` shortcode

---

## File Structure

```
modern-portfolio-showcase/
│
├── modern-portfolio-showcase.php    # Entry point, plugin header
│
├── includes/                        # PHP classes (business logic)
│   ├── class-portfolio-database.php # Database operations
│   ├── class-portfolio-admin.php    # Admin pages & menus
│   ├── class-portfolio-ajax.php     # AJAX handlers
│   └── class-portfolio-frontend.php # Shortcode & frontend
│
├── templates/                       # PHP templates (display only)
│   ├── frontend-portfolio.php       # Portfolio display
│   ├── admin-dashboard.php          # Admin dashboard
│   ├── admin-categories.php         # Category management
│   ├── admin-projects-list.php      # Project listing
│   └── admin-project-edit.php       # Add/edit project form
│
├── assets/                          # Frontend assets
│   ├── css/
│   │   └── frontend-clean.css       # Portfolio styles
│   └── js/
│       └── frontend-clean.js        # Carousel & interactions
│
├── admin/                           # Admin-only assets
│   ├── css/admin.css                # Admin styles
│   └── js/admin.js                  # Admin scripts
│
├── docs/                            # Documentation
│   └── 3d-carousel-tutorial.md      # Carousel deep-dive
│
└── examples/                        # Demos
    └── preview.html                 # Standalone preview
```

### File Naming Conventions

| Pattern | Purpose |
|---------|---------|
| `class-*.php` | PHP classes in `includes/` |
| `admin-*.php` | Admin templates |
| `frontend-*.php` | Public templates |
| `*-clean.css/js` | Refactored production assets |

---

## Data Flow

### Frontend Display Flow

```
User visits page with [modern_portfolio]
         │
         ▼
WordPress processes shortcode
         │
         ▼
Portfolio_Frontend::portfolio_shortcode()
         │
         ├──► Query database for projects & categories
         │
         ▼
Include templates/frontend-portfolio.php
         │
         ├──► Render HTML (carousel + grid)
         │
         ▼
Browser loads assets
         │
         ├──► frontend-clean.css (styles)
         └──► frontend-clean.js (interactions)
         │
         ▼
JavaScript initializes carousel
         │
         └──► updateCarousel() assigns position classes
```

### Admin Save Flow

```
Admin fills project form
         │
         ▼
Click "Save" button
         │
         ▼
admin.js captures form data
         │
         ▼
$.ajax() POST to admin-ajax.php
         │
         ├──► action: 'save_portfolio_item'
         ├──► nonce: security token
         └──► data: form fields
         │
         ▼
Portfolio_Ajax::save_portfolio_item()
         │
         ├──► Verify nonce
         ├──► Sanitize inputs
         └──► $wpdb->insert() or $wpdb->update()
         │
         ▼
Return JSON response
         │
         └──► Success/error message
```

### Carousel Interaction Flow

```
User clicks navigation button
         │
         ▼
Event listener in frontend-clean.js
         │
         ▼
showSlide(newIndex)
         │
         ├──► Handle wrap-around (circular)
         │
         ▼
updateCarousel()
         │
         ├──► Calculate diff from current slide
         ├──► Assign position classes:
         │    • diff = 0  → .active
         │    • diff = -1 → .prev-1
         │    • diff = -2 → .prev-2
         │    • diff = +1 → .next-1
         │    • diff = +2 → .next-2
         │    • else      → .hidden
         │
         ▼
CSS transitions animate changes
         │
         └──► 0.6s cubic-bezier easing
```

---

## PHP Classes

### Portfolio_Database

Handles all database operations.

```php
// Location: includes/class-portfolio-database.php

class Portfolio_Database {
    
    // Get table names
    public function get_table_name()      // Returns: wp_portfolio_items
    public function get_categories_table() // Returns: wp_portfolio_categories
    
    // Create tables on activation
    public function create_tables()
}
```

**Key Methods:**
- `create_tables()` - Creates schema on plugin activation
- `get_table_name()` - Returns prefixed table name

### Portfolio_Admin

Manages admin interface.

```php
// Location: includes/class-portfolio-admin.php

class Portfolio_Admin {
    
    // Register admin menu
    public function add_admin_menu()
    
    // Enqueue admin assets
    public function enqueue_admin_scripts()
    
    // Render admin pages
    public function render_dashboard()
    public function render_categories()
    public function render_projects()
    public function render_project_edit()
}
```

**Hooks Used:**
- `admin_menu` - Adds Portfolio menu
- `admin_enqueue_scripts` - Loads admin CSS/JS

### Portfolio_Ajax

Handles all AJAX requests.

```php
// Location: includes/class-portfolio-ajax.php

class Portfolio_Ajax {
    
    // Category operations
    public function save_category()
    public function delete_category()
    
    // Project operations
    public function save_portfolio_item()
    public function delete_portfolio_item()
    
    // Frontend
    public function load_project_details()
}
```

**Security:**
- All handlers verify nonce with `wp_verify_nonce()`
- Capability checks with `current_user_can()`

### Portfolio_Frontend

Handles public display.

```php
// Location: includes/class-portfolio-frontend.php

class Portfolio_Frontend {
    
    // Enqueue frontend assets
    public function enqueue_frontend_scripts()
    
    // Shortcode handler
    public function portfolio_shortcode()
    
    // Helper
    public function truncate_text($text, $length)
}
```

**Shortcode:**
```php
add_shortcode('modern_portfolio', array($this, 'portfolio_shortcode'));
```

---

## Frontend Architecture

### CSS Structure (frontend-clean.css)

```css
/* Section breakdown (~680 lines total) */

1. Base Container Styles     /* .modern-portfolio-container */
2. View Toggle Buttons       /* .view-toggle-simple, .simple-toggle-btn */
3. 3D Carousel Container     /* .carousel-3d-container, perspective */
4. Slide Base Styles         /* .carousel-slide base transforms */
5. Position Classes          /* .active, .prev-1, .next-1, etc. */
6. Media & Video             /* .slide-media, .slide-play-btn */
7. Navigation & Controls     /* .carousel-nav-btn, .carousel-dots */
8. Responsive Breakpoints    /* @media queries */
9. Grid View                 /* .grid-view, .portfolio-grid-item */
10. Project Modal            /* .project-modal */
11. Utility Classes          /* Performance optimizations */
```

### JavaScript Structure (frontend-clean.js)

```javascript
/* Section breakdown (~350 lines total) */

(function($) {
    'use strict';

    // 1. Global Variables
    let currentSlide = 0;
    let totalSlides = 0;
    let autoSlideInterval = null;

    // 2. View Toggle Functions
    function initViewToggle() { ... }

    // 3. Carousel Core
    function updateCarousel() { ... }  // Assigns position classes
    function showSlide(index) { ... }  // Navigate to slide

    // 4. Navigation
    function initCarouselNavigation() { ... }

    // 5. Video Playback
    function playVideo($slide) { ... }
    function stopAllMedia() { ... }

    // 6. Auto-Slide
    function startAutoSlide() { ... }
    function stopAutoSlide() { ... }

    // 7. Project Modal
    function loadProjectModal(projectId) { ... }
    function closeModal() { ... }

    // 8. Initialization
    function init() { ... }
    
    $(document).ready(init);

})(jQuery);
```

### 3D Transform System

The carousel uses CSS 3D transforms with 5 visible positions:

```
                    VIEWPORT
    ┌───────────────────────────────────────┐
    │                                       │
    │   [prev-2]  [prev-1]  [ACTIVE]  [next-1]  [next-2]
    │      ↖         ↑         ●         ↑         ↗
    │       \        |         |         |        /
    │        \       |    z-index:100    |       /
    │         \      |         |         |      /
    │    z:10  \  z:30        ▼       z:30  / z:10
    │           \    |   Full size    |    /
    │            \   |   No blur     |   /
    │             \  |   No rotate   |  /
    │              \ |               | /
    │               \|               |/
    │        rotateY(35°)     rotateY(-35°)
    │        blur(1-2px)       blur(1-2px)
    │        translateZ(-400 to -500px)
    │                                       │
    └───────────────────────────────────────┘
```

---

## Database Schema

### Tables

**wp_portfolio_items**
```sql
CREATE TABLE wp_portfolio_items (
    id            BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(255) NOT NULL,
    description   LONGTEXT,
    images        LONGTEXT,           -- Comma-separated URLs
    video_url     VARCHAR(500),       -- YouTube/Vimeo/file URL
    project_link  VARCHAR(500),       -- External project URL
    category_id   BIGINT(20) UNSIGNED,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**wp_portfolio_categories**
```sql
CREATE TABLE wp_portfolio_categories (
    id    BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(255) NOT NULL,
    slug  VARCHAR(255) NOT NULL UNIQUE
);
```

### Querying Data

```php
// Get all projects with categories
$items = $wpdb->get_results("
    SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM {$database->get_table_name()} p 
    LEFT JOIN {$database->get_categories_table()} c 
        ON p.category_id = c.id 
    ORDER BY p.created_at DESC
");
```

---

## AJAX Endpoints

All AJAX handlers are in `class-portfolio-ajax.php`.

### Available Actions

| Action | Method | Purpose |
|--------|--------|---------|
| `save_category` | POST | Create/update category |
| `delete_category` | POST | Delete category |
| `save_portfolio_item` | POST | Create/update project |
| `delete_portfolio_item` | POST | Delete project |
| `load_project_details` | POST | Get project for modal |

### Request Format

```javascript
$.ajax({
    url: portfolioAjax.ajaxurl,  // admin-ajax.php
    type: 'POST',
    data: {
        action: 'save_portfolio_item',
        nonce: portfolioAjax.nonce,
        // ... other fields
    },
    success: function(response) {
        if (response.success) {
            // Handle success
        }
    }
});
```

### Response Format

```javascript
// Success
{
    success: true,
    data: {
        message: "Project saved successfully",
        id: 123
    }
}

// Error
{
    success: false,
    data: {
        message: "Error message here"
    }
}
```

---

## Extending the Plugin

### Adding a Custom Field

**1. Update database schema** (`class-portfolio-database.php`):
```php
// Add column to CREATE TABLE statement
custom_field VARCHAR(255),
```

**2. Add form input** (`templates/admin-project-edit.php`):
```html
<div class="form-group">
    <label>Custom Field</label>
    <input type="text" name="custom_field" value="<?php echo esc_attr($item->custom_field ?? ''); ?>">
</div>
```

**3. Handle in AJAX** (`class-portfolio-ajax.php`):
```php
$custom_field = sanitize_text_field($_POST['custom_field']);
// Include in $data array for insert/update
```

**4. Display in template** (`templates/frontend-portfolio.php`):
```php
<?php echo esc_html($item->custom_field); ?>
```

### Adding New Carousel Positions

To add more visible slides (e.g., prev-3, next-3):

**1. Add CSS classes** (`frontend-clean.css`):
```css
.carousel-slide.prev-3 {
    transform: translate(-50%, -50%) translateX(-600px) translateZ(-600px) rotateY(40deg);
    z-index: 5;
    opacity: 0.5;
}
```

**2. Update JavaScript** (`frontend-clean.js`):
```javascript
case -3:
    $slide.addClass('prev-3');
    break;
```

### Adding Video Provider

To support a new video provider (e.g., TikTok):

**1. Update PHP helper** (`templates/frontend-portfolio.php`):
```php
// In portfolio_get_video_data()
if (strpos($video_url, 'tiktok.com') !== false) {
    preg_match('/video\/(\d+)/', $video_url, $matches);
    return array('type' => 'tiktok', 'url' => $matches[1]);
}
```

**2. Update JavaScript** (`frontend-clean.js`):
```javascript
// In playVideo()
else if (videoType === 'tiktok') {
    embedHTML = `<iframe src="https://www.tiktok.com/embed/v2/${videoId}"></iframe>`;
}
```

---

## Debugging

### Enable WordPress Debug Mode

In `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Logs write to `wp-content/debug.log`.

### Common Issues

**Carousel not animating:**
- Check if `perspective` is set on container
- Verify `transform-style: preserve-3d` on wrapper
- Check browser DevTools for CSS being overridden

**AJAX not working:**
- Verify nonce is passed correctly
- Check Network tab for 403/500 errors
- Ensure action hook is registered: `wp_ajax_action_name`

**Videos not playing:**
- Check console for iframe errors
- Verify video URL format is supported
- YouTube needs `?enablejsapi=1` for some features

### Useful Debug Snippets

```javascript
// Log carousel state
console.log('Current slide:', currentSlide);
console.log('Total slides:', totalSlides);
document.querySelectorAll('.carousel-slide').forEach((s, i) => {
    console.log(i, s.className);
});
```

```php
// Log AJAX data
error_log(print_r($_POST, true));
```

---

## Quick Reference

### Key Files to Edit

| Task | File(s) |
|------|---------|
| Change carousel look | `assets/css/frontend-clean.css` |
| Change carousel behavior | `assets/js/frontend-clean.js` |
| Change HTML structure | `templates/frontend-portfolio.php` |
| Add admin features | `includes/class-portfolio-admin.php` |
| Add AJAX endpoint | `includes/class-portfolio-ajax.php` |
| Change database | `includes/class-portfolio-database.php` |

### WordPress Hooks Used

```php
// Actions
add_action('admin_menu', ...)           // Add admin pages
add_action('admin_enqueue_scripts', ...)// Load admin assets
add_action('wp_enqueue_scripts', ...)   // Load frontend assets
add_action('wp_ajax_*', ...)            // AJAX handlers

// Filters
add_shortcode('modern_portfolio', ...)  // Register shortcode
```

---

## Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [CSS 3D Transforms](https://developer.mozilla.org/en-US/docs/Web/CSS/transform-function/perspective)
- [docs/3d-carousel-tutorial.md](docs/3d-carousel-tutorial.md) - Detailed carousel customization
