<?php
/**
 * Admin Project Edit Template
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

global $wpdb;
$database = new Portfolio_Database();
$project = null;

$edit_id = isset($_GET['edit']) ? sanitize_text_field($_GET['edit']) : 0;

// Allow 'new' string or numeric ID
if ($edit_id !== 'new' && intval($edit_id) > 0) {
    $project = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$database->get_table_name()} WHERE id = %d", 
        intval($edit_id)
    ));
    
    if (!$project) {
        echo '<div class="wrap"><div class="error"><p>Project not found.</p></div></div>';
        return;
    }
}

$categories = $wpdb->get_results("SELECT * FROM {$database->get_categories_table()} ORDER BY name ASC");
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
