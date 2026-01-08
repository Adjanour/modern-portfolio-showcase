# Modern Portfolio Showcase - Landing Site

A static landing page for the Modern Portfolio Showcase WordPress plugin, built with vanilla HTML, CSS, and JavaScript.

## 🚀 Deploying to Vercel

### Option 1: Vercel CLI

```bash
# Install Vercel CLI globally
npm install -g vercel

# Navigate to the site folder
cd site

# Deploy
vercel
```

### Option 2: GitHub Integration

1. Push this `site` folder to a GitHub repository
2. Go to [vercel.com](https://vercel.com)
3. Click "New Project"
4. Import your GitHub repository
5. Set the root directory to `/site` (or wherever you placed the files)
6. Click "Deploy"

### Option 3: Drag & Drop

1. Go to [vercel.com](https://vercel.com)
2. Drag the entire `site` folder into the browser
3. Done!

## 📁 Structure

```
site/
├── index.html          # Main landing page
├── css/
│   └── style.css       # All styles
├── js/
│   └── main.js         # Interactive features
├── images/             # Demo images (add your own)
├── vercel.json         # Vercel configuration
└── README.md           # This file
```

## 🎨 Customization

### Colors
Edit the CSS variables in `css/style.css`:

```css
:root {
    --primary: #4f46e5;        /* Main brand color */
    --primary-dark: #4338ca;   /* Hover states */
    --primary-light: #818cf8;  /* Accents */
    --secondary: #0ea5e9;      /* Secondary color */
}
```

### Demo Images
Replace placeholder images in the carousel with your own screenshots:
1. Add images to `images/` folder
2. Update the `src` attributes in `index.html`

Recommended image sizes:
- Carousel slides: 800x600px
- Optimized WebP format preferred

### Content
Edit `index.html` to update:
- Hero text and statistics
- Feature descriptions
- Documentation links
- Footer content

## ✨ Features

- **Responsive Design**: Works on all devices
- **Interactive Carousel**: Real 3D coverflow demo
- **Smooth Animations**: Scroll-triggered reveals
- **Keyboard Navigation**: Arrow keys for carousel
- **Performance Optimized**: Minimal dependencies

## 🔧 Local Development

Just open `index.html` in a browser, or use a local server:

```bash
# Using Python
python -m http.server 8000

# Using Node.js
npx serve

# Using PHP
php -S localhost:8000
```

## 📦 Production Checklist

- [ ] Replace placeholder images with real screenshots
- [ ] Update meta description in `<head>`
- [ ] Add Open Graph tags for social sharing
- [ ] Update links to actual WordPress.org plugin page
- [ ] Add analytics (Vercel Analytics or Google Analytics)
- [ ] Test on multiple browsers and devices

## 📄 License

MIT License - Feel free to use and modify.
