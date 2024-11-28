<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin
 * @author     Jackson Sá <wendelmax@gmail.com>
 */
class APWP_Admin {

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
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Registra as configurações do plugin
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/apwp-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/apwp-admin.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    /**
     * Add plugin admin menu and submenus
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        // Menu principal
        add_menu_page(
            'AmigoPet WP', // Page title
            'AmigoPet WP', // Menu title
            'manage_options', // Capability
            $this->plugin_name, // Menu slug
            array($this, 'display_plugin_dashboard'), // Function to display the page
            plugin_dir_url(__FILE__) . 'images/logo.svg', // Icon URL
            20 // Position
        );

        // Submenu Dashboard (mesmo que o menu principal)
        add_submenu_page(
            $this->plugin_name,
            'Dashboard',
            'Dashboard',
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_dashboard')
        );

        // Submenu Animais
        add_submenu_page(
            $this->plugin_name,
            'Animais',
            'Animais',
            'manage_options',
            'edit.php?post_type=animal'
        );

        // Submenu Adoções
        add_submenu_page(
            $this->plugin_name,
            'Adoções',
            'Adoções',
            'manage_options',
            $this->plugin_name . '-adoptions',
            array($this, 'display_adoptions_page')
        );

        // Submenu Adotantes
        add_submenu_page(
            $this->plugin_name,
            'Adotantes',
            'Adotantes',
            'manage_options',
            $this->plugin_name . '-adopters',
            array($this, 'display_adopters_page')
        );

        // Submenu Contratos
        add_submenu_page(
            $this->plugin_name,
            'Contratos',
            'Contratos',
            'manage_options',
            $this->plugin_name . '-contracts',
            array($this, 'display_contracts_page')
        );

        // Submenu Organizações
        add_submenu_page(
            $this->plugin_name,
            'Organizações',
            'Organizações',
            'manage_options',
            $this->plugin_name . '-organizations',
            array($this, 'display_organizations_page')
        );

        // Submenu Configurações
        add_submenu_page(
            $this->plugin_name,
            'Configurações',
            'Configurações',
            'manage_options',
            $this->plugin_name . '-settings',
            array($this, 'display_settings_page')
        );
    }

    /**
     * Display the dashboard page
     *
     * @since    1.0.0
     */
    public function display_plugin_dashboard() {
        include_once 'partials/apwp-admin-dashboard.php';
    }

    /**
     * Display the adoptions page
     *
     * @since    1.0.0
     */
    public function display_adoptions_page() {
        include_once 'partials/apwp-admin-adoptions.php';
    }

    /**
     * Display the adopters page
     *
     * @since    1.0.0
     */
    public function display_adopters_page() {
        include_once 'partials/apwp-admin-adopters.php';
    }

    /**
     * Display the contracts page
     *
     * @since    1.0.0
     */
    public function display_contracts_page() {
        include_once 'partials/apwp-admin-contracts.php';
    }

    /**
     * Display the organizations page
     *
     * @since    1.0.0
     */
    public function display_organizations_page() {
        include_once 'partials/apwp-admin-organizations.php';
    }

    /**
     * Display the settings page
     *
     * @since    1.0.0
     */
    public function display_settings_page() {
        include_once 'partials/apwp-admin-settings.php';
    }

    /**
     * Register plugin settings
     *
     * @since    1.0.0
     */
    public function register_settings() {
        // Registra o grupo de opções
        register_setting(
            'apwp_options', // Option group
            'apwp_options', // Option name
            array($this, 'sanitize_settings') // Sanitize callback
        );

        // Seção de Configurações Gerais
        add_settings_section(
            'apwp_general_section',
            __('Configurações Gerais', 'amigopet-wp'),
            array($this, 'render_general_section'),
            'apwp_settings'
        );

        // Campo: Nome da Organização
        add_settings_field(
            'org_name',
            __('Nome da Organização', 'amigopet-wp'),
            array($this, 'render_text_field'),
            'apwp_settings',
            'apwp_general_section',
            array(
                'id' => 'org_name',
                'desc' => __('Nome da sua organização ou abrigo', 'amigopet-wp')
            )
        );

        // Campo: Email da Organização
        add_settings_field(
            'org_email',
            __('Email da Organização', 'amigopet-wp'),
            array($this, 'render_text_field'),
            'apwp_settings',
            'apwp_general_section',
            array(
                'id' => 'org_email',
                'desc' => __('Email principal para notificações', 'amigopet-wp')
            )
        );

        // Seção de Configurações de Adoção
        add_settings_section(
            'apwp_adoption_section',
            __('Configurações de Adoção', 'amigopet-wp'),
            array($this, 'render_adoption_section'),
            'apwp_settings'
        );

        // Campo: Aprovação de Adoção
        add_settings_field(
            'adoption_approval',
            __('Aprovação de Adoção', 'amigopet-wp'),
            array($this, 'render_select_field'),
            'apwp_settings',
            'apwp_adoption_section',
            array(
                'id' => 'adoption_approval',
                'desc' => __('Como as adoções devem ser aprovadas', 'amigopet-wp'),
                'options' => array(
                    'auto' => __('Automática', 'amigopet-wp'),
                    'manual' => __('Manual', 'amigopet-wp')
                )
            )
        );

        // Seção de Configurações de Email
        add_settings_section(
            'apwp_email_section',
            __('Configurações de Email', 'amigopet-wp'),
            array($this, 'render_email_section'),
            'apwp_settings'
        );

        // Campo: Notificações por Email
        add_settings_field(
            'email_notifications',
            __('Notificações por Email', 'amigopet-wp'),
            array($this, 'render_checkbox_field'),
            'apwp_settings',
            'apwp_email_section',
            array(
                'id' => 'email_notifications',
                'desc' => __('Enviar notificações por email para adotantes e administradores', 'amigopet-wp')
            )
        );
    }

    /**
     * Sanitize settings
     *
     * @since    1.0.0
     * @param    array    $input    Array com os valores dos campos
     * @return   array    Array com os valores sanitizados
     */
    public function sanitize_settings($input) {
        $sanitized = array();

        if (isset($input['org_name'])) {
            $sanitized['org_name'] = sanitize_text_field($input['org_name']);
        }

        if (isset($input['org_email'])) {
            $sanitized['org_email'] = sanitize_email($input['org_email']);
        }

        if (isset($input['adoption_approval'])) {
            $sanitized['adoption_approval'] = sanitize_text_field($input['adoption_approval']);
        }

        if (isset($input['email_notifications'])) {
            $sanitized['email_notifications'] = absint($input['email_notifications']);
        }

        return $sanitized;
    }

    /**
     * Render general section description
     *
     * @since    1.0.0
     */
    public function render_general_section() {
        echo '<p>' . __('Configure as informações básicas da sua organização.', 'amigopet-wp') . '</p>';
    }

    /**
     * Render adoption section description
     *
     * @since    1.0.0
     */
    public function render_adoption_section() {
        echo '<p>' . __('Configure como o processo de adoção deve funcionar.', 'amigopet-wp') . '</p>';
    }

    /**
     * Render email section description
     *
     * @since    1.0.0
     */
    public function render_email_section() {
        echo '<p>' . __('Configure as notificações por email do sistema.', 'amigopet-wp') . '</p>';
    }

    /**
     * Render text field
     *
     * @since    1.0.0
     * @param    array    $args    Array com argumentos do campo
     */
    public function render_text_field($args) {
        $options = get_option('apwp_options');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : '';
        
        printf(
            '<input type="text" id="%1$s" name="apwp_options[%1$s]" value="%2$s" class="regular-text">',
            esc_attr($args['id']),
            esc_attr($value)
        );
        
        if (isset($args['desc'])) {
            printf('<p class="description">%s</p>', esc_html($args['desc']));
        }
    }

    /**
     * Render select field
     *
     * @since    1.0.0
     * @param    array    $args    Array com argumentos do campo
     */
    public function render_select_field($args) {
        $options = get_option('apwp_options');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : '';
        
        printf('<select id="%1$s" name="apwp_options[%1$s]">', esc_attr($args['id']));
        
        foreach ($args['options'] as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected($value, $key, false),
                esc_html($label)
            );
        }
        
        echo '</select>';
        
        if (isset($args['desc'])) {
            printf('<p class="description">%s</p>', esc_html($args['desc']));
        }
    }

    /**
     * Render checkbox field
     *
     * @since    1.0.0
     * @param    array    $args    Array com argumentos do campo
     */
    public function render_checkbox_field($args) {
        $options = get_option('apwp_options');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : 0;
        
        printf(
            '<input type="checkbox" id="%1$s" name="apwp_options[%1$s]" value="1" %2$s>',
            esc_attr($args['id']),
            checked($value, 1, false)
        );
        
        if (isset($args['desc'])) {
            printf('<p class="description">%s</p>', esc_html($args['desc']));
        }
    }
}
