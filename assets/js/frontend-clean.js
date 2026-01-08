/**
 * Modern Portfolio Showcase - Frontend JavaScript
 * 
 * Clean, refactored version with only active functionality
 * 
 * Table of Contents:
 * 1. Global Variables & Configuration
 * 2. View Toggle Functions
 * 3. Carousel Core Functions
 * 4. Carousel Navigation
 * 5. Video Playback
 * 6. Auto-Slide
 * 7. Project Modal
 * 8. Initialization
 */

(function($) {
    'use strict';

    /* ===========================================
       1. GLOBAL VARIABLES & CONFIGURATION
       =========================================== */

    // Carousel state
    let currentSlide = 0;          // Current active slide index
    let totalSlides = 0;           // Total number of slides
    let autoSlideInterval = null;  // Auto-slide timer reference
    let isAutoSlideEnabled = true; // Auto-slide toggle state

    // Configuration
    const CONFIG = {
        autoSlideDelay: 5000,      // Time between auto-slides (ms)
        transitionDuration: 600,   // CSS transition duration (ms)
    };

    /* ===========================================
       2. VIEW TOGGLE FUNCTIONS
       =========================================== */

    /**
     * Initialize view toggle between carousel and grid views
     */
    function initViewToggle() {
        const carouselBtn = $('#carousel-view-btn');
        const gridBtn = $('#grid-view-btn');
        const carouselView = $('#carousel-view');
        const gridView = $('#grid-view');

        // Carousel view button click
        carouselBtn.on('click', function() {
            carouselBtn.addClass('active');
            gridBtn.removeClass('active');
            carouselView.addClass('active');
            gridView.removeClass('active');
            startAutoSlide();
        });

        // Grid view button click
        gridBtn.on('click', function() {
            gridBtn.addClass('active');
            carouselBtn.removeClass('active');
            gridView.addClass('active');
            carouselView.removeClass('active');
            stopAutoSlide();
            stopAllMedia();
        });
    }

    /* ===========================================
       3. CAROUSEL CORE FUNCTIONS
       =========================================== */

    /**
     * Update carousel slide positions
     * 
     * Position classes:
     * - .active: Center slide (current)
     * - .prev-1: One position to LEFT
     * - .prev-2: Two positions to LEFT
     * - .next-1: One position to RIGHT
     * - .next-2: Two positions to RIGHT
     * - .hidden: Not visible
     */
    function updateCarousel() {
        const $slides = $('.carousel-slide');

        $slides.each(function(index) {
            const $slide = $(this);

            // Remove all position classes
            $slide.removeClass('active prev-1 prev-2 next-1 next-2 hidden');

            // Calculate distance from current slide
            let diff = index - currentSlide;

            // Handle wrap-around for circular navigation
            // If diff is more than half the total, wrap around
            if (diff > totalSlides / 2) {
                diff -= totalSlides;
            } else if (diff < -totalSlides / 2) {
                diff += totalSlides;
            }

            // Assign position class based on distance
            switch (diff) {
                case 0:
                    $slide.addClass('active');
                    break;
                case -1:
                    $slide.addClass('prev-1');  // Left of center
                    break;
                case -2:
                    $slide.addClass('prev-2');  // Far left
                    break;
                case 1:
                    $slide.addClass('next-1');  // Right of center
                    break;
                case 2:
                    $slide.addClass('next-2');  // Far right
                    break;
                default:
                    $slide.addClass('hidden');  // Not visible
            }
        });

        // Update caption
        updateCaption();

        // Update dot navigation
        updateDots();

        // Stop any playing media when slide changes
        stopAllMedia();
    }

    /**
     * Update the caption text below carousel
     */
    function updateCaption() {
        const $activeSlide = $('.carousel-slide.active');
        const title = $activeSlide.data('title') || '';
        $('#active-project-title').text(title);
    }

    /**
     * Update dot navigation active state
     */
    function updateDots() {
        const $dots = $('.carousel-dot');
        $dots.removeClass('active');
        $dots.eq(currentSlide).addClass('active');
    }

    /**
     * Navigate to a specific slide
     * @param {number} index - Target slide index
     */
    function showSlide(index) {
        // Handle wrap-around
        if (index >= totalSlides) {
            index = 0;
        } else if (index < 0) {
            index = totalSlides - 1;
        }

        currentSlide = index;
        updateCarousel();
    }

    /* ===========================================
       4. CAROUSEL NAVIGATION
       =========================================== */

    /**
     * Initialize carousel navigation controls
     */
    function initCarouselNavigation() {
        const $slides = $('.carousel-slide');
        totalSlides = $slides.length;

        if (totalSlides === 0) return;

        // Previous button
        $('.carousel-nav-prev').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            showSlide(currentSlide - 1);
            restartAutoSlide();
        });

        // Next button
        $('.carousel-nav-next').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            showSlide(currentSlide + 1);
            restartAutoSlide();
        });

        // Dot navigation
        $('.carousel-dots').on('click', '.carousel-dot', function() {
            const index = $(this).data('index');
            showSlide(index);
            restartAutoSlide();
        });

        // Click on non-active slides to navigate
        $slides.on('click', function(e) {
            const $slide = $(this);

            // Don't navigate if clicking active slide
            if ($slide.hasClass('active')) {
                return;
            }

            // Navigate to clicked slide
            const slideIndex = $slides.index($slide);
            showSlide(slideIndex);
            restartAutoSlide();
        });

        // Keyboard navigation
        $(document).on('keydown', function(e) {
            // Only handle if carousel is visible
            if (!$('#carousel-view').hasClass('active')) return;
            // Don't handle if modal is open
            if ($('.project-modal').length) return;

            if (e.key === 'ArrowLeft') {
                showSlide(currentSlide - 1);
                restartAutoSlide();
            } else if (e.key === 'ArrowRight') {
                showSlide(currentSlide + 1);
                restartAutoSlide();
            }
        });

        // Initialize first slide
        updateCarousel();
    }

    /* ===========================================
       5. VIDEO PLAYBACK
       =========================================== */

    /**
     * Play video in the active slide
     * @param {jQuery} $slide - The slide element
     */
    function playVideo($slide) {
        const $videoContainer = $slide.find('.slide-video-container');
        const videoUrl = $slide.data('video-url');
        const videoType = $slide.data('video-type');

        if (!videoUrl) return;

        // Clear existing content
        $videoContainer.empty();

        // Create appropriate player based on video type
        let embedHTML = '';

        if (videoType === 'youtube') {
            // YouTube embed with autoplay
            const videoId = extractYouTubeId(videoUrl);
            if (videoId) {
                embedHTML = `<iframe 
                    src="https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>`;
            }
        } else if (videoType === 'vimeo') {
            // Vimeo embed with autoplay
            const videoId = extractVimeoId(videoUrl);
            if (videoId) {
                embedHTML = `<iframe 
                    src="https://player.vimeo.com/video/${videoId}?autoplay=1" 
                    frameborder="0" 
                    allow="autoplay; fullscreen; picture-in-picture" 
                    allowfullscreen>
                </iframe>`;
            }
        } else if (videoType === 'upload') {
            // Direct video file
            embedHTML = `<video autoplay controls>
                <source src="${videoUrl}" type="video/mp4">
                Your browser does not support the video tag.
            </video>`;
        }

        if (embedHTML) {
            $videoContainer.html(embedHTML);
            $slide.addClass('playing');
            stopAutoSlide();
        }
    }

    /**
     * Stop all playing media
     */
    function stopAllMedia() {
        // Remove playing class and clear video containers
        $('.carousel-slide.playing').each(function() {
            const $slide = $(this);
            $slide.removeClass('playing');
            $slide.find('.slide-video-container').empty();
        });
    }

    /**
     * Extract YouTube video ID from URL
     * @param {string} url - YouTube URL
     * @returns {string|null} Video ID or null
     */
    function extractYouTubeId(url) {
        const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    /**
     * Extract Vimeo video ID from URL
     * @param {string} url - Vimeo URL
     * @returns {string|null} Video ID or null
     */
    function extractVimeoId(url) {
        const regExp = /vimeo\.com\/(?:video\/)?(\d+)/;
        const match = url.match(regExp);
        return match ? match[1] : null;
    }

    /**
     * Initialize play buttons
     */
    function initVideoPlayback() {
        // Play button click handler
        $(document).on('click', '.slide-play-btn', function(e) {
            e.stopPropagation();
            const $slide = $(this).closest('.carousel-slide');

            if (!$slide.hasClass('active')) return;

            playVideo($slide);
        });
    }

    /* ===========================================
       6. AUTO-SLIDE
       =========================================== */

    /**
     * Start automatic slide advancement
     */
    function startAutoSlide() {
        if (!isAutoSlideEnabled) return;

        stopAutoSlide(); // Clear any existing timer
        autoSlideInterval = setInterval(function() {
            showSlide(currentSlide + 1);
        }, CONFIG.autoSlideDelay);
    }

    /**
     * Stop automatic slide advancement
     */
    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
    }

    /**
     * Restart auto-slide timer (called after user interaction)
     */
    function restartAutoSlide() {
        if (!isAutoSlideEnabled) return;
        startAutoSlide();
    }

    /**
     * Pause auto-slide on hover
     */
    function initAutoSlidePause() {
        const $carousel = $('.carousel-3d-container');

        $carousel.on('mouseenter', function() {
            stopAutoSlide();
        });

        $carousel.on('mouseleave', function() {
            // Only restart if no video is playing
            if (!$('.carousel-slide.playing').length) {
                startAutoSlide();
            }
        });
    }

    /* ===========================================
       7. PROJECT MODAL
       =========================================== */

    /**
     * Load project details modal via AJAX
     * @param {number} projectId - WordPress post ID
     */
    function loadProjectModal(projectId) {
        // Create modal container if it doesn't exist
        let $modal = $('#project-modal');
        if (!$modal.length) {
            $modal = $('<div id="project-modal" class="project-modal"></div>');
            $('body').append($modal);
        }

        // Show loading state
        $modal.html('<div class="project-modal-content"><div style="text-align:center;padding:50px;">Loading...</div></div>');

        // AJAX request for project details
        $.ajax({
            url: portfolioAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'load_project_details',
                project_id: projectId,
                nonce: portfolioAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $modal.html(response.data.html);
                    initModalCarousel();
                } else {
                    $modal.html('<div class="project-modal-content"><div style="padding:50px;">Error loading project details.</div></div>');
                }
            },
            error: function() {
                $modal.html('<div class="project-modal-content"><div style="padding:50px;">Error loading project details.</div></div>');
            }
        });

        // Stop carousel auto-slide
        stopAutoSlide();
        stopAllMedia();
    }

    /**
     * Close project modal
     */
    function closeModal() {
        $('#project-modal').remove();

        // Restart auto-slide if carousel is visible
        if ($('#carousel-view').hasClass('active')) {
            startAutoSlide();
        }
    }

    /**
     * Initialize modal image carousel (if multiple images)
     */
    function initModalCarousel() {
        const $images = $('.carousel-image');
        if ($images.length <= 1) return;

        let currentImage = 0;

        // Previous button
        $('.carousel-control.prev').on('click', function() {
            $images.eq(currentImage).removeClass('active');
            currentImage = (currentImage - 1 + $images.length) % $images.length;
            $images.eq(currentImage).addClass('active');
        });

        // Next button
        $('.carousel-control.next').on('click', function() {
            $images.eq(currentImage).removeClass('active');
            currentImage = (currentImage + 1) % $images.length;
            $images.eq(currentImage).addClass('active');
        });
    }

    /**
     * Initialize modal event handlers
     */
    function initModalHandlers() {
        // View Details button click
        $(document).on('click', '.view-details-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const projectId = $(this).data('project-id');
            if (projectId) {
                loadProjectModal(projectId);
            }
        });

        // Hover overlay button click (carousel)
        $(document).on('click', '.slide-hover-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const projectId = $(this).data('project-id');
            if (projectId) {
                loadProjectModal(projectId);
            }
        });

        // Grid item click
        $(document).on('click', '.portfolio-grid-item .grid-item-overlay', function(e) {
            // Don't open modal if clicking a link
            if ($(e.target).hasClass('portfolio-cta')) return;

            const $item = $(this).closest('.portfolio-grid-item');
            const projectId = $item.data('project-id');
            if (projectId) {
                loadProjectModal(projectId);
            }
        });

        // Close modal on close button click
        $(document).on('click', '.modal-close', function() {
            closeModal();
        });

        // Close modal on background click
        $(document).on('click', '.project-modal', function(e) {
            if ($(e.target).hasClass('project-modal')) {
                closeModal();
            }
        });

        // Close modal on Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#project-modal').length) {
                closeModal();
            }
        });
    }

    /* ===========================================
       8. INITIALIZATION
       =========================================== */

    /**
     * Initialize all portfolio functionality
     */
    function init() {
        // Initialize view toggle
        initViewToggle();

        // Initialize carousel
        initCarouselNavigation();

        // Initialize video playback
        initVideoPlayback();

        // Initialize auto-slide pause on hover
        initAutoSlidePause();

        // Initialize modal handlers
        initModalHandlers();

        // Start auto-slide if carousel is active
        if ($('#carousel-view').hasClass('active')) {
            startAutoSlide();
        }
    }

    // Initialize on document ready
    $(document).ready(function() {
        init();
    });

})(jQuery);
