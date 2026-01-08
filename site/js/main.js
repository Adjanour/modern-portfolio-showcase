/**
 * Modern Portfolio Showcase - Landing Page JavaScript
 * Interactive carousel demo, tabs, and navigation
 */

// =================================
// Carousel Demo
// =================================
class CarouselDemo {
    constructor(container) {
        this.container = container;
        this.slides = container.querySelectorAll('.carousel-slide');
        this.dots = container.querySelectorAll('.carousel-dot');
        this.prevBtn = container.querySelector('.carousel-prev');
        this.nextBtn = container.querySelector('.carousel-next');
        
        this.currentIndex = 0;
        this.slideCount = this.slides.length;
        this.autoplayInterval = null;
        this.autoplayDelay = 4000;
        
        this.init();
    }
    
    init() {
        this.updatePositions();
        this.bindEvents();
        this.startAutoplay();
    }
    
    bindEvents() {
        // Navigation buttons
        this.prevBtn?.addEventListener('click', () => {
            this.prev();
            this.resetAutoplay();
        });
        
        this.nextBtn?.addEventListener('click', () => {
            this.next();
            this.resetAutoplay();
        });
        
        // Dots
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                this.goTo(index);
                this.resetAutoplay();
            });
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                this.prev();
                this.resetAutoplay();
            } else if (e.key === 'ArrowRight') {
                this.next();
                this.resetAutoplay();
            }
        });
        
        // Pause on hover
        this.container.addEventListener('mouseenter', () => this.stopAutoplay());
        this.container.addEventListener('mouseleave', () => this.startAutoplay());
        
        // Click on slide to go to it
        this.slides.forEach((slide, index) => {
            slide.addEventListener('click', () => {
                if (index !== this.currentIndex) {
                    this.goTo(index);
                    this.resetAutoplay();
                }
            });
        });
    }
    
    getPositionClass(offset) {
        switch (offset) {
            case 0: return 'active';
            case 1: return 'next-1';
            case 2: return 'next-2';
            case -1: return 'prev-1';
            case -2: return 'prev-2';
            default: return 'hidden';
        }
    }
    
    updatePositions() {
        this.slides.forEach((slide, index) => {
            // Remove all position classes
            slide.classList.remove('active', 'prev-1', 'prev-2', 'next-1', 'next-2', 'hidden');
            
            // Calculate offset from current
            let offset = index - this.currentIndex;
            
            // Wrap around for infinite effect
            if (offset > this.slideCount / 2) offset -= this.slideCount;
            if (offset < -this.slideCount / 2) offset += this.slideCount;
            
            // Add appropriate class
            slide.classList.add(this.getPositionClass(offset));
        });
        
        // Update dots
        this.dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentIndex);
        });
    }
    
    next() {
        this.currentIndex = (this.currentIndex + 1) % this.slideCount;
        this.updatePositions();
    }
    
    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.slideCount) % this.slideCount;
        this.updatePositions();
    }
    
    goTo(index) {
        this.currentIndex = index;
        this.updatePositions();
    }
    
    startAutoplay() {
        if (!this.autoplayInterval) {
            this.autoplayInterval = setInterval(() => this.next(), this.autoplayDelay);
        }
    }
    
    stopAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        }
    }
    
    resetAutoplay() {
        this.stopAutoplay();
        this.startAutoplay();
    }
}

// =================================
// Tabs
// =================================
class Tabs {
    constructor(container) {
        this.container = container;
        this.buttons = container.querySelectorAll('.tab-btn');
        this.contents = container.querySelectorAll('.tab-content');
        
        this.init();
    }
    
    init() {
        this.buttons.forEach((btn, index) => {
            btn.addEventListener('click', () => this.activate(index));
        });
    }
    
    activate(index) {
        // Update buttons
        this.buttons.forEach((btn, i) => {
            btn.classList.toggle('active', i === index);
        });
        
        // Update content
        this.contents.forEach((content, i) => {
            content.classList.toggle('active', i === index);
        });
    }
}

// =================================
// Smooth Scroll
// =================================
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                const navHeight = document.querySelector('.navbar')?.offsetHeight || 0;
                const targetPosition = target.getBoundingClientRect().top + window.scrollY - navHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// =================================
// Mobile Menu
// =================================
function initMobileMenu() {
    const menuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuBtn && navLinks) {
        menuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuBtn.classList.toggle('active');
        });
    }
}

// =================================
// Navbar Scroll Effect
// =================================
function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    
    if (navbar) {
        const handleScroll = () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };
        
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll(); // Initial check
    }
}

// =================================
// Intersection Observer for Animations
// =================================
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe elements
    document.querySelectorAll('.feature-card, .step-card, .doc-card').forEach(el => {
        el.classList.add('animate-target');
        observer.observe(el);
    });
}

// Add animation styles dynamically
function addAnimationStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .animate-target {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .animate-target.animate-in {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Staggered animations for grids */
        .features-grid .animate-target:nth-child(2) { transition-delay: 0.1s; }
        .features-grid .animate-target:nth-child(3) { transition-delay: 0.2s; }
        .features-grid .animate-target:nth-child(4) { transition-delay: 0.3s; }
        .features-grid .animate-target:nth-child(5) { transition-delay: 0.4s; }
        .features-grid .animate-target:nth-child(6) { transition-delay: 0.5s; }
        
        .steps-grid .animate-target:nth-child(3) { transition-delay: 0.15s; }
        .steps-grid .animate-target:nth-child(5) { transition-delay: 0.3s; }
        
        /* Mobile nav styles */
        @media (max-width: 768px) {
            .nav-links {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                flex-direction: column;
                background: white;
                padding: 20px;
                gap: 16px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                display: none;
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .mobile-menu-btn.active span:nth-child(1) {
                transform: rotate(45deg) translate(5px, 5px);
            }
            
            .mobile-menu-btn.active span:nth-child(2) {
                opacity: 0;
            }
            
            .mobile-menu-btn.active span:nth-child(3) {
                transform: rotate(-45deg) translate(5px, -5px);
            }
        }
        
        /* Navbar scroll effect */
        .navbar.scrolled {
            padding: 12px 0;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
    `;
    document.head.appendChild(style);
}

// =================================
// Initialize Everything
// =================================
document.addEventListener('DOMContentLoaded', () => {
    // Add dynamic styles
    addAnimationStyles();
    
    // Initialize carousel
    const carouselContainer = document.querySelector('.demo-carousel');
    if (carouselContainer) {
        new CarouselDemo(carouselContainer);
    }
    
    // Initialize tabs
    const tabsContainer = document.querySelector('.customize-content');
    if (tabsContainer) {
        new Tabs(tabsContainer);
    }
    
    // Initialize other features
    initSmoothScroll();
    initMobileMenu();
    initNavbarScroll();
    initScrollAnimations();
    
    console.log('✨ Modern Portfolio Showcase site loaded');
});

// =================================
// Copy Code Functionality
// =================================
function copyShortcode(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = 'Copied to clipboard!';
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    });
}

// Add toast animations
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateX(-50%) translateY(20px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }
    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(-50%) translateY(0); }
        to { opacity: 0; transform: translateX(-50%) translateY(20px); }
    }
`;
document.head.appendChild(toastStyles);
