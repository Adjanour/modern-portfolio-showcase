<?php
/**
 * Frontend functionality for the portfolio plugin
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

class Portfolio_Frontend {
    
    private $database;
    
    public function __construct($database) {
        $this->database = $database;
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        wp_enqueue_style(
            'portfolio-frontend-css', 
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/frontend.css'
        );
        
        wp_enqueue_script(
            'portfolio-frontend-js', 
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/frontend.js', 
            array('jquery'), 
            '1.0', 
            true
        );
        
        wp_localize_script('portfolio-frontend-js', 'portfolioAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('portfolio_nonce')
        ));
    }
    
    /**
     * Portfolio shortcode handler
     */
    public function portfolio_shortcode() {
        global $wpdb;
        
        ob_start();
        
        // Prepare data for template
        $database = $this->database;
        $frontend = $this;
        
        $items = $wpdb->get_results(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM {$database->get_table_name()} p 
             LEFT JOIN {$database->get_categories_table()} c ON p.category_id = c.id 
             ORDER BY p.created_at DESC"
        );
        
        // Handle query errors
        if ($items === null) {
            $items = array();
        }
        
        $categories = $wpdb->get_results("SELECT * FROM {$database->get_categories_table()} ORDER BY name ASC");
        if ($categories === null) {
            $categories = array();
        }
        
        include dirname(dirname(__FILE__)) . '/templates/frontend-portfolio.php';
        return ob_get_clean();
    }
    
    /**
     * Helper function to truncate text
     * 
     * @param string $text Text to truncate
     * @param int $word_limit Maximum number of words (not characters)
     * @return string Truncated text
     */
    public function truncate_text($text, $word_limit = 150) {
        $text = wp_strip_all_tags($text);
        $words = explode(' ', $text);
        
        if (count($words) > $word_limit) {
            $words = array_slice($words, 0, $word_limit);
            $text = implode(' ', $words) . '...';
        }
        
        return $text;
    }
}
