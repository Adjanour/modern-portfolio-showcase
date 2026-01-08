<?php

/**
 * Admin functionality for the portfolio plugin
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

class Portfolio_Admin
{

    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    /**
     * Register admin menu pages
     */
    public function add_admin_menu()
    {
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

        add_submenu_page(
            'modern-portfolio',
            'Settings',
            'Settings',
            'manage_options',
            'modern-portfolio-settings',
            array($this, 'settings_page')
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook)
    {
        // Check if we're on a portfolio admin page
        $portfolio_pages = array(
            'toplevel_page_modern-portfolio',
            'portfolio_page_modern-portfolio-categories',
            'portfolio_page_modern-portfolio-projects',
            'portfolio_page_modern-portfolio-settings'
        );

        if (!in_array($hook, $portfolio_pages)) {
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
    public function dashboard_page()
    {
        global $wpdb;

        // Prepare data for template
        $database = $this->database;
        $total_projects = $wpdb->get_var("SELECT COUNT(*) FROM {$database->get_table_name()}");
        $total_categories = $wpdb->get_var("SELECT COUNT(*) FROM {$database->get_categories_table()}");

        include dirname(dirname(__FILE__)) . '/templates/admin-dashboard.php';
    }

    /**
     * Categories page template
     */
    public function categories_page()
    {
        global $wpdb;

        // Prepare data for template
        $database = $this->database;
        $categories = $wpdb->get_results("SELECT * FROM {$database->get_categories_table()} ORDER BY created_at DESC");

        // Handle query errors
        if ($categories === null) {
            $categories = array();
        }

        include dirname(dirname(__FILE__)) . '/templates/admin-categories.php';
    }

    /**
     * Projects page template
     */
    public function projects_page()
    {
        global $wpdb;
        $edit_id = isset($_GET['edit']) ? sanitize_text_field($_GET['edit']) : 0;

        if ($edit_id === 'new' || intval($edit_id) > 0) {
            // Prepare data for edit template
            $database = $this->database;
            $project = null;

            if ($edit_id !== 'new' && intval($edit_id) > 0) {
                $project = $wpdb->get_row($wpdb->prepare(
                    "SELECT * FROM {$database->get_table_name()} WHERE id = %d",
                    intval($edit_id)
                ));
            }

            $categories = $wpdb->get_results("SELECT * FROM {$database->get_categories_table()} ORDER BY name ASC");
            if ($categories === null) {
                $categories = array();
            }

            $images = $project ? explode(',', $project->images) : array();

            include dirname(dirname(__FILE__)) . '/templates/admin-project-edit.php';
        } else {
            // Prepare data for list template
            $database = $this->database;
            $projects = $wpdb->get_results(
                "SELECT p.*, c.name as category_name 
                 FROM {$database->get_table_name()} p 
                 LEFT JOIN {$database->get_categories_table()} c ON p.category_id = c.id 
                 ORDER BY p.created_at DESC"
            );

            // Handle query errors
            if ($projects === null) {
                $projects = array();
            }

            include dirname(dirname(__FILE__)) . '/templates/admin-projects-list.php';
        }
    }

    /**
     * Settings page template
     */
    public function settings_page()
    {
        include dirname(dirname(__FILE__)) . '/templates/admin-settings.php';
    }
}
