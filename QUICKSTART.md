# Quick Start Guide

## For End Users (Portfolio Management)

### Step 1: Install & Activate
1. Upload the plugin to `/wp-content/plugins/modern-portfolio-showcase/`
2. Activate "Modern Portfolio Showcase" in WordPress admin
3. Look for "Portfolio" in your WordPress admin menu

### Step 2: Create Categories
1. Go to **Portfolio > Categories**
2. Enter category name (e.g., "Web Development")
3. Slug auto-generates automatically
4. Click "Save Category"
5. Repeat for all your categories (e.g., "Mobile Apps", "Design", etc.)

### Step 3: Add Projects
1. Go to **Portfolio > Projects**
2. Click "Add New"
3. Fill in:
   - **Title**: Your project name
   - **Category**: Select from dropdown
   - **Description**: Use the editor to add details, formatting, colors
   - **Project Link**: URL to live project or demo
   - **Images**: Upload images (first = cover image)
4. Click "Create Project"

### Step 4: Display Portfolio
1. Create a new page or edit existing page
2. Add shortcode: `[modern_portfolio]`
3. Publish/Update the page
4. View your portfolio!

### Features Available to Users
- **Filter by Category**: Click category buttons on the sidebar
- **Switch Views**: Toggle between Slideshow and Grid view
- **View Details**: Click "View Details" for full project information
- **View Project**: Click "View Project" to visit the live site
- **Image Carousel**: Navigate through multiple project images

---

## For Developers (Customization)

### Quick Customization Cheat Sheet

#### Change Frontend Colors
File: `assets/css/frontend.css`
```css
/* Primary color (buttons, active states) */
.filter-btn.active,
.view-toggle-btn.active,
.portfolio-cta {
    background: #4f46e5; /* Change this */
}
```

#### Change Admin Colors
File: `admin/css/admin.css`
```css
.stat-content h3 {
    color: #2271b1; /* Change this */
}
```

#### Modify Slideshow Timing
File: `assets/js/frontend.js`
```javascript
const SLIDE_DURATION = 5000; // Change from 5 seconds
```

#### Add Custom Field to Project
1. Update database schema in `includes/class-portfolio-database.php`
2. Add form field in `templates/admin-project-edit.php`
3. Handle save in `includes/class-portfolio-ajax.php`
4. Display in `templates/frontend-portfolio.php`

#### Customize Shortcode Output
Edit: `templates/frontend-portfolio.php`
- Modify HTML structure
- Add/remove elements
- Change layout

#### Add JavaScript Functionality
- **Frontend**: Edit `assets/js/frontend.js`
- **Admin**: Edit `admin/js/admin.js`

### Common Customizations

**Hide Category Sidebar:**
```css
/* Add to assets/css/frontend.css */
.portfolio-sidebar {
    display: none;
}
.portfolio-main {
    width: 100%;
}
```

**Change Grid Columns:**
```css
/* Edit in assets/css/frontend.css */
.grid-view {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Adjust 300px */
}
```

**Disable Auto-Slide:**
```javascript
// Comment out in assets/js/frontend.js
// startAutoSlide();
```

### File Quick Reference
- Main plugin: `modern-portfolio-showcase.php`
- Frontend CSS: `assets/css/frontend.css`
- Frontend JS: `assets/js/frontend.js`
- Admin CSS: `admin/css/admin.css`
- Admin JS: `admin/js/admin.js`
- Portfolio display: `templates/frontend-portfolio.php`
- Database: `includes/class-portfolio-database.php`
- AJAX: `includes/class-portfolio-ajax.php`

### Need More Help?
- See `README.md` for full documentation
- See `DEVELOPMENT.md` for detailed development guide
- See `CHANGELOG.md` for version history
