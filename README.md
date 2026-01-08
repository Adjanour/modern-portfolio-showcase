# Modern Portfolio Showcase

A WordPress portfolio plugin featuring a 3D coverflow carousel, grid view, and video support.

## Features

- **3D Coverflow Carousel**: Stunning perspective-based carousel with smooth animations
- **Grid View**: Alternative layout with hover overlays
- **Video Support**: YouTube, Vimeo, and direct file uploads
- **Category Filtering**: Organize projects by categories
- **Responsive Design**: Mobile-friendly layouts
- **Easy Management**: Intuitive admin interface

## Quick Start

### Installation
1. Upload `modern-portfolio-showcase` to `/wp-content/plugins/`
2. Activate the plugin in WordPress admin
3. Navigate to **Portfolio** in admin menu

### Add Content
1. **Categories**: Portfolio → Categories → Add category name → Save
2. **Projects**: Portfolio → Projects → Add New → Fill details → Create

### Display Portfolio
Add this shortcode to any page:
```
[modern_portfolio]
```

## File Structure

```
modern-portfolio-showcase/
├── assets/
│   ├── css/
│   │   └── frontend-clean.css    # Frontend styles (production)
│   └── js/
│       └── frontend-clean.js     # Frontend scripts (production)
├── admin/
│   ├── css/admin.css             # Admin styles
│   └── js/admin.js               # Admin scripts
├── includes/
│   ├── class-portfolio-database.php
│   ├── class-portfolio-admin.php
│   ├── class-portfolio-ajax.php
│   └── class-portfolio-frontend.php
├── templates/
│   ├── frontend-portfolio.php    # Portfolio display template
│   └── admin-*.php               # Admin templates
├── docs/
│   └── 3d-carousel-tutorial.md   # Carousel customization guide
└── modern-portfolio-showcase.php # Main plugin file
```

## Customization

### Carousel Card Sizes

Navigate to **Portfolio → Settings** in the WordPress admin to access the customization panel.

### Settings Tabs

| Tab | Options |
|-----|---------|
| **Colors** | Primary color, hover color, overlay color, text colors |
| **Dimensions** | Card width/height, border radius, grid gap |
| **3D Effect** | Perspective, rotation angle, blur levels |
| **Animation** | Transition speed, auto-slide delay, enable/disable |
| **Display** | Show/hide title bar, play button, nav buttons, view toggle |

### Shortcode Overrides

Override settings per shortcode instance:

```php
[modern_portfolio primary_color="#e74c3c" card_width="600" auto_slide="false"]
```

### CSS Variables

The plugin uses CSS custom properties that can also be overridden manually:

```css
:root {
    --portfolio-primary: #4f46e5;
    --portfolio-primary-hover: #4338ca;
    --portfolio-card-width: 680px;
    --portfolio-card-height: 400px;
    --portfolio-border-radius: 12px;
    --portfolio-perspective: 1200px;
    --portfolio-rotate-angle: 35deg;
    --portfolio-transition: 0.6s;
}


## Development

### Frontend Files
| Purpose | File |
|---------|------|
| Styles | `assets/css/frontend-clean.css` |
| Scripts | `assets/js/frontend-clean.js` |
| Template | `templates/frontend-portfolio.php` |

### Admin Files
| Purpose | File |
|---------|------|
| Styles | `admin/css/admin.css` |
| Scripts | `admin/js/admin.js` |
| Templates | `templates/admin-*.php` |

### PHP Classes
| Class | Purpose |
|-------|---------|
| `Portfolio_Database` | Table creation, queries |
| `Portfolio_Admin` | Admin menus, pages |
| `Portfolio_Ajax` | AJAX handlers |
| `Portfolio_Frontend` | Shortcode, asset loading |
| `Portfolio_Settings` | Settings management, CSS variables |

### Adding Custom Fields

1. **Database**: Add column in `class-portfolio-database.php`
2. **Admin Form**: Add input in `templates/admin-project-edit.php`
3. **Save Handler**: Update `class-portfolio-ajax.php`
4. **Display**: Use in `templates/frontend-portfolio.php`

## Carousel Deep Dive

See [docs/3d-carousel-tutorial.md](docs/3d-carousel-tutorial.md) for:
- CSS 3D transform concepts
- Position class system (active, prev-1, next-1, etc.)
- Video integration details
- Advanced customization examples

### Position Classes

| Class | Position | Transform |
|-------|----------|-----------|
| `.active` | Center | `translateZ(0) rotateY(0)` |
| `.prev-1` | Left | `translateX(-400px) translateZ(-100px) rotateY(35deg)` |
| `.prev-2` | Far Left | `translateX(-520px) translateZ(-200px) rotateY(45deg)` |
| `.next-1` | Right | `translateX(400px) translateZ(-100px) rotateY(-35deg)` |
| `.next-2` | Far Right | `translateX(520px) translateZ(-200px) rotateY(-45deg)` |


## Browser Support

- Chrome, Firefox, Safari, Edge (latest versions)
- iOS Safari, Android Chrome


## License

GPL v2 or later

## Author

Orcta Technologies

## Version

2.1.0
