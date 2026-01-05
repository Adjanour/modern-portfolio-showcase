<?php
/**
 * Frontend Portfolio Template
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

global $wpdb, $portfolio_database, $portfolio_frontend;

// Use injected or global Portfolio_Database instance instead of creating a new one.
if (isset($database) && $database instanceof Portfolio_Database) {
    // $database was provided by the including context.
} elseif (isset($portfolio_database) && $portfolio_database instanceof Portfolio_Database) {
    $database = $portfolio_database;
}

// Use injected or global Portfolio_Frontend instance instead of creating a new one.
if (isset($frontend) && $frontend instanceof Portfolio_Frontend) {
    // $frontend was provided by the including context.
} elseif (isset($portfolio_frontend) && $portfolio_frontend instanceof Portfolio_Frontend) {
    $frontend = $portfolio_frontend;
}
$items = $wpdb->get_results(
    "SELECT p.*, c.name as category_name, c.slug as category_slug 
     FROM {$database->get_table_name()} p 
     LEFT JOIN {$database->get_categories_table()} c ON p.category_id = c.id 
     ORDER BY p.created_at DESC"
);

$categories = $wpdb->get_results("SELECT * FROM {$database->get_categories_table()} ORDER BY name ASC");
?>

<div class="modern-portfolio-container">
    <div class="portfolio-sidebar">
        <div class="portfolio-filters">
            <button class="filter-btn active" data-filter="all">All</button>
            <?php foreach ($categories as $cat): ?>
                <button class="filter-btn" data-filter="<?php echo esc_attr($cat->slug); ?>">
                    <?php echo esc_html($cat->name); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="portfolio-main">
        <div class="portfolio-view-toggle">
            <button class="view-toggle-btn active" data-view="slideshow">
                <span>Slideshow View</span>
            </button>
            <button class="view-toggle-btn" data-view="grid">
                <span>Grid View</span>
            </button>
        </div>
        
        <div class="portfolio-content">
            <div class="slideshow-view active">
                <div class="slideshow-container">
                    <?php foreach ($items as $index => $item): 
                        $images = explode(',', $item->images);
                        $filter_attr = $item->category_slug ? esc_attr($item->category_slug) : 'uncategorized';
                        $short_description = $frontend->truncate_text($item->description, 150);
                    ?>
                        <div class="portfolio-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-tag="<?php echo $filter_attr; ?>" data-category="<?php echo $filter_attr; ?>" data-project-id="<?php echo $item->id; ?>">
                            <div class="slide-images">
                                <?php if (count($images) > 1): ?>
                                    <div class="image-carousel">
                                        <?php foreach ($images as $img_index => $image): ?>
                                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($item->title); ?>" class="<?php echo $img_index === 0 ? 'active' : ''; ?>">
                                        <?php endforeach; ?>
                                        <button class="carousel-prev">‹</button>
                                        <button class="carousel-next">›</button>
                                    </div>
                                <?php else: ?>
                                    <img src="<?php echo esc_url($images[0]); ?>" alt="<?php echo esc_attr($item->title); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="slide-overlay">
                                <h3><?php echo esc_html($item->title); ?></h3>
                                <p><?php echo wp_kses_post($short_description); ?></p>
                                <div class="slide-actions">
                                    <button class="portfolio-cta view-project-details" data-project-id="<?php echo $item->id; ?>">View Details</button>
                                    <a href="<?php echo esc_url($item->project_link); ?>" class="portfolio-cta portfolio-cta-secondary" target="_blank">View Project</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="slideshow-dots">
                    <?php foreach ($items as $index => $item): ?>
                        <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
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
