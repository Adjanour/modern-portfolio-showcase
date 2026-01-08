<?php

/**
 * Portfolio Settings Admin Page Template
 * 
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

$settings = Portfolio_Settings::get_settings();
$defaults = Portfolio_Settings::get_defaults();
?>

<div class="wrap portfolio-settings-page">
    <h1>
        <span class="dashicons dashicons-art" style="font-size: 30px; margin-right: 10px;"></span>
        Portfolio Settings
    </h1>

    <p class="description">Customize the appearance and behavior of your portfolio showcase.</p>

    <form id="portfolio-settings-form" method="post">
        <?php wp_nonce_field('portfolio_settings_nonce', 'settings_nonce'); ?>

        <div class="portfolio-settings-container">

            <!-- Settings Tabs -->
            <div class="settings-tabs">
                <button type="button" class="settings-tab active" data-tab="colors">
                    <span class="dashicons dashicons-admin-appearance"></span>
                    Colors
                </button>
                <button type="button" class="settings-tab" data-tab="dimensions">
                    <span class="dashicons dashicons-image-crop"></span>
                    Dimensions
                </button>
                <button type="button" class="settings-tab" data-tab="3d-effect">
                    <span class="dashicons dashicons-format-gallery"></span>
                    3D Effect
                </button>
                <button type="button" class="settings-tab" data-tab="animation">
                    <span class="dashicons dashicons-controls-play"></span>
                    Animation
                </button>
                <button type="button" class="settings-tab" data-tab="display">
                    <span class="dashicons dashicons-visibility"></span>
                    Display
                </button>
            </div>

            <!-- Tab Contents -->
            <div class="settings-content">

                <!-- Colors Tab -->
                <div class="settings-panel active" data-panel="colors">
                    <h2>Color Settings</h2>
                    <p class="description">Customize the color scheme of your portfolio.</p>

                    <table class="form-table">
                        <tr>
                            <th><label for="primary_color">Primary Color</label></th>
                            <td>
                                <input type="color" id="primary_color" name="primary_color"
                                    value="<?php echo esc_attr($settings['primary_color']); ?>">
                                <span class="color-value"><?php echo esc_html($settings['primary_color']); ?></span>
                                <p class="description">Used for buttons, accents, and active states.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="primary_hover_color">Primary Hover Color</label></th>
                            <td>
                                <input type="color" id="primary_hover_color" name="primary_hover_color"
                                    value="<?php echo esc_attr($settings['primary_hover_color']); ?>">
                                <span class="color-value"><?php echo esc_html($settings['primary_hover_color']); ?></span>
                                <p class="description">Button color on hover.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="text_color">Text Color</label></th>
                            <td>
                                <input type="color" id="text_color" name="text_color"
                                    value="<?php echo esc_attr($settings['text_color']); ?>">
                                <span class="color-value"><?php echo esc_html($settings['text_color']); ?></span>
                                <p class="description">Text color on overlays and buttons.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="overlay_color">Overlay Color</label></th>
                            <td>
                                <input type="text" id="overlay_color" name="overlay_color" class="regular-text"
                                    value="<?php echo esc_attr($settings['overlay_color']); ?>">
                                <p class="description">Hover overlay background. Use rgba() for transparency, e.g., <code>rgba(0, 0, 0, 0.6)</code></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Dimensions Tab -->
                <div class="settings-panel" data-panel="dimensions">
                    <h2>Card Dimensions</h2>
                    <p class="description">Set the size of carousel cards.</p>

                    <table class="form-table">
                        <tr>
                            <th><label for="card_width">Card Width (px)</label></th>
                            <td>
                                <input type="number" id="card_width" name="card_width"
                                    value="<?php echo esc_attr($settings['card_width']); ?>"
                                    min="200" max="1200" step="10">
                                <p class="description">Base width of carousel cards. Default: <?php echo $defaults['card_width']; ?>px</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="card_height">Card Height (px)</label></th>
                            <td>
                                <input type="number" id="card_height" name="card_height"
                                    value="<?php echo esc_attr($settings['card_height']); ?>"
                                    min="150" max="800" step="10">
                                <p class="description">Base height of carousel cards. Default: <?php echo $defaults['card_height']; ?>px</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="card_border_radius">Border Radius (px)</label></th>
                            <td>
                                <input type="number" id="card_border_radius" name="card_border_radius"
                                    value="<?php echo esc_attr($settings['card_border_radius']); ?>"
                                    min="0" max="50" step="1">
                                <p class="description">Corner roundness of cards. Default: <?php echo $defaults['card_border_radius']; ?>px</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="grid_columns">Grid Columns</label></th>
                            <td>
                                <input type="number" id="grid_columns" name="grid_columns"
                                    value="<?php echo esc_attr($settings['grid_columns']); ?>"
                                    min="1" max="6" step="1">
                                <p class="description">Number of columns in grid view. Default: <?php echo $defaults['grid_columns']; ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="grid_gap">Grid Gap (px)</label></th>
                            <td>
                                <input type="number" id="grid_gap" name="grid_gap"
                                    value="<?php echo esc_attr($settings['grid_gap']); ?>"
                                    min="0" max="100" step="5">
                                <p class="description">Spacing between grid items. Default: <?php echo $defaults['grid_gap']; ?>px</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- 3D Effect Tab -->
                <div class="settings-panel" data-panel="3d-effect">
                    <h2>3D Carousel Effect</h2>
                    <p class="description">Fine-tune the 3D coverflow appearance.</p>

                    <table class="form-table">
                        <tr>
                            <th><label for="perspective">Perspective (px)</label></th>
                            <td>
                                <input type="range" id="perspective" name="perspective"
                                    value="<?php echo esc_attr($settings['perspective']); ?>"
                                    min="600" max="2000" step="50">
                                <span class="range-value"><?php echo esc_html($settings['perspective']); ?>px</span>
                                <p class="description">Lower = more dramatic 3D effect. Default: <?php echo $defaults['perspective']; ?>px</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="rotate_angle">Rotation Angle (deg)</label></th>
                            <td>
                                <input type="range" id="rotate_angle" name="rotate_angle"
                                    value="<?php echo esc_attr($settings['rotate_angle']); ?>"
                                    min="0" max="60" step="5">
                                <span class="range-value"><?php echo esc_html($settings['rotate_angle']); ?>°</span>
                                <p class="description">How much side cards rotate. Default: <?php echo $defaults['rotate_angle']; ?>°</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="side_card_blur">Side Card Blur (px)</label></th>
                            <td>
                                <input type="range" id="side_card_blur" name="side_card_blur"
                                    value="<?php echo esc_attr($settings['side_card_blur']); ?>"
                                    min="0" max="10" step="1">
                                <span class="range-value"><?php echo esc_html($settings['side_card_blur']); ?>px</span>
                                <p class="description">Blur on prev-1/next-1 cards. Default: <?php echo $defaults['side_card_blur']; ?>px</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="far_card_blur">Far Card Blur (px)</label></th>
                            <td>
                                <input type="range" id="far_card_blur" name="far_card_blur"
                                    value="<?php echo esc_attr($settings['far_card_blur']); ?>"
                                    min="0" max="10" step="1">
                                <span class="range-value"><?php echo esc_html($settings['far_card_blur']); ?>px</span>
                                <p class="description">Blur on prev-2/next-2 cards. Default: <?php echo $defaults['far_card_blur']; ?>px</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Animation Tab -->
                <div class="settings-panel" data-panel="animation">
                    <h2>Animation Settings</h2>
                    <p class="description">Control timing and auto-slide behavior.</p>

                    <table class="form-table">
                        <tr>
                            <th><label for="transition_duration">Transition Speed (ms)</label></th>
                            <td>
                                <input type="range" id="transition_duration" name="transition_duration"
                                    value="<?php echo esc_attr($settings['transition_duration']); ?>"
                                    min="200" max="1500" step="100">
                                <span class="range-value"><?php echo esc_html($settings['transition_duration']); ?>ms</span>
                                <p class="description">How fast slides transition. Default: <?php echo $defaults['transition_duration']; ?>ms</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="auto_slide_enabled">Auto-Slide</label></th>
                            <td>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="auto_slide_enabled" name="auto_slide_enabled"
                                        <?php checked($settings['auto_slide_enabled']); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label">Enable automatic slide advancement</span>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="auto_slide_delay">Auto-Slide Delay (ms)</label></th>
                            <td>
                                <input type="range" id="auto_slide_delay" name="auto_slide_delay"
                                    value="<?php echo esc_attr($settings['auto_slide_delay']); ?>"
                                    min="2000" max="15000" step="500">
                                <span class="range-value"><?php echo esc_html($settings['auto_slide_delay'] / 1000); ?>s</span>
                                <p class="description">Time between auto-slides. Default: <?php echo $defaults['auto_slide_delay'] / 1000; ?>s</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Display Tab -->
                <div class="settings-panel" data-panel="display">
                    <h2>Display Options</h2>
                    <p class="description">Show or hide various UI elements.</p>

                    <table class="form-table">
                        <tr>
                            <th>Title Bar</th>
                            <td>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="show_title_bar" name="show_title_bar"
                                        <?php checked($settings['show_title_bar']); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label">Show project title at bottom of cards</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Play Button</th>
                            <td>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="show_play_button" name="show_play_button"
                                        <?php checked($settings['show_play_button']); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label">Show play button on video slides</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Navigation Buttons</th>
                            <td>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="show_nav_buttons" name="show_nav_buttons"
                                        <?php checked($settings['show_nav_buttons']); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label">Show prev/next navigation arrows</span>
                            </td>
                        </tr>
                        <tr>
                            <th>View Toggle</th>
                            <td>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="show_view_toggle" name="show_view_toggle"
                                        <?php checked($settings['show_view_toggle']); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label">Show carousel/grid view toggle buttons</span>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>

        </div>

        <!-- Action Buttons -->
        <div class="settings-actions">
            <button type="submit" class="button button-primary button-large" id="save-settings">
                <span class="dashicons dashicons-saved"></span>
                Save Settings
            </button>
            <button type="button" class="button button-secondary" id="reset-settings">
                <span class="dashicons dashicons-image-rotate"></span>
                Reset to Defaults
            </button>
        </div>

        <!-- Shortcode Reference -->
        <div class="shortcode-reference">
            <h2>Shortcode Reference</h2>
            <p>Use the shortcode below to display your portfolio:</p>

            <div class="shortcode-box">
                <code>[modern_portfolio]</code>
                <button type="button" class="button copy-shortcode" data-shortcode="[modern_portfolio]">Copy</button>
            </div>

            <h3>Shortcode Attributes</h3>
            <p>Override settings per shortcode instance:</p>

            <table class="widefat shortcode-attrs-table">
                <thead>
                    <tr>
                        <th>Attribute</th>
                        <th>Description</th>
                        <th>Example</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>category</code></td>
                        <td>Filter by category slug</td>
                        <td><code>[modern_portfolio category="web-design"]</code></td>
                    </tr>
                    <tr>
                        <td><code>limit</code></td>
                        <td>Maximum projects to show</td>
                        <td><code>[modern_portfolio limit="6"]</code></td>
                    </tr>
                    <tr>
                        <td><code>view</code></td>
                        <td>Default view (carousel or grid)</td>
                        <td><code>[modern_portfolio view="grid"]</code></td>
                    </tr>
                    <tr>
                        <td><code>auto_slide</code></td>
                        <td>Enable/disable auto-slide</td>
                        <td><code>[modern_portfolio auto_slide="false"]</code></td>
                    </tr>
                    <tr>
                        <td><code>primary_color</code></td>
                        <td>Override primary color</td>
                        <td><code>[modern_portfolio primary_color="#ff6600"]</code></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </form>
</div>

<style>
    /* Settings Page Styles */
    .portfolio-settings-page {
        max-width: 1200px;
    }

    .portfolio-settings-container {
        display: flex;
        gap: 30px;
        margin-top: 20px;
        background: #fff;
        border: 1px solid #ccd0d4;
        border-radius: 8px;
        overflow: hidden;
    }

    .settings-tabs {
        width: 200px;
        background: #f6f7f7;
        border-right: 1px solid #ccd0d4;
        padding: 20px 0;
    }

    .settings-tab {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 12px 20px;
        border: none;
        background: none;
        cursor: pointer;
        font-size: 14px;
        color: #50575e;
        text-align: left;
        transition: all 0.2s;
    }

    .settings-tab:hover {
        background: #e9e9e9;
        color: #1d2327;
    }

    .settings-tab.active {
        background: #fff;
        color: #2271b1;
        border-left: 3px solid #2271b1;
        font-weight: 600;
    }

    .settings-tab .dashicons {
        font-size: 18px;
        width: 18px;
        height: 18px;
    }

    .settings-content {
        flex: 1;
        padding: 30px;
    }

    .settings-panel {
        display: none;
    }

    .settings-panel.active {
        display: block;
    }

    .settings-panel h2 {
        margin-top: 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #e0e0e0;
    }

    .form-table th {
        width: 200px;
        padding: 20px 10px 20px 0;
    }

    .form-table td {
        padding: 15px 10px;
    }

    /* Color picker with value display */
    input[type="color"] {
        width: 50px;
        height: 35px;
        padding: 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
    }

    .color-value {
        margin-left: 10px;
        font-family: monospace;
        color: #666;
    }

    /* Range slider */
    input[type="range"] {
        width: 300px;
        vertical-align: middle;
    }

    .range-value {
        display: inline-block;
        min-width: 60px;
        margin-left: 15px;
        font-weight: 600;
        color: #2271b1;
    }

    /* Toggle switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.3s;
        border-radius: 26px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked+.toggle-slider {
        background-color: #2271b1;
    }

    input:checked+.toggle-slider:before {
        transform: translateX(24px);
    }

    .toggle-label {
        margin-left: 15px;
        vertical-align: super;
    }

    /* Action buttons */
    .settings-actions {
        margin-top: 30px;
        padding: 20px;
        background: #f6f7f7;
        border: 1px solid #ccd0d4;
        border-radius: 8px;
        display: flex;
        gap: 15px;
    }

    .settings-actions .button {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .settings-actions .dashicons {
        font-size: 16px;
        width: 16px;
        height: 16px;
    }

    /* Shortcode reference */
    .shortcode-reference {
        margin-top: 30px;
        padding: 25px;
        background: #fff;
        border: 1px solid #ccd0d4;
        border-radius: 8px;
    }

    .shortcode-reference h2 {
        margin-top: 0;
    }

    .shortcode-box {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: #f0f0f1;
        border-radius: 6px;
        margin: 15px 0;
    }

    .shortcode-box code {
        font-size: 16px;
        padding: 8px 15px;
        background: #fff;
        border-radius: 4px;
    }

    .shortcode-attrs-table {
        margin-top: 15px;
    }

    .shortcode-attrs-table code {
        background: #f0f0f1;
        padding: 2px 6px;
        border-radius: 3px;
    }

    /* Responsive */
    @media (max-width: 782px) {
        .portfolio-settings-container {
            flex-direction: column;
        }

        .settings-tabs {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            border-right: none;
            border-bottom: 1px solid #ccd0d4;
        }

        .settings-tab {
            width: auto;
            flex: 1;
            justify-content: center;
            padding: 10px 15px;
        }

        .settings-tab.active {
            border-left: none;
            border-bottom: 3px solid #2271b1;
        }

        input[type="range"] {
            width: 100%;
        }
    }
</style>

<script>
    jQuery(document).ready(function($) {
        // Tab switching
        $('.settings-tab').on('click', function() {
            const tabId = $(this).data('tab');

            $('.settings-tab').removeClass('active');
            $(this).addClass('active');

            $('.settings-panel').removeClass('active');
            $(`.settings-panel[data-panel="${tabId}"]`).addClass('active');
        });

        // Range slider value update
        $('input[type="range"]').on('input', function() {
            const $value = $(this).siblings('.range-value');
            let val = $(this).val();

            // Format based on input name
            if ($(this).attr('name') === 'auto_slide_delay') {
                val = (val / 1000) + 's';
            } else if ($(this).attr('name') === 'rotate_angle') {
                val = val + '°';
            } else if ($(this).attr('name').includes('blur')) {
                val = val + 'px';
            } else {
                val = val + 'px';
            }

            $value.text(val);
        });

        // Color picker value update
        $('input[type="color"]').on('input', function() {
            $(this).siblings('.color-value').text($(this).val());
        });

        // Save settings
        $('#portfolio-settings-form').on('submit', function(e) {
            e.preventDefault();

            const $btn = $('#save-settings');
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_portfolio_settings',
                    nonce: $('#settings_nonce').val(),
                    settings: $(this).serialize()
                },
                success: function(response) {
                    if (response.success) {
                        $btn.html('<span class="dashicons dashicons-yes"></span> Saved!');
                        setTimeout(() => {
                            $btn.html('<span class="dashicons dashicons-saved"></span> Save Settings').prop('disabled', false);
                        }, 2000);
                    } else {
                        alert('Error saving settings: ' + response.data);
                        $btn.html('<span class="dashicons dashicons-saved"></span> Save Settings').prop('disabled', false);
                    }
                },
                error: function() {
                    alert('Error saving settings');
                    $btn.html('<span class="dashicons dashicons-saved"></span> Save Settings').prop('disabled', false);
                }
            });
        });

        // Reset settings
        $('#reset-settings').on('click', function() {
            if (!confirm('Are you sure you want to reset all settings to defaults?')) return;

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'reset_portfolio_settings',
                    nonce: $('#settings_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error resetting settings');
                    }
                }
            });
        });

        // Copy shortcode
        $('.copy-shortcode').on('click', function() {
            const shortcode = $(this).data('shortcode');
            navigator.clipboard.writeText(shortcode).then(() => {
                $(this).text('Copied!');
                setTimeout(() => $(this).text('Copy'), 2000);
            });
        });
    });
</script>