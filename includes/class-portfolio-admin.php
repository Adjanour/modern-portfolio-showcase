<?php
/**
 * Admin functionality for the portfolio plugin
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

class Portfolio_Admin {
    
    private $database;
    
    public function __construct($database) {
        $this->database = $database;
    }
    
    /**
     * Register admin menu pages
     */
    public function add_admin_menu() {
        add_menu_page(
            'Portfolio Manager',
            'Portfolio',
            'manage_options',
            'modern-portfolio',
            array($this, 'dashboard_page'),
            'dashicons-portfolio',
            30
        );
        
        add_submenu_page(
            'modern-portfolio',
            'Categories',
            'Categories',
            'manage_options',
            'modern-portfolio-categories',
            array($this, 'categories_page')
        );
        
        add_submenu_page(
            'modern-portfolio',
            'Projects',
            'Projects',
            'manage_options',
            'modern-portfolio-projects',
            array($this, 'projects_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook != 'toplevel_page_modern-portfolio' && 
            $hook != 'portfolio_page_modern-portfolio-categories' && 
            $hook != 'portfolio_page_modern-portfolio-projects') {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_editor();
        
        wp_enqueue_style(
            'portfolio-admin-css', 
            plugin_dir_url(dirname(__FILE__)) . 'admin/css/admin.css'
        );
        
        wp_enqueue_script(
            'portfolio-admin-js', 
            plugin_dir_url(dirname(__FILE__)) . 'admin/js/admin.js', 
            array('jquery', 'editor'), 
            '1.0', 
            true
        );
        
        wp_localize_script('portfolio-admin-js', 'portfolioAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('portfolio_nonce')
        ));
    }
    
    /**
     * Dashboard page template
     */
    public function dashboard_page() {
        include dirname(dirname(__FILE__)) . '/templates/admin-dashboard.php';
    }
    
    /**
     * Categories page template
     */
    public function categories_page() {
        include dirname(dirname(__FILE__)) . '/templates/admin-categories.php';
    }
    
    /**
     * Projects page template
     */
    public function projects_page() {
        $edit_id = isset($_GET['edit']) ? sanitize_text_field($_GET['edit']) : 0;
        
        if ($edit_id === 'new' || intval($edit_id) > 0) {
            include dirname(dirname(__FILE__)) . '/templates/admin-project-edit.php';
        } else {
            include dirname(dirname(__FILE__)) . '/templates/admin-projects-list.php';
        }
    }
}
