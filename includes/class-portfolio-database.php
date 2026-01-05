<?php
/**
 * Database operations for the portfolio plugin
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

class Portfolio_Database {
    
    private $table_name;
    private $categories_table;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'portfolio_items';
        $this->categories_table = $wpdb->prefix . 'portfolio_categories';
    }
    
    /**
     * Create database tables
     */
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description longtext NOT NULL,
            images text NOT NULL,
            project_link varchar(255) NOT NULL,
            category_id mediumint(9),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        $sql2 = "CREATE TABLE IF NOT EXISTS {$this->categories_table} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            slug varchar(100) NOT NULL UNIQUE,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        dbDelta($sql2);
        
        // Add category_id column if it doesn't exist
        $columns = $wpdb->get_results("DESCRIBE {$this->table_name}");
        $column_names = wp_list_pluck($columns, 'Field');
        
        if (!in_array('category_id', $column_names)) {
            $alter_sql = "ALTER TABLE {$this->table_name} ADD COLUMN category_id mediumint(9)";
            $result    = $wpdb->query($alter_sql);

            if ($result === false) {
                error_log(
                    sprintf(
                        'Portfolio_Database: Failed to execute schema update "%s" on table "%s": %s',
                        $alter_sql,
                        $this->table_name,
                        $wpdb->last_error
                    )
                );
            }
        }
    }
    
    /**
     * Get table name
     */
    public function get_table_name() {
        return $this->table_name;
    }
    
    /**
     * Get categories table name
     */
    public function get_categories_table() {
        return $this->categories_table;
    }
}
