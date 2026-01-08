<?php

/**
 * Plugin Name: Modern Portfolio Showcase
 * Plugin URI: https://yoursite.com
 * Description: A clean and modern portfolio plugin with 3D coverflow carousel, grid view, video support, and full customization
 * Version: 2.1.0
 * Author: Orcta Technologies 
 * License: GPL2
 * 
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

// Define plugin constants
define('MODERN_PORTFOLIO_VERSION', '2.1.0');
define('MODERN_PORTFOLIO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MODERN_PORTFOLIO_PLUGIN_URL', plugin_dir_url(__FILE__));

// Require class files
require_once MODERN_PORTFOLIO_PLUGIN_DIR . 'includes/class-portfolio-database.php';
require_once MODERN_PORTFOLIO_PLUGIN_DIR . 'includes/class-portfolio-settings.php';
require_once MODERN_PORTFOLIO_PLUGIN_DIR . 'includes/class-portfolio-admin.php';
require_once MODERN_PORTFOLIO_PLUGIN_DIR . 'includes/class-portfolio-ajax.php';
require_once MODERN_PORTFOLIO_PLUGIN_DIR . 'includes/class-portfolio-frontend.php';

/**
 * Main plugin class
 */
class Modern_Portfolio_Plugin
{

    private $database;
    private $admin;
    private $ajax;
    private $frontend;

    public function __construct()
    {
        // Initialize components
        $this->database = new Portfolio_Database();
        $this->admin = new Portfolio_Admin($this->database);
        $this->ajax = new Portfolio_Ajax($this->database);
        $this->frontend = new Portfolio_Frontend($this->database);

        // Register hooks
        register_activation_hook(__FILE__, array($this, 'activate'));

        // Admin hooks
        add_action('admin_menu', array($this->admin, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this->admin, 'enqueue_admin_scripts'));

        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this->frontend, 'enqueue_frontend_scripts'));
        add_shortcode('modern_portfolio', array($this->frontend, 'portfolio_shortcode'));

        // AJAX hooks
        $this->ajax->register_ajax_actions();
    }

    /**
     * Plugin activation
     */
    public function activate()
    {
        $this->database->create_tables();
    }
}

// Initialize the plugin
new Modern_Portfolio_Plugin();
