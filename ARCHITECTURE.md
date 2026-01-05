# Architecture Overview

## Plugin Structure Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                  WordPress Environment                       │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │      modern-portfolio-showcase.php (Main File)        │ │
│  │                                                        │ │
│  │  • Defines constants                                  │ │
│  │  • Requires class files                               │ │
│  │  • Initializes plugin                                 │ │
│  │  • Registers activation hook                          │ │
│  └──────────────┬─────────────────────────────────────────┘ │
│                 │                                            │
│                 │ Instantiates                               │
│                 ▼                                            │
│  ┌─────────────────────────────────────────────────────────┐│
│  │          Modern_Portfolio_Plugin (Core)                 ││
│  │                                                          ││
│  │  Creates instances:                                     ││
│  │  ├─ Portfolio_Database                                  ││
│  │  ├─ Portfolio_Admin                                     ││
│  │  ├─ Portfolio_Ajax                                      ││
│  │  └─ Portfolio_Frontend                                  ││
│  └──┬────────┬─────────┬────────┬────────────────────────┬─┘│
│     │        │         │        │                        │  │
│     │        │         │        │                        │  │
│     ▼        ▼         ▼        ▼                        ▼  │
├─────────────────────────────────────────────────────────────┤
│                      CLASS LAYER                             │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────────┐  ┌──────────────────┐                │
│  │   Database       │  │      Admin       │                │
│  │   (includes/)    │  │   (includes/)    │                │
│  │                  │  │                  │                │
│  │ • Create tables  │  │ • Add menus      │                │
│  │ • Get table names│  │ • Enqueue scripts│                │
│  │                  │  │ • Render pages   │                │
│  └──────────────────┘  └────────┬─────────┘                │
│                                  │                          │
│                                  │ Includes                 │
│                                  ▼                          │
│  ┌──────────────────┐  ┌──────────────────┐                │
│  │      AJAX        │  │    Frontend      │                │
│  │   (includes/)    │  │   (includes/)    │                │
│  │                  │  │                  │                │
│  │ • Handle saves   │  │ • Enqueue scripts│                │
│  │ • Handle deletes │  │ • Shortcode      │                │
│  │ • Get data       │  │ • Truncate text  │                │
│  └──────────────────┘  └────────┬─────────┘                │
│                                  │                          │
│                                  │ Includes                 │
│                                  ▼                          │
├─────────────────────────────────────────────────────────────┤
│                    TEMPLATE LAYER                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Admin Templates (templates/)  │  Frontend Template         │
│  ┌────────────────────────┐    │  ┌──────────────────────┐ │
│  │ admin-dashboard.php    │    │  │ frontend-portfolio   │ │
│  │ admin-categories.php   │    │  │      .php            │ │
│  │ admin-projects-list    │    │  │                      │ │
│  │      .php              │    │  │ • Displays portfolio │ │
│  │ admin-project-edit     │    │  │ • Slideshow view     │ │
│  │      .php              │    │  │ • Grid view          │ │
│  └────────────────────────┘    │  │ • Filters            │ │
│                                 │  │ • Modal              │ │
│                                 │  └──────────────────────┘ │
├─────────────────────────────────────────────────────────────┤
│                      ASSET LAYER                             │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Admin Assets (admin/)         │  Frontend Assets (assets/) │
│  ┌────────────────────────┐    │  ┌──────────────────────┐ │
│  │ css/admin.css          │    │  │ css/frontend.css     │ │
│  │ • Dashboard styles     │    │  │ • Portfolio styles   │ │
│  │ • Form styles          │    │  │ • Slideshow styles   │ │
│  │ • List styles          │    │  │ • Grid styles        │ │
│  │                        │    │  │ • Modal styles       │ │
│  │ js/admin.js            │    │  │                      │ │
│  │ • Category CRUD        │    │  │ js/frontend.js       │ │
│  │ • Project CRUD         │    │  │ • Filtering          │ │
│  │ • Media upload         │    │  │ • Slideshow nav      │ │
│  │ • Form handling        │    │  │ • View toggle        │ │
│  └────────────────────────┘    │  │ • Carousel           │ │
│                                 │  └──────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

## Data Flow

### Frontend Display Flow
```
User visits page
      ↓
WordPress processes [modern_portfolio] shortcode
      ↓
Portfolio_Frontend::portfolio_shortcode() called
      ↓
Templates/frontend-portfolio.php included
      ↓
Database queried for projects and categories
      ↓
HTML rendered with project data
      ↓
assets/css/frontend.css styles the output
      ↓
assets/js/frontend.js adds interactivity
      ↓
User sees portfolio (slideshow/grid)
```

### Admin Save Flow
```
Admin edits project
      ↓
Fills form in templates/admin-project-edit.php
      ↓
Clicks "Save" → admin/js/admin.js intercepts
      ↓
AJAX request to admin-ajax.php
      ↓
Portfolio_Ajax::save_portfolio_item() processes
      ↓
Data validated and sanitized
      ↓
Database updated via global $wpdb
      ↓
Success response sent back
      ↓
Page refreshed/redirected
```

### Category Filter Flow
```
User clicks category filter
      ↓
assets/js/frontend.js handles click
      ↓
JavaScript filters visible items by data-tag
      ↓
Slideshow/grid updates with CSS transitions
      ↓
Active category button highlighted
```

## Component Dependencies

```
Modern_Portfolio_Plugin
├── Requires: Portfolio_Database
│   └── Used by: All other classes
│
├── Requires: Portfolio_Admin
│   ├── Uses: Portfolio_Database
│   └── Includes: Admin templates
│
├── Requires: Portfolio_Ajax
│   ├── Uses: Portfolio_Database
│   └── Processes: AJAX requests
│
└── Requires: Portfolio_Frontend
    ├── Uses: Portfolio_Database
    ├── Includes: Frontend template
    └── Enqueues: Frontend assets
```

## File Interaction Map

```
PHP Classes (includes/)
    ↓ include
Templates (templates/)
    ↓ reference
Assets (admin/ & assets/)
    ↓ style/interact
User Interface
```

## Development Workflow

```
┌──────────────────────────────────────────┐
│         Working on UI/Styling?           │
│                                          │
│  Edit: assets/css/* or admin/css/*       │
│  Test: Refresh page                      │
│  No PHP knowledge needed                 │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│      Working on Interactivity?           │
│                                          │
│  Edit: assets/js/* or admin/js/*         │
│  Test: Refresh page                      │
│  Basic JavaScript knowledge              │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│       Working on PHP Logic?              │
│                                          │
│  Edit: includes/class-*.php              │
│  Test: Perform action in WordPress       │
│  PHP & WordPress knowledge needed        │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│      Working on HTML Structure?          │
│                                          │
│  Edit: templates/*.php                   │
│  Test: Refresh relevant page             │
│  HTML & basic PHP knowledge              │
└──────────────────────────────────────────┘
```

## Key Principles

1. **Separation of Concerns**: Each component has one job
2. **Single Responsibility**: Each class handles one aspect
3. **Template Pattern**: Logic separate from presentation
4. **WordPress Standards**: Follows WP coding practices
5. **Modular Design**: Easy to extend and maintain

## Benefits of This Architecture

✅ **For UI Designers**: Work in `assets/` and `templates/` without touching complex PHP
✅ **For JavaScript Devs**: Work in `assets/js/` and `admin/js/` independently
✅ **For PHP Developers**: Work in `includes/` with clean class structure
✅ **For Integration**: Clean plugin structure makes WordPress integration simple
✅ **For Maintenance**: Find and fix issues quickly in organized codebase
✅ **For Testing**: Test components in isolation
✅ **For Documentation**: Easy to document and understand

## Security Layers

```
User Input
    ↓
WordPress Nonce Verification
    ↓
Capability Check (manage_options)
    ↓
Data Sanitization (sanitize_text_field, etc.)
    ↓
Database Query (prepared statements)
    ↓
Output Escaping (esc_html, esc_url, etc.)
    ↓
Displayed to User
```

Every layer ensures security at different levels, following WordPress best practices.
