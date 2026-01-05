<?php
/**
 * Admin Categories Template
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

global $wpdb;
$database = new Portfolio_Database();
$categories = $wpdb->get_results("SELECT * FROM {$database->get_categories_table()} ORDER BY created_at DESC");
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
                                    <td><?php echo date_i18n('M d, Y', strtotime($cat->created_at)); ?></td>
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
