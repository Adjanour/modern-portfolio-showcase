<?php
/**
 * Plugin Name: Modern Portfolio Showcase
 * Plugin URI: https://yoursite.com
 * Description: A clean and modern portfolio plugin with filtering, slideshow and grid views
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL2
 */

if (!defined('ABSPATH')) exit;

class Modern_Portfolio_Plugin {
    
    private $table_name;
    private $categories_table;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'portfolio_items';
        $this->categories_table = $wpdb->prefix . 'portfolio_categories';
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_shortcode('modern_portfolio', array($this, 'portfolio_shortcode'));
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
    
    public function activate() {
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
            $wpdb->query("ALTER TABLE {$this->table_name} ADD COLUMN category_id mediumint(9)");
        }
    }
    
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
    
    public function enqueue_admin_scripts($hook) {
        if ($hook != 'toplevel_page_modern-portfolio' && $hook != 'portfolio_page_modern-portfolio-categories' && $hook != 'portfolio_page_modern-portfolio-projects') return;
        
        wp_enqueue_media();
        
        // Enqueue TinyMCE editor for rich text editing
        wp_enqueue_editor();
        
        wp_enqueue_style('portfolio-admin-css', plugin_dir_url(__FILE__) . 'portfolio_admin_css.css');
        wp_enqueue_script('portfolio-admin-js', plugin_dir_url(__FILE__) . 'portfolio_admin_js.js', array('jquery', 'editor'), '1.0', true);
        wp_localize_script('portfolio-admin-js', 'portfolioAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('portfolio_nonce')
        ));
    }
    
    public function enqueue_frontend_scripts() {
        wp_enqueue_style('portfolio-frontend-css', plugin_dir_url(__FILE__) . 'portfolio_frontend_css.css');
        wp_enqueue_script('portfolio-frontend-js', plugin_dir_url(__FILE__) . 'portfolio_frontend_js.js', array('jquery'), '1.0', true);
    }
    
    public function dashboard_page() {
        global $wpdb;
        $total_projects = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
        $total_categories = $wpdb->get_var("SELECT COUNT(*) FROM {$this->categories_table}");
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
        <?php
    }
    
    public function categories_page() {
        global $wpdb;
        $categories = $wpdb->get_results("SELECT * FROM {$this->categories_table} ORDER BY created_at DESC");
        ?>
        <div class="wrap portfolio-admin">
            <h1>Manage Categories</h1>
            
            <div class="categories-container">
                <div class="category-form-section">
                    <h2>Add New Category</h2>
                    <form id="category-form">
                        <table class="form-table">
                            <tr>
                                <th><label for="category-name">Category Name</label></th>
                                <td>
                                    <input type="text" id="category-name" name="name" class="regular-text" placeholder="e.g., Web Development" required>
                                    <p class="description">The display name for this category</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="category-slug">Slug</label></th>
                                <td>
                                    <input type="text" id="category-slug" name="slug" class="regular-text" placeholder="auto-generated" readonly>
                                    <p class="description">Auto-generated from the category name</p>
                                </td>
                            </tr>
                        </table>
                        <input type="hidden" id="category-id" name="category_id">
                        <p class="submit">
                            <button type="submit" class="button button-primary">Save Category</button>
                            <button type="button" class="button cancel-category-edit" style="display:none;">Cancel</button>
                        </p>
                    </form>
                </div>
                
                <div class="category-list-section">
                    <h2>Existing Categories</h2>
                    <div id="categories-list">
                        <?php if (!empty($categories)): ?>
                            <table class="wp-list-table widefat striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="categories-table-body">
                                    <?php foreach ($categories as $cat): ?>
                                        <tr class="category-row" data-id="<?php echo $cat->id; ?>">
                                            <td><?php echo esc_html($cat->name); ?></td>
                                            <td><code><?php echo esc_html($cat->slug); ?></code></td>
                                            <td><?php echo date('M d, Y', strtotime($cat->created_at)); ?></td>
                                            <td>
                                                <button class="button button-small edit-category" data-id="<?php echo $cat->id; ?>">Edit</button>
                                                <button class="button button-small button-link-delete delete-category" data-id="<?php echo $cat->id; ?>">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No categories found. Create one to get started!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function projects_page() {
        // Check if editing a specific project
        $edit_id = isset($_GET['edit']) ? sanitize_text_field($_GET['edit']) : 0;
        
        if ($edit_id === 'new' || intval($edit_id) > 0) {
            $this->project_edit_page($edit_id);
        } else {
            $this->projects_list_page();
        }
    }
    
    public function projects_list_page() {
        global $wpdb;
        $projects = $wpdb->get_results("SELECT p.*, c.name as category_name FROM {$this->table_name} p LEFT JOIN {$this->categories_table} c ON p.category_id = c.id ORDER BY p.created_at DESC");
        ?>
        <div class="wrap portfolio-admin">
            <h1>Projects
                <a href="<?php echo admin_url('admin.php?page=modern-portfolio-projects&edit=new'); ?>" class="page-title-action">Add New</a>
            </h1>
            
            <div class="projects-list-section">
                <?php if (!empty($projects)): ?>
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($project->title); ?></strong>
                                    </td>
                                    <td>
                                        <?php 
                                        if ($project->category_name) {
                                            echo '<span class="badge">' . esc_html($project->category_name) . '</span>';
                                        } else {
                                            echo '<em style="color:#999;">Uncategorized</em>';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($project->created_at)); ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('admin.php?page=modern-portfolio-projects&edit=' . $project->id); ?>" class="button button-small">Edit</a>
                                        <button class="button button-small button-link-delete delete-project-btn" data-id="<?php echo $project->id; ?>">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No projects found. <a href="<?php echo admin_url('admin.php?page=modern-portfolio-projects&edit=new'); ?>">Create one now</a>!</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    public function project_edit_page($edit_id) {
        global $wpdb;
        $project = null;
        
        // Allow 'new' string or numeric ID
        if ($edit_id !== 'new' && intval($edit_id) > 0) {
            $project = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", intval($edit_id)));
            if (!$project) {
                echo '<div class="wrap"><div class="error"><p>Project not found.</p></div></div>';
                return;
            }
        }
        
        $categories = $wpdb->get_results("SELECT * FROM {$this->categories_table} ORDER BY name ASC");
        $images = $project ? explode(',', $project->images) : array();
        ?>
        <div class="wrap portfolio-admin">
            <h1><?php echo $project ? 'Edit Project' : 'Add New Project'; ?></h1>
            
            <form id="project-edit-form" method="post">
                <table class="form-table project-edit-table">
                    <tr>
                        <th><label for="project-title">Project Title</label></th>
                        <td>
                            <input type="text" id="project-title" name="title" class="regular-text" value="<?php echo $project ? esc_attr($project->title) : ''; ?>" required>
                            <p class="description">The name of your project</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="project-category">Category</label></th>
                        <td>
                            <select id="project-category" name="category_id" class="regular-text" required>
                                <option value="">-- Select a Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat->id; ?>" <?php echo $project && $project->category_id == $cat->id ? 'selected' : ''; ?>>
                                        <?php echo esc_html($cat->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">Choose the category for this project</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="project-description">Description</label></th>
                        <td>
                            <?php 
                            $editor_settings = array(
                                'media_buttons' => false,
                                'textarea_rows' => 10,
                                'tinymce' => array(
                                    'toolbar1' => 'bold,italic,underline,strikethrough,forecolor,backcolor,hr,removeformat,charmap',
                                    'toolbar2' => 'formatselect,fontselect,fontsizeselect',
                                    'plugins' => 'colorpicker',
                                    'paste_as_text' => true,
                                )
                            );
                            wp_editor($project ? $project->description : '', 'project-description', $editor_settings);
                            ?>
                            <p class="description">Write a detailed description of your project with rich formatting options (bold, italic, underline, colors, etc.)</p>
                        </td>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="project-link">Project Link</label></th>
                        <td>
                            <input type="url" id="project-link" name="project_link" class="regular-text" value="<?php echo $project ? esc_attr($project->project_link) : ''; ?>" placeholder="https://example.com" required>
                            <p class="description">URL to view the live project or demo</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label>Images (for carousel)</label></th>
                        <td>
                            <p class="description">Upload multiple images. The first image will be used as the cover image.</p>
                            <button type="button" class="button upload-project-images-btn">Upload Images</button>
                            <div id="project-images-preview" class="images-preview">
                                <?php if ($project && !empty($images[0])): ?>
                                    <?php foreach ($images as $index => $image): ?>
                                        <div class="image-preview-item <?php echo $index === 0 ? 'cover-image' : ''; ?>" data-url="<?php echo esc_attr($image); ?>">
                                            <img src="<?php echo esc_url($image); ?>" alt="preview">
                                            <?php if ($index === 0): ?>
                                                <span class="cover-badge">Cover Image</span>
                                            <?php endif; ?>
                                            <button type="button" class="remove-image" data-index="<?php echo $index; ?>">×</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" id="project-images" name="images" value="<?php echo $project ? esc_attr($project->images) : ''; ?>">
                            <p class="description" style="margin-top: 10px;"><small>The first image will be displayed as the cover/main image in the slideshow</small></p>
                        </td>
                    </tr>
                </table>
                <input type="hidden" id="project-id" name="project_id" value="<?php echo $project ? $project->id : ''; ?>">
                
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php echo $project ? 'Update Project' : 'Create Project'; ?>
                    </button>
                    <a href="<?php echo admin_url('admin.php?page=modern-portfolio-projects'); ?>" class="button">Cancel</a>
                </p>
            </form>
        </div>
        <?php
    }
    
    public function save_category() {
        check_ajax_referer('portfolio_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        
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
            $wpdb->update($this->categories_table, $data, array('id' => $category_id));
            wp_send_json_success(array('message' => 'Category updated successfully', 'category' => $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->categories_table} WHERE id = %d", $category_id))));
        } else {
            // Check if slug already exists
            $existing = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$this->categories_table} WHERE slug = %s", $slug));
            if ($existing) {
                wp_send_json_error('Slug already exists');
            }
            
            $wpdb->insert($this->categories_table, $data);
            wp_send_json_success(array('message' => 'Category created successfully', 'category' => $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->categories_table} WHERE id = %d", $wpdb->insert_id))));
        }
    }
    
    public function delete_category() {
        check_ajax_referer('portfolio_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $category_id = intval($_POST['category_id']);
        
        // Set projects with this category to uncategorized
        $wpdb->update($this->table_name, array('category_id' => null), array('category_id' => $category_id));
        
        $wpdb->delete($this->categories_table, array('id' => $category_id));
        wp_send_json_success('Category deleted successfully');
    }
    
    public function get_categories() {
        check_ajax_referer('portfolio_nonce', 'nonce');
        
        global $wpdb;
        $categories = $wpdb->get_results("SELECT * FROM {$this->categories_table} ORDER BY name ASC");
        wp_send_json_success($categories);
    }
    
    public function get_project() {
        check_ajax_referer('portfolio_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $project_id = intval($_POST['project_id']);
        $project = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $project_id));
        
        wp_send_json_success($project);
    }
    
    public function get_project_details() {
        check_ajax_referer('portfolio_nonce', 'nonce');
        
        global $wpdb;
        $project_id = intval($_POST['project_id']);
        
        $project = $wpdb->get_row($wpdb->prepare(
            "SELECT p.*, c.name as category_name FROM {$this->table_name} p 
             LEFT JOIN {$this->categories_table} c ON p.category_id = c.id 
             WHERE p.id = %d", 
            $project_id
        ));
        
        if (!$project) {
            wp_send_json_error('Project not found');
        }
        
        wp_send_json_success($project);
    }
    
    public function save_portfolio_item() {
        check_ajax_referer('portfolio_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        global $wpdb;
        
        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        // Use wp_kses_post for rich text content from TinyMCE
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
            $result = $wpdb->update($this->table_name, $data, array('id' => $project_id));
            if ($result === false) {
                wp_send_json_error(array('message' => 'Error updating project: ' . $wpdb->last_error));
            }
            wp_send_json_success(array('message' => 'Project updated successfully', 'redirect' => admin_url('admin.php?page=modern-portfolio-projects')));
        } else {
            $result = $wpdb->insert($this->table_name, $data);
            if ($result === false) {
                wp_send_json_error(array('message' => 'Error creating project: ' . $wpdb->last_error));
            }
            wp_send_json_success(array('message' => 'Project created successfully', 'redirect' => admin_url('admin.php?page=modern-portfolio-projects')));
        }
    }
    
    public function delete_portfolio_item() {
        check_ajax_referer('portfolio_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $project_id = intval($_POST['project_id']);
        $wpdb->delete($this->table_name, array('id' => $project_id));
        
        wp_send_json_success('Project deleted successfully');
    }
    
    public function get_portfolio_items() {
        check_ajax_referer('portfolio_nonce', 'nonce');
        
        global $wpdb;
        $items = $wpdb->get_results("SELECT p.*, c.name as category_name FROM {$this->table_name} p LEFT JOIN {$this->categories_table} c ON p.category_id = c.id ORDER BY p.created_at DESC");
        
        wp_send_json_success($items);
    }
    
    public function portfolio_shortcode() {
        global $wpdb;
        $items = $wpdb->get_results("SELECT p.*, c.name as category_name, c.slug as category_slug FROM {$this->table_name} p LEFT JOIN {$this->categories_table} c ON p.category_id = c.id ORDER BY p.created_at DESC");
        
        // Get unique categories
        $categories = $wpdb->get_results("SELECT * FROM {$this->categories_table} ORDER BY name ASC");
        
        ob_start();
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
                                $short_description = $this->truncate_text($item->description, 150);
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
                            $short_description = $this->truncate_text($item->description, 150);
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
        
        <script>
        jQuery(document).ready(function($) {
            // Close modal on close button or background click
            $(document).on('click', '#project-details-modal .modal-close, #project-details-modal', function(e) {
                if (e.target === $('#project-details-modal')[0] || $(e.target).hasClass('modal-close')) {
                    $('#project-details-modal').fadeOut();
                }
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    private function truncate_text($text, $length = 150) {
        // Strip HTML tags first
        $text = wp_strip_all_tags($text);
        
        // Count words
        $words = explode(' ', $text);
        if (count($words) > $length) {
            $words = array_slice($words, 0, $length);
            $text = implode(' ', $words) . '...';
        }
        
        return $text;
    }
}

new Modern_Portfolio_Plugin();
