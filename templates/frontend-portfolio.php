<?php
/**
 * Frontend Portfolio Template
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

// Data is passed from Portfolio_Frontend::portfolio_shortcode()
// Available variables: $database, $frontend, $items, $categories
?>

<div class="modern-portfolio-container">
    <!-- <div class="portfolio-sidebar">
        <div class="portfolio-filters">
            <button class="filter-btn active" data-filter="all">All</button>
            <?php foreach ($categories as $cat): ?>
                <button class="filter-btn" data-filter="<?php echo esc_attr($cat->slug); ?>">
                    <?php echo esc_html($cat->name); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div> -->
    
    <div class="portfolio-main">
        <div class="brand-gallery-header">
            <h2>Brand Gallery</h2>
            <div class="view-toggle-simple">
                <button class="simple-toggle-btn active" data-view="carousel" title="Carousel View">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                </button>
                <button class="simple-toggle-btn" data-view="grid" title="Grid View">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="portfolio-content">
            <div class="carousel-view active">
                <div class="card-stack-container">
                    <button class="stack-nav-btn stack-nav-prev">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="15,18 9,12 15,6"></polyline>
                        </svg>
                    </button>
                    
                    <div class="card-stack-wrapper">
                        <div class="card-stack">
                            <?php foreach ($items as $index => $item): 
                                $images = explode(',', $item->images);
                                $filter_attr = $item->category_slug ? esc_attr($item->category_slug) : 'uncategorized';
                                $short_description = $frontend->truncate_text($item->description, 100);
                                $stack_position = $index;
                            ?>
                                <div class="portfolio-card" 
                                     data-index="<?php echo $index; ?>" 
                                     data-tag="<?php echo $filter_attr; ?>" 
                                     data-category="<?php echo $filter_attr; ?>" 
                                     data-project-id="<?php echo $item->id; ?>"
                                     style="--stack-index: <?php echo $stack_position; ?>;">
                                    
                                    <div class="card-media">
                                        <?php if (count($images) > 1): ?>
                                            <div class="card-image-carousel">
                                                <?php foreach ($images as $img_index => $image): ?>
                                                    <img src="<?php echo esc_url($image); ?>" 
                                                         alt="<?php echo esc_attr($item->title); ?>" 
                                                         class="card-image <?php echo $img_index === 0 ? 'active' : ''; ?>">
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <img src="<?php echo esc_url($images[0]); ?>" 
                                                 alt="<?php echo esc_attr($item->title); ?>" 
                                                 class="card-image">
                                        <?php endif; ?>
                                        
                                        <div class="card-play-overlay">
                                            <div class="play-button">
                                                <svg viewBox="0 0 24 24" fill="currentColor">
                                                    <polygon points="5,3 19,12 5,21"></polygon>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-content">
                                        <h3 class="card-title"><?php echo esc_html($item->title); ?></h3>
                                        <p class="card-description"><?php echo wp_kses_post($short_description); ?></p>
                                        <div class="card-actions">
                                            <button class="card-btn primary view-project-details" data-project-id="<?php echo $item->id; ?>">
                                                View Details
                                            </button>
                                            <?php if ($item->project_link): ?>
                                                <a href="<?php echo esc_url($item->project_link); ?>" 
                                                   class="card-btn secondary" 
                                                   target="_blank">
                                                    Visit Project
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button class="stack-nav-btn stack-nav-next">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="9,18 15,12 9,6"></polyline>
                        </svg>
                    </button>
                </div>
                
                <div class="stack-indicators">
                    <?php foreach ($items as $index => $item): ?>
                        <button class="stack-indicator <?php echo $index === 0 ? 'active' : ''; ?>" 
                                data-index="<?php echo $index; ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="grid-view">
                <?php foreach ($items as $item): 
                    $images = explode(',', $item->images);
                    $filter_attr = $item->category_slug ? esc_attr($item->category_slug) : 'uncategorized';
                    $short_description = $frontend->truncate_text($item->description, 150);
                ?>
                    <div class="portfolio-grid-item" data-tag="<?php echo $filter_attr; ?>" data-project-id="<?php echo $item->id; ?>">
                        <div class="grid-item-image">
                            <img src="<?php echo esc_url($images[0]); ?>" alt="<?php echo esc_attr($item->title); ?>">
                        </div>
                        <div class="grid-item-overlay">
                            <h3><?php echo esc_html($item->title); ?></h3>
                            <p><?php echo wp_kses_post($short_description); ?></p>
                            <div class="grid-item-actions">
                                <button class="portfolio-cta view-project-details" data-project-id="<?php echo $item->id; ?>">View Details</button>
                                <a href="<?php echo esc_url($item->project_link); ?>" class="portfolio-cta portfolio-cta-secondary" target="_blank">View Project</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Project Details Modal -->
<div id="project-details-modal" class="project-modal" style="display: none;">
    <div class="project-modal-content">
        <button class="modal-close">&times;</button>
        <div id="project-details-container"></div>
    </div>
</div>
