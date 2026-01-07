# 3D Carousel Portfolio Tutorial

A comprehensive guide to building a modern 3D carousel with video support for WordPress.

---

## Table of Contents

1. [Introduction](#introduction)
2. [Core Concepts](#core-concepts)
3. [HTML Structure](#html-structure)
4. [CSS 3D Transforms](#css-3d-transforms)
5. [JavaScript Carousel Logic](#javascript-carousel-logic)
6. [Video Integration](#video-integration)
7. [WordPress Integration](#wordpress-integration)
8. [Best Practices](#best-practices)
9. [Resources & Links](#resources--links)

---

## Introduction

This tutorial teaches you how to build a **3D perspective carousel** that:
- Displays portfolio items in a visually engaging 3D space
- Supports images and videos (YouTube, Vimeo, uploaded files)
- Uses CSS 3D transforms for smooth animations
- Integrates with WordPress for dynamic content

### What You'll Learn

- CSS `perspective` and `transform-style: preserve-3d`
- 3D transformations: `rotateY()`, `translateX()`, `translateZ()`, `scale()`
- JavaScript carousel navigation logic
- Embedding videos from multiple sources
- WordPress plugin development basics

---

## Core Concepts

### CSS 3D Transform Space

The key to 3D effects in CSS is understanding the coordinate system:

```
        Y (up/down)
        │
        │
        │
        └───────── X (left/right)
       /
      /
     Z (towards viewer)
```

**Key Properties:**

| Property | Purpose |
|----------|---------|
| `perspective` | Sets the distance from viewer to z=0 plane |
| `perspective-origin` | Sets the vanishing point |
| `transform-style: preserve-3d` | Allows children to exist in 3D space |
| `transform` | Applies 3D transformations |

### The Perspective Concept

Think of `perspective` like a camera lens:

```
Low perspective (e.g., 500px)     High perspective (e.g., 2000px)
┌─────────────────────┐           ┌─────────────────────┐
│     ╱───────╲       │           │     ┌───────┐       │
│   ╱           ╲     │           │     │       │       │
│  │   SLIDE    │     │           │     │ SLIDE │       │
│   ╲           ╱     │           │     │       │       │
│     ╲───────╱       │           │     └───────┘       │
└─────────────────────┘           └─────────────────────┘
   More dramatic 3D                   Flatter appearance
```

---

## HTML Structure

### Basic Carousel Markup

```html
<div class="portfolio-carousel-wrapper">
  <!-- Navigation Arrows -->
  <button class="carousel-arrow carousel-prev">
    <svg><!-- Left arrow icon --></svg>
  </button>
  
  <!-- 3D Carousel Container -->
  <div class="portfolio-carousel">
    <div class="carousel-track">
      
      <!-- Individual Slides -->
      <div class="carousel-slide" data-index="0">
        <div class="slide-media">
          <img src="image.jpg" alt="Project Title">
          
          <!-- Video overlay (if video exists) -->
          <div class="slide-video-container">
            <button class="slide-play-btn">▶</button>
          </div>
        </div>
        <div class="slide-info">
          <h3>Project Title</h3>
          <p>Description</p>
        </div>
      </div>
      
      <!-- More slides... -->
      
    </div>
  </div>
  
  <button class="carousel-arrow carousel-next">
    <svg><!-- Right arrow icon --></svg>
  </button>
</div>
```

### Data Attributes for Video

```html
<div class="slide-video-container" 
     data-video-type="youtube"
     data-video-id="dQw4w9WgXcQ">
```

| Attribute | Values | Purpose |
|-----------|--------|---------|
| `data-video-type` | `youtube`, `vimeo`, `file` | Determines how to embed |
| `data-video-id` | Video ID or URL | The video source |

---

## CSS 3D Transforms

### Container Setup

```css
/* Wrapper provides perspective for all children */
.portfolio-carousel-wrapper {
  position: relative;
  padding: 60px 80px;
  /* Creates the 3D viewing context */
  perspective: 1200px;
  perspective-origin: center center;
}

/* The rotating container */
.portfolio-carousel {
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
  /* Allow children to be positioned in 3D */
  transform-style: preserve-3d;
}

/* Track holds all slides */
.carousel-track {
  position: relative;
  width: 100%;
  height: 500px;
  /* Critical: maintains 3D space */
  transform-style: preserve-3d;
}
```

### Slide Positioning

Each slide is positioned using 3D transforms based on its position relative to the active slide:

```css
.carousel-slide {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 700px;
  height: 420px;
  
  /* Center the slide */
  margin-left: -350px;
  margin-top: -210px;
  
  /* Smooth transitions */
  transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1),
              opacity 0.6s ease,
              z-index 0s 0.3s;
  
  /* Default: hidden */
  opacity: 0;
  pointer-events: none;
}
```

### Slide States

```css
/* Active slide - front and center */
.carousel-slide.active {
  transform: translateX(0) translateZ(100px) scale(1);
  opacity: 1;
  z-index: 10;
  pointer-events: auto;
}

/* Previous slide - to the left */
.carousel-slide.prev {
  transform: translateX(-450px) translateZ(-100px) scale(0.8);
  opacity: 0.6;
  z-index: 5;
}

/* Next slide - to the right */
.carousel-slide.next {
  transform: translateX(450px) translateZ(-100px) scale(0.8);
  opacity: 0.6;
  z-index: 5;
}

/* Far slides - more distant */
.carousel-slide.far-prev {
  transform: translateX(-700px) translateZ(-200px) scale(0.6);
  opacity: 0.3;
  z-index: 1;
}

.carousel-slide.far-next {
  transform: translateX(700px) translateZ(-200px) scale(0.6);
  opacity: 0.3;
  z-index: 1;
}
```

### Visual Diagram of Transform Values

```
                    Active
                   Z: +100px
                   Scale: 1.0
                   Opacity: 1
                      │
    Prev              │              Next
  X: -450px           │            X: +450px
  Z: -100px      ┌────┴────┐       Z: -100px
  Scale: 0.8     │         │       Scale: 0.8
                 │ ACTIVE  │
  ┌─────┐        │  SLIDE  │        ┌─────┐
  │PREV │        │         │        │NEXT │
  │     │        └─────────┘        │     │
  └─────┘                           └─────┘
     │                                 │
     │                                 │
  ┌──┴──┐                           ┌──┴──┐
  │ FAR │   X: -700px    X: +700px  │ FAR │
  │PREV │   Z: -200px    Z: -200px  │NEXT │
  └─────┘   Scale: 0.6   Scale: 0.6 └─────┘
```

### Cubic Bezier Easing

The transition uses a custom easing function:

```css
transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
```

This creates a "ease-out-quint" effect - fast start, slow end. Visualize it:

```
Position
   │    ╭────────────────
   │   ╱
   │  ╱
   │ ╱
   │╱
   └─────────────────────── Time
   Fast acceleration, gentle deceleration
```

**Tool:** [cubic-bezier.com](https://cubic-bezier.com/#.23,1,.32,1) - Interactive easing editor

---

## JavaScript Carousel Logic

### Core Variables

```javascript
const track = document.querySelector('.carousel-track');
const slides = document.querySelectorAll('.carousel-slide');
const prevBtn = document.querySelector('.carousel-prev');
const nextBtn = document.querySelector('.carousel-next');

let currentIndex = 0;
const totalSlides = slides.length;
```

### Update Slides Function

```javascript
function updateSlides() {
  slides.forEach((slide, index) => {
    // Remove all state classes
    slide.classList.remove('active', 'prev', 'next', 'far-prev', 'far-next');
    
    // Calculate position relative to current
    let diff = index - currentIndex;
    
    // Handle wrapping for infinite loop
    if (diff > totalSlides / 2) diff -= totalSlides;
    if (diff < -totalSlides / 2) diff += totalSlides;
    
    // Apply appropriate class based on position
    if (diff === 0) {
      slide.classList.add('active');
    } else if (diff === -1) {
      slide.classList.add('prev');
    } else if (diff === 1) {
      slide.classList.add('next');
    } else if (diff === -2) {
      slide.classList.add('far-prev');
    } else if (diff === 2) {
      slide.classList.add('far-next');
    }
  });
  
  // Stop any playing videos
  stopAllMedia();
}
```

### Navigation Functions

```javascript
function goToNext() {
  currentIndex = (currentIndex + 1) % totalSlides;
  updateSlides();
}

function goToPrev() {
  currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
  updateSlides();
}

// Event listeners
prevBtn.addEventListener('click', goToPrev);
nextBtn.addEventListener('click', goToNext);

// Keyboard navigation
document.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowLeft') goToPrev();
  if (e.key === 'ArrowRight') goToNext();
});
```

### Position Calculation Explained

```javascript
// For 5 slides, currentIndex = 0
// Slide 0: diff = 0 → active
// Slide 1: diff = 1 → next
// Slide 2: diff = 2 → far-next
// Slide 3: diff = 3 → wraps to -2 → far-prev
// Slide 4: diff = 4 → wraps to -1 → prev

// Wrapping logic:
if (diff > totalSlides / 2) diff -= totalSlides;
// 3 > 2.5, so 3 - 5 = -2 (far-prev)
```

---

## Video Integration

### Detecting Video Type (PHP)

```php
function get_video_data($url) {
  if (empty($url)) return null;
  
  // YouTube patterns
  if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches) ||
      preg_match('/youtu\.be\/([^?]+)/', $url, $matches) ||
      preg_match('/youtube\.com\/embed\/([^?]+)/', $url, $matches)) {
    return [
      'type' => 'youtube',
      'id' => $matches[1]
    ];
  }
  
  // Vimeo patterns
  if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches) ||
      preg_match('/player\.vimeo\.com\/video\/(\d+)/', $url, $matches)) {
    return [
      'type' => 'vimeo',
      'id' => $matches[1]
    ];
  }
  
  // Direct video files
  $video_extensions = ['mp4', 'webm', 'ogg', 'mov', 'm4v'];
  $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
  if (in_array($extension, $video_extensions)) {
    return [
      'type' => 'file',
      'id' => $url
    ];
  }
  
  return null;
}
```

### JavaScript Video Playback

```javascript
function playVideo(container) {
  const type = container.dataset.videoType;
  const id = container.dataset.videoId;
  const playBtn = container.querySelector('.slide-play-btn');
  
  let videoElement;
  
  switch(type) {
    case 'youtube':
      videoElement = document.createElement('iframe');
      videoElement.src = `https://www.youtube.com/embed/${id}?autoplay=1&rel=0`;
      videoElement.allow = 'autoplay; encrypted-media';
      videoElement.allowFullscreen = true;
      break;
      
    case 'vimeo':
      videoElement = document.createElement('iframe');
      videoElement.src = `https://player.vimeo.com/video/${id}?autoplay=1`;
      videoElement.allow = 'autoplay; fullscreen';
      break;
      
    case 'file':
      videoElement = document.createElement('video');
      videoElement.src = id;
      videoElement.controls = true;
      videoElement.autoplay = true;
      break;
  }
  
  // Hide play button, show video
  playBtn.style.display = 'none';
  container.appendChild(videoElement);
  container.classList.add('playing');
}

function stopAllMedia() {
  document.querySelectorAll('.slide-video-container.playing').forEach(container => {
    const iframe = container.querySelector('iframe');
    const video = container.querySelector('video');
    
    if (iframe) iframe.remove();
    if (video) {
      video.pause();
      video.remove();
    }
    
    container.classList.remove('playing');
    container.querySelector('.slide-play-btn').style.display = 'flex';
  });
}
```

### Video Container CSS

```css
.slide-video-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.3);
}

.slide-play-btn {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  font-size: 32px;
  cursor: pointer;
  transition: transform 0.3s, background 0.3s;
}

.slide-play-btn:hover {
  transform: scale(1.1);
  background: #fff;
}

/* When video is playing */
.slide-video-container.playing {
  background: #000;
}

.slide-video-container iframe,
.slide-video-container video {
  width: 100%;
  height: 100%;
  border: none;
}
```

---

## WordPress Integration

### Plugin Structure

```
modern-portfolio-showcase/
├── modern-portfolio-showcase.php   # Main plugin file
├── includes/
│   ├── class-portfolio-database.php  # Database operations
│   ├── class-portfolio-ajax.php      # AJAX handlers
│   └── class-portfolio-admin.php     # Admin functionality
├── templates/
│   ├── frontend-portfolio.php        # Frontend template
│   └── admin-project-edit.php        # Admin form
├── assets/
│   ├── css/
│   │   └── frontend.css
│   └── js/
│       └── frontend.js
└── admin/
    ├── css/
    │   └── admin.css
    └── js/
        └── admin.js
```

### Enqueueing Scripts & Styles

```php
// Frontend assets
function enqueue_frontend_assets() {
  wp_enqueue_style(
    'portfolio-frontend',
    plugin_dir_url(__FILE__) . 'assets/css/frontend.css',
    [],
    '1.0.0'
  );
  
  wp_enqueue_script(
    'portfolio-frontend',
    plugin_dir_url(__FILE__) . 'assets/js/frontend.js',
    ['jquery'],
    '1.0.0',
    true // Load in footer
  );
}
add_action('wp_enqueue_scripts', 'enqueue_frontend_assets');
```

### WordPress Media Uploader

```javascript
// In admin.js
function setupVideoUpload() {
  const uploadBtn = document.getElementById('upload-video-btn');
  const urlInput = document.getElementById('project-video-url');
  
  if (!uploadBtn) return;
  
  uploadBtn.addEventListener('click', function(e) {
    e.preventDefault();
    
    // Create WordPress media frame
    const frame = wp.media({
      title: 'Select or Upload Video',
      button: { text: 'Use this video' },
      library: { type: 'video' }, // Filter to videos only
      multiple: false
    });
    
    // When video is selected
    frame.on('select', function() {
      const attachment = frame.state().get('selection').first().toJSON();
      urlInput.value = attachment.url;
      updateVideoPreview(attachment.url);
    });
    
    frame.open();
  });
}

// Don't forget to enqueue WordPress media scripts
function enqueue_admin_scripts($hook) {
  wp_enqueue_media(); // Required for media uploader
  wp_enqueue_script('portfolio-admin', ...);
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
```

### Database Schema

```php
// In class-portfolio-database.php
function create_tables() {
  global $wpdb;
  
  $table_name = $wpdb->prefix . 'portfolio_items';
  $charset_collate = $wpdb->get_charset_collate();
  
  $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    description text,
    thumbnail_url varchar(500),
    video_url varchar(500),
    project_url varchar(500),
    display_order int DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
  ) $charset_collate;";
  
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}
```

---

## Best Practices

### Performance

1. **Use `will-change` sparingly**
   ```css
   .carousel-slide {
     will-change: transform, opacity;
   }
   ```

2. **Hardware acceleration**
   ```css
   .carousel-slide {
     transform: translateZ(0); /* Creates new layer */
     backface-visibility: hidden;
   }
   ```

3. **Lazy load videos** - Only load iframe when play button clicked

4. **Optimize images** - Use appropriate sizes for thumbnails

### Accessibility

```html
<!-- Add ARIA labels -->
<button class="carousel-prev" aria-label="Previous slide">
  <svg aria-hidden="true">...</svg>
</button>

<!-- Add role and live region -->
<div class="carousel-track" role="region" aria-label="Portfolio carousel">
  <div class="carousel-slide" role="group" aria-roledescription="slide">
```

### Responsive Design

```css
@media (max-width: 900px) {
  .carousel-slide {
    width: 90%;
    max-width: 400px;
    margin-left: -45%;
  }
  
  .carousel-slide.prev,
  .carousel-slide.next {
    transform: translateX(-60%) translateZ(-50px) scale(0.7);
  }
}

@media (max-width: 600px) {
  .portfolio-carousel-wrapper {
    perspective: 800px;
    padding: 40px 20px;
  }
  
  /* Hide side slides on mobile */
  .carousel-slide.far-prev,
  .carousel-slide.far-next {
    display: none;
  }
}
```

---

## Resources & Links

### CSS 3D Transforms

| Resource | Description |
|----------|-------------|
| [MDN: CSS Transforms](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_transforms) | Complete reference |
| [MDN: perspective](https://developer.mozilla.org/en-US/docs/Web/CSS/perspective) | Perspective property |
| [CSS-Tricks: 3D Transforms](https://css-tricks.com/how-css-perspective-works/) | Visual explanation |
| [Intro to CSS 3D Transforms](https://3dtransforms.desandro.com/) | Interactive tutorial by David DeSandro |

### CSS Animations & Transitions

| Resource | Description |
|----------|-------------|
| [cubic-bezier.com](https://cubic-bezier.com/) | Easing function visualizer |
| [easings.net](https://easings.net/) | Easing function examples |
| [MDN: CSS Transitions](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_transitions) | Transition reference |

### Video Embedding

| Resource | Description |
|----------|-------------|
| [YouTube IFrame API](https://developers.google.com/youtube/iframe_api_reference) | Official YouTube embed docs |
| [Vimeo Player API](https://developer.vimeo.com/player/sdk) | Vimeo embed documentation |
| [HTML5 Video](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/video) | Native video element |

### WordPress Development

| Resource | Description |
|----------|-------------|
| [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/) | Official plugin guide |
| [WordPress JavaScript](https://developer.wordpress.org/plugins/javascript/) | JS in WordPress |
| [wp.media() Reference](https://developer.wordpress.org/plugins/javascript/media/) | Media uploader API |
| [AJAX in Plugins](https://developer.wordpress.org/plugins/javascript/ajax/) | WordPress AJAX |

### Tools

| Tool | Purpose |
|------|---------|
| [Chrome DevTools 3D View](https://developer.chrome.com/docs/devtools/css/3d-view/) | Visualize 3D layers |
| [Firefox 3D Inspector](https://firefox-source-docs.mozilla.org/devtools-user/3d_view/) | Debug 3D transforms |
| [CodePen](https://codepen.io/) | Prototype and test |

### Inspiration & Examples

| Link | Description |
|------|-------------|
| [Codrops Tutorials](https://tympanus.net/codrops/category/tutorials/) | Creative CSS tutorials |
| [CSS Design Awards](https://cssdesignawards.com/) | Award-winning designs |
| [Awwwards](https://awwwards.com/) | Web design inspiration |

---

## Practice Exercises

### Exercise 1: Basic 3D Card
Create a single card that rotates on hover:
```css
.card {
  transform-style: preserve-3d;
  transition: transform 0.6s;
}
.card:hover {
  transform: rotateY(180deg);
}
```

### Exercise 2: Add Touch Support
Implement touch/swipe navigation:
```javascript
let startX;
carousel.addEventListener('touchstart', e => startX = e.touches[0].clientX);
carousel.addEventListener('touchend', e => {
  const diff = startX - e.changedTouches[0].clientX;
  if (diff > 50) goToNext();
  if (diff < -50) goToPrev();
});
```

### Exercise 3: Auto-Play
Add automatic slide advancement:
```javascript
let autoplayInterval = setInterval(goToNext, 5000);
carousel.addEventListener('mouseenter', () => clearInterval(autoplayInterval));
carousel.addEventListener('mouseleave', () => {
  autoplayInterval = setInterval(goToNext, 5000);
});
```

---

## Summary

You've learned how to build a complete 3D carousel with:

✅ CSS 3D transforms (`perspective`, `rotateY`, `translateZ`)  
✅ Smooth cubic-bezier transitions  
✅ JavaScript navigation with infinite loop  
✅ Multi-source video embedding (YouTube, Vimeo, files)  
✅ WordPress plugin integration  
✅ Accessibility and responsive design  

Keep practicing and experimenting with the transform values to create your own unique carousel designs!

---

*Created as part of the Modern Portfolio Showcase WordPress Plugin*
