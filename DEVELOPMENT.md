# Development Guide

## Getting Started

This guide will help you set up your development environment and understand how to work with the Modern Portfolio Showcase plugin.

## Prerequisites

- WordPress development environment (local or hosted)
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Basic knowledge of WordPress plugin development
- Text editor or IDE (VS Code, PhpStorm, etc.)

## Installation for Development

1. Clone this repository into your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone <repository-url> modern-portfolio-showcase
   ```

2. Activate the plugin in WordPress admin panel

3. Navigate to **Portfolio** menu to access the plugin

## Directory Structure Explained

### `/admin/`
Contains admin-specific assets that are only loaded in the WordPress admin panel.

- **`css/admin.css`**: Styles for admin interface (dashboard, categories, projects pages)
- **`js/admin.js`**: JavaScript for admin functionality (AJAX calls, form handling, media uploads)

### `/assets/`
Contains frontend assets that are loaded on public-facing pages where the portfolio is displayed.

- **`css/frontend.css`**: Styles for portfolio slideshow and grid views
- **`js/frontend.js`**: JavaScript for filtering, slideshow navigation, modals

### `/includes/`
Contains PHP classes with core plugin logic. These files handle all business logic and database operations.

- **`class-portfolio-database.php`**: Database table creation and management
- **`class-portfolio-admin.php`**: Admin menu, pages, and asset enqueuing
- **`class-portfolio-ajax.php`**: All AJAX request handlers
- **`class-portfolio-frontend.php`**: Frontend display and shortcode handling

### `/templates/`
Contains PHP template files that generate HTML output. These are separated from logic for better maintainability.

**Admin Templates:**
- `admin-dashboard.php`: Portfolio dashboard overview
- `admin-categories.php`: Category management interface
- `admin-projects-list.php`: Projects listing page
- `admin-project-edit.php`: Add/edit project form

**Frontend Templates:**
- `frontend-portfolio.php`: Portfolio display with slideshow and grid views

### `/examples/`
Contains example files and preview HTML for reference.

## Working on Different Aspects

### UI/Styling Work (HTML/CSS)

To work on the visual presentation:

1. **Frontend Portfolio Appearance:**
   - Edit `assets/css/frontend.css` for styles
   - Edit `templates/frontend-portfolio.php` for HTML structure
   - Changes reflect immediately when you refresh the page with the `[modern_portfolio]` shortcode

2. **Admin Interface Appearance:**
   - Edit `admin/css/admin.css` for admin styles
   - Edit template files in `templates/admin-*.php` for HTML structure
   - Refresh admin pages to see changes

### JavaScript/Interactivity Work

1. **Frontend JavaScript:**
   - Edit `assets/js/frontend.js`
   - Handles: filtering, slideshow navigation, image carousels, modals
   - Changes require page refresh (or use browser auto-reload extensions)

2. **Admin JavaScript:**
   - Edit `admin/js/admin.js`
   - Handles: AJAX operations, form submissions, media uploads
   - Changes require admin page refresh

### PHP Logic Work

1. **Database Operations:**
   - Edit `includes/class-portfolio-database.php`
   - Add/modify table structures, queries

2. **Admin Functionality:**
   - Edit `includes/class-portfolio-admin.php`
   - Add new admin pages, modify menu structure

3. **AJAX Handlers:**
   - Edit `includes/class-portfolio-ajax.php`
   - Add new AJAX endpoints, modify existing handlers

4. **Frontend Logic:**
   - Edit `includes/class-portfolio-frontend.php`
   - Modify shortcode output, add helper functions

## Development Workflow

### 1. Making Changes

```bash
# Create a new branch for your feature
git checkout -b feature/your-feature-name

# Make your changes to the appropriate files
# Test your changes in WordPress

# Commit your changes
git add .
git commit -m "Description of changes"

# Push to repository
git push origin feature/your-feature-name
```

### 2. Testing Changes

- **Frontend changes**: Create a test page with `[modern_portfolio]` shortcode
- **Admin changes**: Navigate to Portfolio menu in WordPress admin
- **Database changes**: Check tables in phpMyAdmin or similar tool

### 3. Debugging

Enable WordPress debugging in `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check debug log at `wp-content/debug.log`

## Common Tasks

### Adding a New Admin Page

1. Add menu item in `includes/class-portfolio-admin.php`
2. Create template file in `templates/admin-newpage.php`
3. Enqueue necessary CSS/JS if needed

### Adding a New AJAX Endpoint

1. Add action handler in `includes/class-portfolio-ajax.php`
2. Register action in `register_ajax_actions()` method
3. Add JavaScript handler in `admin/js/admin.js` or `assets/js/frontend.js`

### Modifying Portfolio Display

1. Edit HTML structure in `templates/frontend-portfolio.php`
2. Edit styles in `assets/css/frontend.css`
3. Edit behavior in `assets/js/frontend.js`

### Adding Database Fields

1. Update table schema in `includes/class-portfolio-database.php`
2. Add migration code in `create_tables()` method
3. Update save/retrieve methods in `includes/class-portfolio-ajax.php`
4. Update form fields in templates

## Best Practices

1. **Separation of Concerns**: Keep logic in classes, templates for display
2. **Security**: Always sanitize input, escape output, use nonces
3. **WordPress Standards**: Follow WordPress coding standards
4. **Documentation**: Comment complex logic, document functions
5. **Version Control**: Commit regularly with clear messages
6. **Testing**: Test changes in fresh WordPress installation

## File Naming Conventions

- Class files: `class-{name}.php`
- Template files: `{context}-{name}.php`
- CSS files: `{context}.css`
- JS files: `{context}.js`

## Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WordPress AJAX Documentation](https://codex.wordpress.org/AJAX_in_Plugins)

## Troubleshooting

### Plugin doesn't activate
- Check PHP error log
- Verify file permissions
- Check for syntax errors: `php -l filename.php`

### Changes not appearing
- Clear WordPress cache
- Check if files are in correct location
- Verify enqueue functions are called correctly

### AJAX not working
- Check browser console for errors
- Verify nonce is being passed correctly
- Check WordPress debug log for PHP errors

## Support

For issues or questions, please open an issue in the repository.
