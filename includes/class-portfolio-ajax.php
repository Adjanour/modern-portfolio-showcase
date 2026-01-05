<?php
/**
 * AJAX handlers for the portfolio plugin
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

class Portfolio_Ajax {
    
    private $database;
    
    public function __construct($database) {
        $this->database = $database;
    }
    
    /**
     * Register AJAX actions
     */
    public function register_ajax_actions() {
        add_action('wp_ajax_save_portfolio_item', array($this, 'save_portfolio_item'));
        add_action('wp_ajax_delete_portfolio_item', array($this, 'delete_portfolio_item'));
        add_action('wp_ajax_get_portfolio_items', array($this, 'get_portfolio_items'));
        add_action('wp_ajax_save_category', array($this, 'save_category'));
        add_action('wp_ajax_delete_category', array($this, 'delete_category'));
        add_action('wp_ajax_get_categories', array($this, 'get_categories'));
        add_action('wp_ajax_get_project', array($this, 'get_project'));
        add_action('wp_ajax_nopriv_get_project_details', array($this, 'get_project_details'));
        add_action('wp_ajax_get_project_details', array($this, 'get_project_details'));
    }
    
    /**
     * Save portfolio item
     */
    public function save_portfolio_item() {
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
    public function delete_portfolio_item() {
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
    public function get_portfolio_items() {
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
    public function save_category() {
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
            $wpdb->update($categories_table, $data, array('id' => $category_id));
            wp_send_json_success(array(
                'message' => 'Category updated successfully', 
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
    public function delete_category() {
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
    public function get_categories() {
        check_ajax_referer('portfolio_nonce', 'nonce');
        
        global $wpdb;
        $categories = $wpdb->get_results("SELECT * FROM {$this->database->get_categories_table()} ORDER BY name ASC");
        wp_send_json_success($categories);
    }
    
    /**
     * Get project
     */
    public function get_project() {
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
     * Get project details
     */
    public function get_project_details() {
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
}
