<?php

/**
 * Classe responsável pelas configurações de exibição do plugin.
 *
 * @since      1.0.0
 * @package    AmigoPetWp
 * @subpackage AmigoPetWp/includes
 */
class APWP_Display_Settings {

    /**
     * Nome da opção no banco de dados.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $option_name    Nome da opção no banco de dados.
     */
    private $option_name = 'apwp_display_settings';

    /**
     * Configurações padrão.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $defaults    Configurações padrão.
     */
    private $defaults = array(
        'items_per_page' => 12,
        'show_filters' => true,
        'show_search' => true,
        'show_pagination' => true,
        'layout' => 'grid', // grid ou list
        'grid_columns' => 3,
        'show_species_filter' => true,
        'show_age_filter' => true,
        'show_size_filter' => true,
        'show_status_filter' => true,
        'enable_favorites' => true,
        'enable_sharing' => true,
        'thumbnail_size' => 'medium',
    );

    /**
     * Construtor.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Registra as configurações no WordPress.
     *
     * @since    1.0.0
     */
    public function register_settings() {
        register_setting(
            'apwp_display_settings',
            $this->option_name,
            array($this, 'sanitize_settings')
        );
    }

    /**
     * Sanitiza as configurações antes de salvar.
     *
     * @since    1.0.0
     * @param    array    $input    Configurações enviadas.
     * @return   array              Configurações sanitizadas.
     */
    public function sanitize_settings($input) {
        $sanitized = array();

        if (isset($input['items_per_page'])) {
            $sanitized['items_per_page'] = absint($input['items_per_page']);
        }

        if (isset($input['show_filters'])) {
            $sanitized['show_filters'] = (bool) $input['show_filters'];
        }

        if (isset($input['show_search'])) {
            $sanitized['show_search'] = (bool) $input['show_search'];
        }

        if (isset($input['show_pagination'])) {
            $sanitized['show_pagination'] = (bool) $input['show_pagination'];
        }

        if (isset($input['layout'])) {
            $sanitized['layout'] = sanitize_text_field($input['layout']);
        }

        if (isset($input['grid_columns'])) {
            $sanitized['grid_columns'] = absint($input['grid_columns']);
        }

        if (isset($input['show_species_filter'])) {
            $sanitized['show_species_filter'] = (bool) $input['show_species_filter'];
        }

        if (isset($input['show_age_filter'])) {
            $sanitized['show_age_filter'] = (bool) $input['show_age_filter'];
        }

        if (isset($input['show_size_filter'])) {
            $sanitized['show_size_filter'] = (bool) $input['show_size_filter'];
        }

        if (isset($input['show_status_filter'])) {
            $sanitized['show_status_filter'] = (bool) $input['show_status_filter'];
        }

        if (isset($input['enable_favorites'])) {
            $sanitized['enable_favorites'] = (bool) $input['enable_favorites'];
        }

        if (isset($input['enable_sharing'])) {
            $sanitized['enable_sharing'] = (bool) $input['enable_sharing'];
        }

        if (isset($input['thumbnail_size'])) {
            $sanitized['thumbnail_size'] = sanitize_text_field($input['thumbnail_size']);
        }

        return $sanitized;
    }

    /**
     * Obtém uma configuração específica.
     *
     * @since    1.0.0
     * @param    string    $key      Nome da configuração.
     * @param    mixed     $default  Valor padrão caso a configuração não exista.
     * @return   mixed               Valor da configuração.
     */
    public function get($key, $default = null) {
        $options = get_option($this->option_name, $this->defaults);
        
        if (isset($options[$key])) {
            return $options[$key];
        }
        
        if ($default !== null) {
            return $default;
        }
        
        return isset($this->defaults[$key]) ? $this->defaults[$key] : null;
    }

    /**
     * Obtém todas as configurações.
     *
     * @since    1.0.0
     * @return   array    Todas as configurações.
     */
    public function get_all() {
        return get_option($this->option_name, $this->defaults);
    }

    /**
     * Atualiza uma configuração específica.
     *
     * @since    1.0.0
     * @param    string    $key    Nome da configuração.
     * @param    mixed     $value  Valor da configuração.
     * @return   boolean           True em caso de sucesso.
     */
    public function update($key, $value) {
        $options = get_option($this->option_name, $this->defaults);
        $options[$key] = $value;
        return update_option($this->option_name, $options);
    }

    /**
     * Atualiza múltiplas configurações.
     *
     * @since    1.0.0
     * @param    array     $settings    Array de configurações.
     * @return   boolean                True em caso de sucesso.
     */
    public function update_multiple($settings) {
        $options = get_option($this->option_name, $this->defaults);
        $options = array_merge($options, $settings);
        return update_option($this->option_name, $options);
    }

    /**
     * Reseta as configurações para o padrão.
     *
     * @since    1.0.0
     * @return   boolean    True em caso de sucesso.
     */
    public function reset() {
        return update_option($this->option_name, $this->defaults);
    }
}
