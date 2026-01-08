<?php

/**
 * Portfolio Settings Handler
 * 
 * Manages plugin settings and customization options
 *
 * @package Modern_Portfolio_Showcase
 */

if (!defined('ABSPATH')) exit;

class Portfolio_Settings
{

    /**
     * Option name in database
     */
    const OPTION_NAME = 'modern_portfolio_settings';

    /**
     * Default settings
     */
    private static $defaults = array(
        // Colors
        'primary_color'         => '#4f46e5',
        'primary_hover_color'   => '#4338ca',
        'overlay_color'         => 'rgba(0, 0, 0, 0.6)',
        'text_color'            => '#ffffff',
        'title_bg_gradient'     => 'linear-gradient(transparent, rgba(0, 0, 0, 0.8))',

        // Carousel dimensions
        'card_width'            => 680,
        'card_height'           => 400,
        'card_border_radius'    => 16,

        // 3D Effect
        'perspective'           => 1200,
        'rotate_angle'          => 35,
        'side_card_blur'        => 1,
        'far_card_blur'         => 2,

        // Animation
        'transition_duration'   => 600,
        'auto_slide_delay'      => 5000,
        'auto_slide_enabled'    => true,

        // Display options
        'show_title_bar'        => true,
        'show_play_button'      => true,
        'show_nav_buttons'      => true,
        'show_view_toggle'      => true,

        // Grid view
        'grid_columns'          => 3,
        'grid_gap'              => 30,
    );

    /**
     * Get all settings with defaults
     */
    public static function get_settings()
    {
        $saved = get_option(self::OPTION_NAME, array());
        return wp_parse_args($saved, self::$defaults);
    }

    /**
     * Get a single setting
     */
    public static function get($key)
    {
        $settings = self::get_settings();
        return isset($settings[$key]) ? $settings[$key] : null;
    }

    /**
     * Save settings
     */
    public static function save($settings)
    {
        $sanitized = self::sanitize_settings($settings);
        return update_option(self::OPTION_NAME, $sanitized);
    }

    /**
     * Reset to defaults
     */
    public static function reset()
    {
        return update_option(self::OPTION_NAME, self::$defaults);
    }

    /**
     * Sanitize settings before saving
     */
    private static function sanitize_settings($settings)
    {
        $sanitized = array();

        // Colors
        $sanitized['primary_color'] = sanitize_hex_color($settings['primary_color'] ?? self::$defaults['primary_color']);
        $sanitized['primary_hover_color'] = sanitize_hex_color($settings['primary_hover_color'] ?? self::$defaults['primary_hover_color']);
        $sanitized['overlay_color'] = sanitize_text_field($settings['overlay_color'] ?? self::$defaults['overlay_color']);
        $sanitized['text_color'] = sanitize_hex_color($settings['text_color'] ?? self::$defaults['text_color']);
        $sanitized['title_bg_gradient'] = sanitize_text_field($settings['title_bg_gradient'] ?? self::$defaults['title_bg_gradient']);

        // Dimensions (integers)
        $sanitized['card_width'] = absint($settings['card_width'] ?? self::$defaults['card_width']);
        $sanitized['card_height'] = absint($settings['card_height'] ?? self::$defaults['card_height']);
        $sanitized['card_border_radius'] = absint($settings['card_border_radius'] ?? self::$defaults['card_border_radius']);

        // 3D Effect
        $sanitized['perspective'] = absint($settings['perspective'] ?? self::$defaults['perspective']);
        $sanitized['rotate_angle'] = absint($settings['rotate_angle'] ?? self::$defaults['rotate_angle']);
        $sanitized['side_card_blur'] = absint($settings['side_card_blur'] ?? self::$defaults['side_card_blur']);
        $sanitized['far_card_blur'] = absint($settings['far_card_blur'] ?? self::$defaults['far_card_blur']);

        // Animation
        $sanitized['transition_duration'] = absint($settings['transition_duration'] ?? self::$defaults['transition_duration']);
        $sanitized['auto_slide_delay'] = absint($settings['auto_slide_delay'] ?? self::$defaults['auto_slide_delay']);
        $sanitized['auto_slide_enabled'] = !empty($settings['auto_slide_enabled']);

        // Display options (booleans)
        $sanitized['show_title_bar'] = !empty($settings['show_title_bar']);
        $sanitized['show_play_button'] = !empty($settings['show_play_button']);
        $sanitized['show_nav_buttons'] = !empty($settings['show_nav_buttons']);
        $sanitized['show_view_toggle'] = !empty($settings['show_view_toggle']);

        // Grid
        $sanitized['grid_columns'] = absint($settings['grid_columns'] ?? self::$defaults['grid_columns']);
        $sanitized['grid_gap'] = absint($settings['grid_gap'] ?? self::$defaults['grid_gap']);

        return $sanitized;
    }

    /**
     * Get defaults (for reset functionality)
     */
    public static function get_defaults()
    {
        return self::$defaults;
    }

    /**
     * Generate CSS variables from settings
     */
    public static function generate_css_variables()
    {
        $settings = self::get_settings();

        $css = ":root {\n";
        $css .= "    /* Portfolio Colors */\n";
        $css .= "    --portfolio-primary: {$settings['primary_color']};\n";
        $css .= "    --portfolio-primary-hover: {$settings['primary_hover_color']};\n";
        $css .= "    --portfolio-overlay: {$settings['overlay_color']};\n";
        $css .= "    --portfolio-text: {$settings['text_color']};\n";
        $css .= "    --portfolio-title-bg: {$settings['title_bg_gradient']};\n";
        $css .= "    \n";
        $css .= "    /* Portfolio Dimensions */\n";
        $css .= "    --portfolio-card-width: {$settings['card_width']}px;\n";
        $css .= "    --portfolio-card-height: {$settings['card_height']}px;\n";
        $css .= "    --portfolio-border-radius: {$settings['card_border_radius']}px;\n";
        $css .= "    \n";
        $css .= "    /* Portfolio 3D Effect */\n";
        $css .= "    --portfolio-perspective: {$settings['perspective']}px;\n";
        $css .= "    --portfolio-rotate-angle: {$settings['rotate_angle']}deg;\n";
        $css .= "    --portfolio-blur-side: {$settings['side_card_blur']}px;\n";
        $css .= "    --portfolio-blur-far: {$settings['far_card_blur']}px;\n";
        $css .= "    \n";
        $css .= "    /* Portfolio Animation */\n";
        $css .= "    --portfolio-transition: {$settings['transition_duration']}ms;\n";
        $css .= "    \n";
        $css .= "    /* Portfolio Grid */\n";
        $css .= "    --portfolio-grid-columns: {$settings['grid_columns']};\n";
        $css .= "    --portfolio-grid-gap: {$settings['grid_gap']}px;\n";
        $css .= "}\n";

        return $css;
    }

    /**
     * Get settings for JavaScript
     */
    public static function get_js_settings()
    {
        $settings = self::get_settings();

        return array(
            'autoSlideDelay'    => $settings['auto_slide_delay'],
            'autoSlideEnabled'  => $settings['auto_slide_enabled'],
            'transitionDuration' => $settings['transition_duration'],
        );
    }
}
