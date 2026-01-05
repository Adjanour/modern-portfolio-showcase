<?php
/**
 * Admin Projects List Template
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

// Data is passed from Portfolio_Admin::projects_page()
// Available variables: $database, $projects
?>

<div class="wrap portfolio-admin">
    <h1>Projects
        <a href="<?php echo esc_url(admin_url('admin.php?page=modern-portfolio-projects&edit=new')); ?>" class="page-title-action">Add New</a>
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
                            <td><?php echo esc_html(date_i18n('M d, Y', strtotime($project->created_at))); ?></td>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=modern-portfolio-projects&edit=' . $project->id)); ?>" class="button button-small">Edit</a>
                                <button class="button button-small button-link-delete delete-project-btn" data-id="<?php echo esc_attr($project->id); ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No projects found. <a href="<?php echo esc_url(admin_url('admin.php?page=modern-portfolio-projects&edit=new')); ?>">Create one now</a>!</p>
        <?php endif; ?>
    </div>
</div>
