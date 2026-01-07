<?php
/**
 * Frontend Portfolio Template
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

// Data is passed from Portfolio_Frontend::portfolio_shortcode()
// Available variables: $database, $frontend, $items, $categories

/**
 * Helper function to detect video type and format URL
 */
function portfolio_get_video_data($video_url) {
    if (empty($video_url)) {
        return array('type' => 'none', 'url' => '');
    }
    
    $video_url = trim($video_url);
    
    // Check for YouTube
    if (strpos($video_url, 'youtube.com/watch') !== false) {
        preg_match('/[?&]v=([^&]+)/', $video_url, $matches);
        if (!empty($matches[1])) {
            return array('type' => 'youtube', 'url' => 'https://www.youtube.com/embed/' . $matches[1]);
        }
    } elseif (strpos($video_url, 'youtu.be/') !== false) {
        $video_id = substr(parse_url($video_url, PHP_URL_PATH), 1);
        return array('type' => 'youtube', 'url' => 'https://www.youtube.com/embed/' . $video_id);
    } elseif (strpos($video_url, 'youtube.com/embed') !== false) {
        return array('type' => 'youtube', 'url' => $video_url);
    }
    
    // Check for Vimeo
    if (strpos($video_url, 'vimeo.com') !== false) {
        preg_match('/vimeo\.com\/(\d+)/', $video_url, $matches);
        if (!empty($matches[1])) {
            return array('type' => 'vimeo', 'url' => 'https://player.vimeo.com/video/' . $matches[1]);
        }
    }
    
    // Check for direct video file (WordPress media upload)
    $video_extensions = array('.mp4', '.webm', '.ogg', '.mov', '.m4v');
    foreach ($video_extensions as $ext) {
        if (stripos($video_url, $ext) !== false) {
            return array('type' => 'file', 'url' => $video_url);
        }
    }
    
    // Default: treat as embeddable URL
    return array('type' => 'embed', 'url' => $video_url);
}
?>

<div class="modern-portfolio-container">
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
            <!-- Clean Modern Carousel View -->
            <div class="carousel-view active">
                <div class="carousel-3d-container">
                    <button class="carousel-nav-btn carousel-nav-prev" aria-label="Previous Slide">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15,18 9,12 15,6"></polyline>
                        </svg>
                    </button>
                    
                    <div class="carousel-3d-wrapper">
                        <div class="carousel-3d-slides">
                            <?php foreach ($items as $index => $item): 
                                $images = explode(',', $item->images);
                                $first_image = trim($images[0]);
                                $video_url = !empty($item->video_url) ? $item->video_url : '';
                                $video_data = portfolio_get_video_data($video_url);
                                $has_video = $video_data['type'] !== 'none';
                            ?>
                                <div class="carousel-slide" 
                                     data-index="<?php echo $index; ?>"
                                     data-title="<?php echo esc_attr($item->title); ?>"
                                     data-video-type="<?php echo esc_attr($video_data['type']); ?>"
                                     data-video="<?php echo esc_attr($video_data['url']); ?>"
                                     data-project-id="<?php echo $item->id; ?>">
                                    <div class="slide-card">
                                        <!-- Thumbnail / Cover Image -->
                                        <div class="slide-media">
                                            <img src="<?php echo esc_url($first_image); ?>" 
                                                 alt="<?php echo esc_attr($item->title); ?>" 
                                                 class="slide-thumbnail">
                                            
                                            <?php if ($has_video): ?>
                                                <!-- Play button overlay for videos -->
                                                <div class="slide-play-btn">
                                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                                        <polygon points="5,3 19,12 5,21"></polygon>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Video container (hidden until active) -->
                                            <div class="slide-video-container">
                                                <?php if ($video_data['type'] === 'file'): ?>
                                                    <!-- WordPress uploaded video -->
                                                    <video class="slide-video" controls playsinline>
                                                        <source src="" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                <?php elseif ($has_video): ?>
                                                    <!-- YouTube/Vimeo embed -->
                                                    <iframe class="slide-iframe" 
                                                            src="" 
                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                            allowfullscreen></iframe>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Title overlay -->
                                        <div class="slide-overlay">
                                            <h3><?php echo esc_html($item->title); ?></h3>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button class="carousel-nav-btn carousel-nav-next" aria-label="Next Slide">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9,18 15,12 9,6"></polyline>
                        </svg>
                    </button>
                </div>
                
                <!-- Caption -->
                <div class="carousel-caption">
                    <h3><?php echo !empty($items[0]) ? esc_html($items[0]->title) : ''; ?></h3>
                </div>
                
                <!-- Dots Navigation -->
                <div class="carousel-dots">
                    <?php foreach ($items as $index => $item): ?>
                        <button class="carousel-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                data-index="<?php echo $index; ?>"
                                aria-label="Go to slide <?php echo $index + 1; ?>"></button>
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
