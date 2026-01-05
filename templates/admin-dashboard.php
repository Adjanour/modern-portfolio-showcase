<?php
/**
 * Admin Dashboard Template
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

global $wpdb, $portfolio_database;

$total_projects   = 0;
$total_categories = 0;

if ( isset( $portfolio_database ) && $portfolio_database instanceof Portfolio_Database ) {
    $database         = $portfolio_database;
    $total_projects   = $wpdb->get_var( "SELECT COUNT(*) FROM {$database->get_table_name()}" );
    $total_categories = $wpdb->get_var( "SELECT COUNT(*) FROM {$database->get_categories_table()}" );
}
?>

<div class="wrap portfolio-admin">
    <h1>Portfolio Dashboard</h1>
    
    <div class="portfolio-dashboard">
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon">📁</div>
                <div class="stat-content">
                    <h3><?php echo $total_projects; ?></h3>
                    <p>Total Projects</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏷️</div>
                <div class="stat-content">
                    <h3><?php echo $total_categories; ?></h3>
                    <p>Total Categories</p>
                </div>
            </div>
        </div>
        
        <div class="dashboard-info">
            <h2>Getting Started</h2>
            <p>Manage your portfolio using the menu options:</p>
            <ul>
                <li><strong>Categories:</strong> Create and manage project categories with automatic slug generation</li>
                <li><strong>Projects:</strong> View, add, and edit your portfolio projects</li>
            </ul>
            <h3>Usage Shortcode</h3>
            <p>Display your portfolio on any page using: <code>[modern_portfolio]</code></p>
        </div>
    </div>
</div>
