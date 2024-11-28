<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/public
 * @author     Your Name <email@example.com>
 */
class APWP_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/apwp-public.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/apwp-public.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    /**
     * Register custom post types for the plugin
     *
     * @since    1.0.0
     */
    public function register_post_types() {
        // Register Animal post type
        register_post_type('animal', array(
            'labels' => array(
                'name' => __('Animals', 'amigopet-wp'),
                'singular_name' => __('Animal', 'amigopet-wp'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_icon' => 'dashicons-pets',
        ));
    }

    /**
     * Register custom taxonomies for the plugin
     *
     * @since    1.0.0
     */
    public function register_taxonomies() {
        // Register Animal Species taxonomy
        register_taxonomy('animal_species', 'animal', array(
            'labels' => array(
                'name' => __('Species', 'amigopet-wp'),
                'singular_name' => __('Species', 'amigopet-wp'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
        ));

        // Register Animal Breed taxonomy
        register_taxonomy('animal_breed', 'animal', array(
            'labels' => array(
                'name' => __('Breeds', 'amigopet-wp'),
                'singular_name' => __('Breed', 'amigopet-wp'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
        ));
    }
}
