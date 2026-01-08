<?php

/**
 * AJAX handlers for the portfolio plugin
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

class Portfolio_Ajax
{

    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    /**
     * Register AJAX actions
     */
    public function register_ajax_actions()
    {
        add_action('wp_ajax_save_portfolio_item', array($this, 'save_portfolio_item'));
        add_action('wp_ajax_delete_portfolio_item', array($this, 'delete_portfolio_item'));
        add_action('wp_ajax_get_portfolio_items', array($this, 'get_portfolio_items'));
        add_action('wp_ajax_save_category', array($this, 'save_category'));
        add_action('wp_ajax_delete_category', array($this, 'delete_category'));
        add_action('wp_ajax_get_categories', array($this, 'get_categories'));
        add_action('wp_ajax_get_project', array($this, 'get_project'));
        add_action('wp_ajax_nopriv_load_project_details', array($this, 'load_project_details'));
        add_action('wp_ajax_load_project_details', array($this, 'load_project_details'));

        // Settings AJAX
        add_action('wp_ajax_save_portfolio_settings', array($this, 'save_portfolio_settings'));
        add_action('wp_ajax_reset_portfolio_settings', array($this, 'reset_portfolio_settings'));
    }

    /**
     * Save portfolio settings
     */
    public function save_portfolio_settings()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'portfolio_settings_nonce')) {
            wp_send_json_error('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        // Parse the serialized form data
        parse_str($_POST['settings'], $form_data);

        // Build settings array
        $settings = array(
            // Colors
            'primary_color'         => $form_data['primary_color'] ?? '',
            'primary_hover_color'   => $form_data['primary_hover_color'] ?? '',
            'overlay_color'         => $form_data['overlay_color'] ?? '',
            'text_color'            => $form_data['text_color'] ?? '',

            // Dimensions
            'card_width'            => $form_data['card_width'] ?? 680,
            'card_height'           => $form_data['card_height'] ?? 400,
            'card_border_radius'    => $form_data['card_border_radius'] ?? 16,

            // 3D Effect
            'perspective'           => $form_data['perspective'] ?? 1200,
            'rotate_angle'          => $form_data['rotate_angle'] ?? 35,
            'side_card_blur'        => $form_data['side_card_blur'] ?? 1,
            'far_card_blur'         => $form_data['far_card_blur'] ?? 2,

            // Animation
            'transition_duration'   => $form_data['transition_duration'] ?? 600,
            'auto_slide_delay'      => $form_data['auto_slide_delay'] ?? 5000,
            'auto_slide_enabled'    => isset($form_data['auto_slide_enabled']),

            // Display options
            'show_title_bar'        => isset($form_data['show_title_bar']),
            'show_play_button'      => isset($form_data['show_play_button']),
            'show_nav_buttons'      => isset($form_data['show_nav_buttons']),
            'show_view_toggle'      => isset($form_data['show_view_toggle']),

            // Grid
            'grid_columns'          => $form_data['grid_columns'] ?? 3,
            'grid_gap'              => $form_data['grid_gap'] ?? 30,
        );

        if (Portfolio_Settings::save($settings)) {
            wp_send_json_success('Settings saved');
        } else {
            wp_send_json_error('Failed to save settings');
        }
    }

    /**
     * Reset portfolio settings to defaults
     */
    public function reset_portfolio_settings()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'portfolio_settings_nonce')) {
            wp_send_json_error('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        if (Portfolio_Settings::reset()) {
            wp_send_json_success('Settings reset');
        } else {
            wp_send_json_error('Failed to reset settings');
        }
    }

    /**
     * Save portfolio item
     */
    public function save_portfolio_item()
    {
        check_ajax_referer('portfolio_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        global $wpdb;
        $table_name = $this->database->get_table_name();

        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $description = isset($_POST['description']) ? wp_kses_post($_POST['description']) : '';
        $images = isset($_POST['images']) ? sanitize_text_field($_POST['images']) : '';
        $video_url = isset($_POST['video_url']) ? esc_url_raw($_POST['video_url']) : '';
        $project_link = isset($_POST['project_link']) ? esc_url_raw($_POST['project_link']) : '';
        $category_id = isset($_POST['category_id']) && !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;

        // Validate required fields
        if (empty($title)) {
            wp_send_json_error(array('message' => 'Project title is required'));
        }
        if (empty($description)) {
            wp_send_json_error(array('message' => 'Project description is required'));
        }
        if (empty($images)) {
            wp_send_json_error(array('message' => 'At least one project image is required'));
        }
        if (empty($project_link)) {
            wp_send_json_error(array('message' => 'Project link is required'));
        }

        $data = array(
            'title' => $title,
            'description' => $description,
            'images' => $images,
            'video_url' => $video_url,
            'project_link' => $project_link,
            'category_id' => $category_id
        );

        if ($project_id > 0) {
            $result = $wpdb->update($table_name, $data, array('id' => $project_id));
            if ($result === false) {
                wp_send_json_error(array('message' => 'Error updating project: ' . $wpdb->last_error));
            }
            wp_send_json_success(array('message' => 'Project updated successfully', 'redirect' => admin_url('admin.php?page=modern-portfolio-projects')));
        } else {
            $result = $wpdb->insert($table_name, $data);
            if ($result === false) {
                wp_send_json_error(array('message' => 'Error creating project: ' . $wpdb->last_error));
            }
            wp_send_json_success(array('message' => 'Project created successfully', 'redirect' => admin_url('admin.php?page=modern-portfolio-projects')));
        }
    }

    /**
     * Delete portfolio item
     */
    public function delete_portfolio_item()
    {
        check_ajax_referer('portfolio_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        global $wpdb;
        $project_id = intval($_POST['project_id']);
        $wpdb->delete($this->database->get_table_name(), array('id' => $project_id));

        wp_send_json_success('Project deleted successfully');
    }

    /**
     * Get portfolio items
     */
    public function get_portfolio_items()
    {
        check_ajax_referer('portfolio_nonce', 'nonce');

        global $wpdb;
        $table_name = $this->database->get_table_name();
        $categories_table = $this->database->get_categories_table();

        $items = $wpdb->get_results(
            "SELECT p.*, c.name as category_name 
             FROM {$table_name} p 
             LEFT JOIN {$categories_table} c ON p.category_id = c.id 
             ORDER BY p.created_at DESC"
        );

        wp_send_json_success($items);
    }

    /**
     * Save category
     */
    public function save_category()
    {
        check_ajax_referer('portfolio_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        global $wpdb;
        $categories_table = $this->database->get_categories_table();

        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $name = sanitize_text_field($_POST['name']);
        $slug = sanitize_title($_POST['slug']);

        if (empty($name) || empty($slug)) {
            wp_send_json_error('Category name and slug are required');
        }

        $data = array(
            'name' => $name,
            'slug' => $slug
        );

        if ($category_id > 0) {
            // Ensure slug is unique for updates as well
            $existing = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id FROM {$categories_table} WHERE slug = %s AND id != %d",
                    $slug,
                    $category_id
                )
            );
            if ($existing) {
                wp_send_json_error('Slug already exists');
            }

            $result = $wpdb->update($categories_table, $data, array('id' => $category_id));

            if ($result === false) {
                wp_send_json_error('Failed to update category');
            }

            $message = ($result === 0)
                ? 'No changes made to category'
                : 'Category updated successfully';

            wp_send_json_success(array(
                'message' => $message,
                'category' => $wpdb->get_row($wpdb->prepare("SELECT * FROM {$categories_table} WHERE id = %d", $category_id))
            ));
        } else {
            $existing = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$categories_table} WHERE slug = %s", $slug));
            if ($existing) {
                wp_send_json_error('Slug already exists');
            }

            $wpdb->insert($categories_table, $data);
            wp_send_json_success(array(
                'message' => 'Category created successfully',
                'category' => $wpdb->get_row($wpdb->prepare("SELECT * FROM {$categories_table} WHERE id = %d", $wpdb->insert_id))
            ));
        }
    }

    /**
     * Delete category
     */
    public function delete_category()
    {
        check_ajax_referer('portfolio_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        global $wpdb;
        $category_id = intval($_POST['category_id']);

        // Set projects with this category to uncategorized
        $wpdb->update($this->database->get_table_name(), array('category_id' => null), array('category_id' => $category_id));

        $wpdb->delete($this->database->get_categories_table(), array('id' => $category_id));
        wp_send_json_success('Category deleted successfully');
    }

    /**
     * Get categories
     */
    public function get_categories()
    {
        check_ajax_referer('portfolio_nonce', 'nonce');

        global $wpdb;
        $categories = $wpdb->get_results("SELECT * FROM {$this->database->get_categories_table()} ORDER BY name ASC");
        wp_send_json_success($categories);
    }

    /**
     * Get project
     */
    public function get_project()
    {
        check_ajax_referer('portfolio_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        global $wpdb;
        $project_id = intval($_POST['project_id']);
        $project = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->database->get_table_name()} WHERE id = %d",
            $project_id
        ));

        wp_send_json_success($project);
    }

    /**
     * Get project details - returns raw project data (admin use)
     */
    public function get_project_details()
    {
        check_ajax_referer('portfolio_nonce', 'nonce');

        global $wpdb;
        $project_id = intval($_POST['project_id']);

        $table_name = $this->database->get_table_name();
        $categories_table = $this->database->get_categories_table();

        $project = $wpdb->get_row($wpdb->prepare(
            "SELECT p.*, c.name as category_name 
             FROM {$table_name} p 
             LEFT JOIN {$categories_table} c ON p.category_id = c.id 
             WHERE p.id = %d",
            $project_id
        ));

        if (!$project) {
            wp_send_json_error('Project not found');
        }

        wp_send_json_success($project);
    }

    /**
     * Load project details for modal (frontend) - returns HTML
     */
    public function load_project_details()
    {
        error_log('load_project_details called');
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'portfolio_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed'));
        }

        global $wpdb;
        $project_id = intval($_POST['project_id']);

        $table_name = $this->database->get_table_name();
        $categories_table = $this->database->get_categories_table();

        $project = $wpdb->get_row($wpdb->prepare(
            "SELECT p.*, c.name as category_name 
             FROM {$table_name} p 
             LEFT JOIN {$categories_table} c ON p.category_id = c.id 
             WHERE p.id = %d",
            $project_id
        ));

        if (!$project) {
            wp_send_json_error(array('message' => 'Project not found'));
        }

        // Parse images
        $images = array_filter(array_map('trim', explode(',', $project->images)));
        $first_image = !empty($images) ? $images[0] : '';
        $has_multiple_images = count($images) > 1;

        // Build HTML for modal
        ob_start();
?>
        <div class="project-modal-content">
            <button class="modal-close" aria-label="Close modal">&times;</button>

            <div id="project-details-container">
                <div class="project-details-header">
                    <?php if ($has_multiple_images): ?>
                        <div class="modal-carousel">
                            <?php foreach ($images as $index => $image): ?>
                                <img src="<?php echo esc_url(trim($image)); ?>"
                                    alt="<?php echo esc_attr($project->title); ?>"
                                    class="carousel-image <?php echo $index === 0 ? 'active' : ''; ?>"
                                    data-index="<?php echo $index; ?>">
                            <?php endforeach; ?>
                            <?php if (count($images) > 1): ?>
                                <button class="carousel-control prev" aria-label="Previous image">&lsaquo;</button>
                                <button class="carousel-control next" aria-label="Next image">&rsaquo;</button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <img src="<?php echo esc_url($first_image); ?>"
                            alt="<?php echo esc_attr($project->title); ?>"
                            class="modal-image">
                    <?php endif; ?>
                </div>

                <div class="project-details-content">
                    <h2><?php echo esc_html($project->title); ?></h2>

                    <?php if (!empty($project->category_name)): ?>
                        <p class="project-category"><?php echo esc_html($project->category_name); ?></p>
                    <?php endif; ?>

                    <div class="project-full-description">
                        <?php echo wp_kses_post(wpautop($project->description)); ?>
                    </div>

                    <?php if (!empty($project->project_link)): ?>
                        <div class="project-details-footer">
                            <a href="<?php echo esc_url($project->project_link); ?>"
                                class="portfolio-cta"
                                target="_blank"
                                rel="noopener noreferrer">
                                View Project
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
        $html = ob_get_clean();

        wp_send_json_success(array('html' => $html));
    }
}
