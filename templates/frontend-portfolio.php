<?php
/**
 * Frontend Portfolio Template
 * 
 * Renders the portfolio display with carousel and grid views.
 *
 * @package Modern_Portfolio_Showcase
 * @version 2.0
 * 
 * Available Variables (from Portfolio_Frontend::portfolio_shortcode()):
 * @var Portfolio_Database $database - Database handler instance
 * @var Portfolio_Frontend $frontend - Frontend class instance  
 * @var array              $items    - Portfolio items from database
 * @var array              $categories - Available categories
 */

if (!defined('ABSPATH')) exit;

/**
 * Helper function to detect video type and format embed URL
 * 
 * Supports: YouTube, Vimeo, and direct video files (.mp4, .webm, etc.)
 * 
 * @param string $video_url The video URL to process
 * @return array ['type' => string, 'url' => string]
 */
if (!function_exists('portfolio_get_video_data')) {
    function portfolio_get_video_data($video_url) {
        if (empty($video_url)) {
            return array('type' => 'none', 'url' => '');
        }
        
        $video_url = trim($video_url);
        
        // YouTube - various URL formats
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
        
        // Vimeo
        if (strpos($video_url, 'vimeo.com') !== false) {
            preg_match('/vimeo\.com\/(\d+)/', $video_url, $matches);
            if (!empty($matches[1])) {
                return array('type' => 'vimeo', 'url' => 'https://player.vimeo.com/video/' . $matches[1]);
            }
        }
        
        // Direct video file (WordPress media upload)
        $video_extensions = array('.mp4', '.webm', '.ogg', '.mov', '.m4v');
        foreach ($video_extensions as $ext) {
            if (stripos($video_url, $ext) !== false) {
                return array('type' => 'file', 'url' => $video_url);
            }
        }
        
        // Default: treat as generic embeddable URL
        return array('type' => 'embed', 'url' => $video_url);
    }
}
?>

<div class="modern-portfolio-container">
    <div class="portfolio-main">
        
        <!-- ============================================
             VIEW TOGGLE BUTTONS
             Switches between Carousel and Grid layouts
             ============================================ -->
        <div class="view-toggle-simple">
            <button id="carousel-view-btn" class="simple-toggle-btn active" data-view="carousel" title="Carousel View">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
            </button>
            <button id="grid-view-btn" class="simple-toggle-btn" data-view="grid" title="Grid View">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
            </button>
        </div>
        
        <div class="portfolio-content">
            
            <!-- ============================================
                 3D COVERFLOW CAROUSEL VIEW
                 Main showcase with perspective transforms
                 ============================================ -->
            <div id="carousel-view" class="carousel-view active">
                <div class="carousel-3d-container">
                    
                    <!-- Previous Button -->
                    <button class="carousel-nav-btn carousel-nav-prev" aria-label="Previous Slide">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15,18 9,12 15,6"></polyline>
                        </svg>
                    </button>
                    
                    <div class="carousel-3d-wrapper">
                        <div class="carousel-3d-slides">
                            
                            <?php foreach ($items as $index => $item): 
                                // Parse item data
                                $images = explode(',', $item->images);
                                $first_image = trim($images[0]);
                                $video_url = !empty($item->video_url) ? $item->video_url : '';
                                $video_data = portfolio_get_video_data($video_url);
                                $has_video = $video_data['type'] !== 'none';
                            ?>
                            
                            <!-- Carousel Slide -->
                            <div class="carousel-slide" 
                                 data-index="<?php echo $index; ?>"
                                 data-title="<?php echo esc_attr($item->title); ?>"
                                 data-video-type="<?php echo esc_attr($video_data['type']); ?>"
                                 data-video-url="<?php echo esc_attr($video_data['url']); ?>"
                                 data-project-id="<?php echo $item->id; ?>">
                                 
                                <div class="slide-card">
                                    <div class="slide-media">
                                        
                                        <!-- Thumbnail Image -->
                                        <img src="<?php echo esc_url($first_image); ?>" 
                                             alt="<?php echo esc_attr($item->title); ?>" 
                                             class="slide-thumbnail">
                                        
                                        <?php if ($has_video): ?>
                                        <!-- Play Button (videos only) -->
                                        <div class="slide-play-btn">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <polygon points="5,3 19,12 5,21"></polygon>
                                            </svg>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <!-- Video Container (populated by JS when playing) -->
                                        <div class="slide-video-container"></div>
                                        
                                        <!-- Hover Overlay (non-active slides) -->
                                        <div class="slide-hover-overlay">
                                            <h3><?php echo esc_html($item->title); ?></h3>
                                            <p><?php echo wp_kses_post($frontend->truncate_text($item->description, 100)); ?></p>
                                            <button class="slide-hover-btn" data-project-id="<?php echo $item->id; ?>">View Details</button>
                                        </div>
                                        
                                    </div>
                                    
                                    <!-- Title Overlay (bottom of slide) -->
                                    <div class="slide-overlay">
                                        <h3><?php echo esc_html($item->title); ?></h3>
                                    </div>
                                </div>
                            </div>
                            
                            <?php endforeach; ?>
                            
                        </div>
                    </div>
                    
                    <!-- Next Button -->
                    <button class="carousel-nav-btn carousel-nav-next" aria-label="Next Slide">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9,18 15,12 9,6"></polyline>
                        </svg>
                    </button>
                    
                </div>
                
                <!-- Active Slide Caption -->
                <div class="carousel-caption">
                    <h3 id="active-project-title"><?php echo !empty($items[0]) ? esc_html($items[0]->title) : ''; ?></h3>
                </div>
                
                <!-- Dot Navigation -->
                <div class="carousel-dots">
                    <?php foreach ($items as $index => $item): ?>
                    <button class="carousel-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                            data-index="<?php echo $index; ?>"
                            aria-label="Go to slide <?php echo $index + 1; ?>"></button>
                    <?php endforeach; ?>
                </div>
                
            </div>
            
            <!-- ============================================
                 GRID VIEW
                 Alternative layout with hover overlays
                 ============================================ -->
            <div id="grid-view" class="grid-view">
                
                <?php foreach ($items as $item): 
                    $images = explode(',', $item->images);
                    $filter_attr = $item->category_slug ? esc_attr($item->category_slug) : 'uncategorized';
                    $short_description = $frontend->truncate_text($item->description, 150);
                ?>
                
                <div class="portfolio-grid-item" 
                     data-category="<?php echo $filter_attr; ?>" 
                     data-project-id="<?php echo $item->id; ?>">
                     
                    <div class="grid-item-image">
                        <img src="<?php echo esc_url(trim($images[0])); ?>" 
                             alt="<?php echo esc_attr($item->title); ?>">
                    </div>
                    
                    <div class="grid-item-overlay">
                        <h3><?php echo esc_html($item->title); ?></h3>
                        <p><?php echo wp_kses_post($short_description); ?></p>
                        <div class="grid-item-actions">
                            <button class="portfolio-cta view-details-btn" data-project-id="<?php echo $item->id; ?>">View Details</button>
                            <?php if (!empty($item->project_link)): ?>
                            <a href="<?php echo esc_url($item->project_link); ?>" class="portfolio-cta portfolio-cta-secondary" target="_blank" rel="noopener">View Project</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
                
                <?php endforeach; ?>
                
            </div>
            
        </div>
    </div>
</div>
