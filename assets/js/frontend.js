jQuery(document).ready(function($) {
    let currentCardIndex = 0;
    let autoSlideInterval;
    let isAnimating = false;
    const SLIDE_DURATION = 6000; // 6 seconds per slide
    const TRANSITION_DURATION = 800; // 0.8 seconds transition
    
    const cards = $('.portfolio-card');
    const indicators = $('.stack-indicator');
    
    // Initialize card stack
    if (cards.length > 0) {
        updateCardStack();
        startAutoSlide();
    }
    
    // Card stack navigation
    function updateCardStack() {
        cards.each(function(index) {
            const $card = $(this);
            const position = index - currentCardIndex;
            
            $card.removeClass('active prev next');
            $card.css('--stack-index', Math.abs(position));
            
            if (position === 0) {
                $card.addClass('active');
            } else if (position === -1) {
                $card.addClass('prev');
            } else if (position === 1) {
                $card.addClass('next');
            }
        });
        
        // Update indicators
        indicators.removeClass('active');
        indicators.eq(currentCardIndex).addClass('active');
    }
    
    function showCard(index) {
        if (isAnimating || index < 0 || index >= cards.length) return;
        
        isAnimating = true;
        currentCardIndex = index;
        updateCardStack();
        
        setTimeout(() => {
            isAnimating = false;
        }, TRANSITION_DURATION);
    }
    
    // Navigation buttons
    $(document).on('click', '.stack-nav-prev', function() {
        stopAutoSlide();
        const newIndex = currentCardIndex > 0 ? currentCardIndex - 1 : cards.length - 1;
        showCard(newIndex);
        startAutoSlide();
    });
    
    $(document).on('click', '.stack-nav-next', function() {
        stopAutoSlide();
        const newIndex = currentCardIndex < cards.length - 1 ? currentCardIndex + 1 : 0;
        showCard(newIndex);
        startAutoSlide();
    });
    
    // Indicator navigation
    $(document).on('click', '.stack-indicator', function() {
        stopAutoSlide();
        const index = $(this).data('index');
        showCard(index);
        startAutoSlide();
    });
    
    // Card click navigation
    $(document).on('click', '.portfolio-card:not(.active)', function() {
        stopAutoSlide();
        const index = $(this).data('index');
        showCard(index);
        startAutoSlide();
    });
    
    // Auto-advance slideshow
    function startAutoSlide() {
        stopAutoSlide();
        autoSlideInterval = setInterval(function() {
            const nextIndex = currentCardIndex < cards.length - 1 ? currentCardIndex + 1 : 0;
            showCard(nextIndex);
        }, SLIDE_DURATION);
    }
    
    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
    }
    
    // Pause on hover
    $('.card-stack-container').hover(
        function() {
            stopAutoSlide();
        },
        function() {
            startAutoSlide();
        }
    );
    
    // Image carousel within cards
    $(document).on('click', '.card-image-carousel', function(e) {
        e.stopPropagation();
        const $carousel = $(this);
        const $images = $carousel.find('.card-image');
        const $current = $images.filter('.active');
        const currentIndex = $images.index($current);
        const nextIndex = (currentIndex + 1) % $images.length;
        
        $images.removeClass('active');
        $images.eq(nextIndex).addClass('active');
    });
    
    // View toggle (simplified design)
    $(document).on('click', '.simple-toggle-btn', function() {
        const view = $(this).data('view');
        $('.simple-toggle-btn').removeClass('active');
        $(this).addClass('active');
        
        if (view === 'carousel') {
            $('.carousel-view').addClass('active');
            $('.grid-view').removeClass('active');
            startAutoSlide();
        } else {
            $('.grid-view').addClass('active');
            $('.carousel-view').removeClass('active');
            stopAutoSlide();
        }
    });
    
    // Filter functionality
    $(document).on('click', '.filter-btn', function() {
        const filter = $(this).data('filter');
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Reset animation flag and current index
        isAnimating = false;
        currentCardIndex = 0;
        
        // Filter cards
        if (filter === 'all') {
            cards.show();
        } else {
            cards.hide().filter('[data-category="' + filter + '"]').show();
        }
        
        // Update card references and restart
        const visibleCards = cards.filter(':visible');
        if (visibleCards.length > 0) {
            updateCardStack();
        }
        
        // Filter grid items
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
    
    // Project Details Modal
    $(document).on('click', '.view-project-details', function(e) {
        e.stopPropagation();
        const projectId = $(this).data('project-id');
        loadProjectModal(projectId);
    });
    
    // Make cards clickable to open details
    $(document).on('click', '.portfolio-card.active .card-media', function(e) {
        if (!$(e.target).closest('.play-button').length) {
            const projectId = $(this).closest('.portfolio-card').data('project-id');
            loadProjectModal(projectId);
        }
    });
    
    // Grid view compatibility
    $(document).on('click', '.portfolio-grid-item', function(e) {
        if (!$(e.target).closest('a, button').length) {
            const projectId = $(this).data('project-id');
            loadProjectModal(projectId);
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
                            ${project.project_link ? `<a href="${project.project_link}" target="_blank" class="portfolio-cta">Visit Live Project →</a>` : ''}
                        </div>
                    </div>
                `;
                
                $('#project-details-container').html(html);
                $('#project-details-modal').fadeIn();
                
                // Handle carousel navigation in modal
                $('.carousel-control').on('click', function(e) {
                    e.stopPropagation();
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
                    
                    currentImg.removeClass('active').fadeOut(300);
                    nextImg.addClass('active').fadeIn(300);
                });
            }
        }).fail(function() {
            alert('Error loading project details. Please try again.');
        });
    }
    
    // Close modal
    $(document).on('click', '.modal-close, #project-details-modal', function(e) {
        if (e.target === this) {
            $('#project-details-modal').fadeOut();
        }
    });
    
    // Prevent modal from closing when clicking inside content
    $(document).on('click', '.project-modal-content', function(e) {
        e.stopPropagation();
    });
    
    // Keyboard navigation
    $(document).keydown(function(e) {
        if ($('#project-details-modal').is(':visible')) {
            if (e.keyCode === 27) { // ESC key
                $('#project-details-modal').fadeOut();
            }
        } else if ($('.carousel-view').hasClass('active')) {
            if (e.keyCode === 37) { // Left arrow
                $('.stack-nav-prev').click();
            } else if (e.keyCode === 39) { // Right arrow
                $('.stack-nav-next').click();
            }
        }
    });
    
    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    $('.card-stack-wrapper').on('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    $('.card-stack-wrapper').on('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            stopAutoSlide();
            if (diff > 0) {
                // Swipe left - next card
                $('.stack-nav-next').click();
            } else {
                // Swipe right - previous card
                $('.stack-nav-prev').click();
            }
            startAutoSlide();
        }
    }
});