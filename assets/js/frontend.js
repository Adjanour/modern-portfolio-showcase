jQuery(document).ready(function($) {
    let currentSlide = 0;
    let autoSlideInterval;
    let isAnimating = false;
    const SLIDE_DURATION = 5000; // 5 seconds per slide
    const TRANSITION_DURATION = 800; // 0.8 seconds transition
    
    const slides = $('.portfolio-slide');
    const dots = $('.slideshow-dots .dot');
    
    // Initialize - show first slide
    if (slides.length > 0) {
        slides.first().addClass('active');
        startAutoSlide();
    }
    
    // View toggle
    $(document).on('click', '.view-toggle-btn', function() {
        const view = $(this).data('view');
        $('.view-toggle-btn').removeClass('active');
        $(this).addClass('active');
        
        if (view === 'slideshow') {
            $('.slideshow-view').addClass('active');
            $('.grid-view').removeClass('active');
            startAutoSlide();
        } else {
            $('.grid-view').addClass('active');
            $('.slideshow-view').removeClass('active');
            stopAutoSlide();
        }
    });
    
    // Filter functionality
    $(document).on('click', '.filter-btn', function() {
        const filter = $(this).data('filter');
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Reset animation flag
        isAnimating = false;
        currentSlide = 0;
        
        // Filter slideshow
        if (filter === 'all') {
            slides.show();
        } else {
            slides.hide().filter('[data-category="' + filter + '"]').show();
        }
        
        // Show first visible slide with animation
        const firstVisible = slides.filter(':visible').first();
        if (firstVisible.length) {
            slides.removeClass('active').css('opacity', '0');
            firstVisible.addClass('active').css('opacity', '1');
            currentSlide = slides.index(firstVisible);
        }
        
        // Update dots
        updateDotsVisibility();
        
        // Filter grid
        if (filter === 'all') {
            $('.portfolio-grid-item').fadeIn(300);
        } else {
            $('.portfolio-grid-item').hide();
            $('.portfolio-grid-item[data-tag="' + filter + '"]').fadeIn(300);
        }
        
        // Restart auto-slide
        stopAutoSlide();
        startAutoSlide();
    });
    
    // Slideshow navigation with smooth fade transitions
    function showSlide(n) {
        if (isAnimating) return;
        
        const visibleSlides = slides.filter(':visible');
        
        if (visibleSlides.length === 0) return;
        
        isAnimating = true;
        
        // Calculate new slide index
        if (n >= visibleSlides.length) {
            n = 0;
        } else if (n < 0) {
            n = visibleSlides.length - 1;
        }
        
        currentSlide = n;
        
        // Fade out current slide
        slides.filter('.active').animate({
            opacity: 0
        }, TRANSITION_DURATION, function() {
            $(this).removeClass('active');
        });
        
        // Fade in next slide
        visibleSlides.eq(currentSlide).animate({
            opacity: 1
        }, TRANSITION_DURATION, function() {
            $(this).addClass('active');
            isAnimating = false;
        }).css('display', 'block').css('opacity', '0');
        
        updateDots();
    }
    
    function updateDots() {
        const visibleSlides = slides.filter(':visible');
        if (visibleSlides.length === 0) return;
        
        dots.removeClass('active');
        
        // Find which dot corresponds to the current visible slide
        let dotIndex = 0;
        visibleSlides.each(function(index) {
            if ($(this).hasClass('active')) {
                dotIndex = index;
            }
        });
        
        dots.each(function(index) {
            if (index === dotIndex && $(this).is(':visible')) {
                $(this).addClass('active');
            }
        });
    }
    
    function updateDotsVisibility() {
        const visibleSlides = slides.filter(':visible');
        dots.each(function(index) {
            if (visibleSlides.eq(index).length > 0) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    
    // Dot navigation
    $(document).on('click', '.dot', function() {
        stopAutoSlide();
        const visibleSlides = slides.filter(':visible');
        const visibleDots = dots.filter(':visible');
        currentSlide = visibleDots.index($(this));
        showSlide(currentSlide);
        startAutoSlide();
    });
    
    // Previous slide button
    $(document).on('click', '.slideshow-container .carousel-prev', function() {
        stopAutoSlide();
        showSlide(currentSlide - 1);
        startAutoSlide();
    });
    
    // Next slide button
    $(document).on('click', '.slideshow-container .carousel-next', function() {
        stopAutoSlide();
        showSlide(currentSlide + 1);
        startAutoSlide();
    });
    
    // Auto-advance slideshow
    function startAutoSlide() {
        stopAutoSlide();
        autoSlideInterval = setInterval(function() {
            showSlide(currentSlide + 1);
        }, SLIDE_DURATION);
    }
    
    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
    }
    
    // Pause on hover
    $('.slideshow-container').hover(
        function() {
            stopAutoSlide();
        },
        function() {
            startAutoSlide();
        }
    );
    
    // Pause on mouse enter, resume on mouse leave
    $('.portfolio-slide').hover(
        function() {
            stopAutoSlide();
        },
        function() {
            startAutoSlide();
        }
    );
    
    // View Details button click handler
    $(document).on('click', '.view-project-details', function(e) {
        e.preventDefault();
        const projectId = $(this).data('project-id');
        loadProjectModal(projectId);
    });
    
    // Make grid cards clickable to open details
    $(document).on('click', '.portfolio-grid-item', function(e) {
        // Don't trigger if clicking on buttons
        if (!$(e.target).closest('a, button').length) {
            const projectId = $(this).data('project-id');
            loadProjectModal(projectId);
        }
    });
    
    // Make slideshow cards clickable to open details
    $(document).on('click', '.portfolio-slide', function(e) {
        // Don't trigger if clicking on buttons or carousel controls
        if (!$(e.target).closest('button, a, .carousel-prev, .carousel-next, .slide-actions').length) {
            const projectId = $(this).data('project-id');
            if (projectId) {
                loadProjectModal(projectId);
            }
        }
    });
    
    // Load project modal
    function loadProjectModal(projectId) {
        $.post(portfolioAjax.ajax_url, {
            action: 'get_project_details',
            project_id: projectId,
            nonce: portfolioAjax.nonce
        }, function(response) {
            if (response.success) {
                const project = response.data;
                const images = project.images.split(',');
                let carouselHTML = '';
                
                if (images.length > 1) {
                    carouselHTML = '<div class="modal-carousel">';
                    images.forEach((img, idx) => {
                        carouselHTML += '<img src="' + img + '" alt="' + project.title + '" class="carousel-image ' + (idx === 0 ? 'active' : '') + '" style="display: ' + (idx === 0 ? 'block' : 'none') + '">';
                    });
                    carouselHTML += '<button class="carousel-control prev">‹</button>';
                    carouselHTML += '<button class="carousel-control next">›</button>';
                    carouselHTML += '</div>';
                } else {
                    carouselHTML = '<img src="' + images[0] + '" alt="' + project.title + '" class="modal-image">';
                }
                
                const html = `
                    <div class="project-details-header">
                        ${carouselHTML}
                    </div>
                    <div class="project-details-content">
                        <h2>${project.title}</h2>
                        <p class="project-category"><strong>Category:</strong> ${project.category_name || 'Uncategorized'}</p>
                        <div class="project-full-description">
                            ${project.description}
                        </div>
                        <div class="project-details-footer">
                            <a href="${project.project_link}" target="_blank" class="portfolio-cta">Visit Live Project →</a>
                        </div>
                    </div>
                `;
                
                $('#project-details-container').html(html);
                $('#project-details-modal').fadeIn();
                
                // Handle carousel navigation
                $('.carousel-control').on('click', function() {
                    const isNext = $(this).hasClass('next');
                    const currentImg = $('.carousel-image.active');
                    let nextImg;
                    
                    if (isNext) {
                        nextImg = currentImg.next('.carousel-image');
                        if (nextImg.length === 0) nextImg = $('.carousel-image').first();
                    } else {
                        nextImg = currentImg.prev('.carousel-image');
                        if (nextImg.length === 0) nextImg = $('.carousel-image').last();
                    }
                    
                    currentImg.removeClass('active').fadeOut();
                    nextImg.addClass('active').fadeIn();
                });
            }
        });
    }
    
    // Hover effect for slideshow
    $('.portfolio-slide').hover(
        function() {
            $(this).find('.slide-overlay').addClass('visible');
        },
        function() {
            $(this).find('.slide-overlay').removeClass('visible');
        }
    );
    
    // Stop auto-slide when user interacts
    $(document).on('click', '.slideshow-dots, .filter-btn', function() {
        stopAutoSlide();
        setTimeout(startAutoSlide, 1000);
    });