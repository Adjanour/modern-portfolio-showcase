# Modern Portfolio Showcase

A clean and modern WordPress portfolio plugin with filtering, slideshow and grid views.

## Features

- **Beautiful Portfolio Display**: Showcase your projects with slideshow and grid views
- **Category Filtering**: Organize projects by categories with smooth filtering
- **Image Carousels**: Support for multiple images per project with carousel functionality
- **Rich Text Editor**: Full WYSIWYG editor for project descriptions
- **Responsive Design**: Mobile-friendly and adaptable to all screen sizes
- **Easy Management**: Intuitive admin interface for managing projects and categories

## Directory Structure

```
modern-portfolio-showcase/
├── admin/                      # Admin-specific assets
│   ├── css/                   # Admin styles
│   └── js/                    # Admin scripts
├── assets/                    # Frontend assets
│   ├── css/                   # Frontend styles
│   └── js/                    # Frontend scripts
├── includes/                  # PHP classes and core logic
│   ├── class-portfolio-database.php
│   ├── class-portfolio-admin.php
│   ├── class-portfolio-ajax.php
│   └── class-portfolio-frontend.php
├── templates/                 # Template files
│   ├── admin-dashboard.php
│   ├── admin-categories.php
│   ├── admin-projects-list.php
│   ├── admin-project-edit.php
│   └── frontend-portfolio.php
├── examples/                  # Example files and previews
│   └── preview.html
├── modern-portfolio-showcase.php  # Main plugin file
├── .gitignore
└── README.md
```

## Installation

1. Download or clone this repository
2. Upload the `modern-portfolio-showcase` folder to your WordPress `wp-content/plugins/` directory
3. Activate the plugin through the WordPress admin panel
4. Navigate to **Portfolio** in the admin menu to start managing your projects

## Usage

### Managing Categories

1. Go to **Portfolio > Categories** in the admin menu
2. Enter a category name (slug will be auto-generated)
3. Click "Save Category"

### Adding Projects

1. Go to **Portfolio > Projects** in the admin menu
2. Click "Add New"
3. Fill in the project details:
   - **Title**: Your project name
   - **Category**: Select a category
   - **Description**: Use the rich text editor for detailed descriptions
   - **Project Link**: URL to the live project
   - **Images**: Upload one or more images (first image becomes the cover)
4. Click "Create Project"

### Displaying the Portfolio

Add the shortcode to any page or post:

```
[modern_portfolio]
```

## Development

### Working on UI (HTML/CSS/JS)

All frontend UI files are organized separately:

- **HTML Templates**: `templates/frontend-portfolio.php`
- **CSS**: `assets/css/frontend.css`
- **JavaScript**: `assets/js/frontend.js`

### Working on Admin Interface

Admin interface files are in dedicated directories:

- **HTML Templates**: `templates/admin-*.php`
- **CSS**: `admin/css/admin.css`
- **JavaScript**: `admin/js/admin.js`

### Working on PHP Logic

PHP classes are modular and organized in the `includes/` directory:

- **Database Operations**: `includes/class-portfolio-database.php`
- **Admin Functions**: `includes/class-portfolio-admin.php`
- **AJAX Handlers**: `includes/class-portfolio-ajax.php`
- **Frontend Functions**: `includes/class-portfolio-frontend.php`

### File References

All file paths are now properly structured:
- CSS/JS files are loaded via plugin directory URL
- Templates are included via absolute paths
- No hardcoded paths in the codebase

## Customization

### Styling

- Frontend styles: Edit `assets/css/frontend.css`
- Admin styles: Edit `admin/css/admin.css`

### Functionality

- Modify shortcode output: Edit `templates/frontend-portfolio.php`
- Customize admin pages: Edit templates in `templates/admin-*.php`
- Add new features: Extend classes in `includes/`

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## License

GPL v2 or later

## Author

Your Name

## Version

1.0.0
