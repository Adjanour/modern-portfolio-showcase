# Modern Portfolio Showcase

A WordPress portfolio plugin featuring a 3D coverflow carousel, grid view, and video support.

## Features

- **3D Coverflow Carousel**: Stunning perspective-based carousel with smooth animations
- **Grid View**: Alternative layout with hover overlays
- **Video Support**: YouTube, Vimeo, and direct file uploads
- **Category Filtering**: Organize projects by categories
- **Responsive Design**: Mobile-friendly layouts
- **Easy Management**: Intuitive admin interface

---

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

---

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

---

## Customization

### Carousel Card Sizes

Edit `assets/css/frontend-clean.css`:

```css
/* Base card dimensions (Section 4) */
.carousel-slide {
    width: 680px;     /* Card width */
    height: 400px;    /* Card height */
}

/* Side card scaling (Section 5) */
.carousel-slide.prev-1 {
    transform: translate(-50%, -50%) translateX(-400px) scale(0.7) rotateY(35deg);
    /*                                                   ↑ Scale: 0.7 = 70% size */
}
```

### 3D Effect Depth

```css
/* Container perspective (Section 3) */
.carousel-3d-container {
    perspective: 1200px;  /* Lower = more dramatic 3D */
}

/* Card rotation angle */
.carousel-slide.prev-1 {
    transform: ... rotateY(35deg);  /* Higher = more angled */
}
```

### Auto-Slide Timing

Edit `assets/js/frontend-clean.js`:

```javascript
// Configuration section
const CONFIG = {
    autoSlideDelay: 5000,  // 5 seconds between slides
};
```

### Colors

```css
/* Primary accent color */
.simple-toggle-btn.active,
.portfolio-cta,
.carousel-dot.active {
    background: #4f46e5;  /* Indigo */
}
```

---

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

### Adding Custom Fields

1. **Database**: Add column in `class-portfolio-database.php`
2. **Admin Form**: Add input in `templates/admin-project-edit.php`
3. **Save Handler**: Update `class-portfolio-ajax.php`
4. **Display**: Use in `templates/frontend-portfolio.php`

---

## Carousel Deep Dive

See [docs/3d-carousel-tutorial.md](docs/3d-carousel-tutorial.md) for:
- CSS 3D transform concepts
- Position class system (active, prev-1, next-1, etc.)
- Video integration details
- Advanced customization examples

### Position Classes

| Class | Position | Transform |
|-------|----------|-----------|
| `.active` | Center | `scale(1) rotateY(0)` |
| `.prev-1` | Left | `translateX(-400px) scale(0.7) rotateY(35deg)` |
| `.prev-2` | Far Left | `translateX(-520px) scale(0.55) rotateY(45deg)` |
| `.next-1` | Right | `translateX(400px) scale(0.7) rotateY(-35deg)` |
| `.next-2` | Far Right | `translateX(520px) scale(0.55) rotateY(-45deg)` |

---

## Browser Support

- Chrome, Firefox, Safari, Edge (latest versions)
- iOS Safari, Android Chrome

---

## License

GPL v2 or later

## Author

Orcta Technologies

## Version

2.0.0
