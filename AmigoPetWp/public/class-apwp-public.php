<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/AmigoPetWp/amigopet-wp
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

        // Adiciona o objeto de configuração para o JavaScript
        wp_localize_script(
            $this->plugin_name,
            'apwpAjax',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('apwp-ajax-nonce')
            )
        );

        // Registra os handlers de AJAX
        add_action('wp_ajax_get_pet_details', array($this, 'get_pet_details'));
        add_action('wp_ajax_nopriv_get_pet_details', array($this, 'get_pet_details'));
        add_action('wp_ajax_submit_adoption_request', array($this, 'submit_adoption_request'));
        add_action('wp_ajax_nopriv_submit_adoption_request', array($this, 'submit_adoption_request'));
    }

    /**
     * Registra os shortcodes do plugin
     *
     * @since    1.0.0
     */
    public function register_shortcodes() {
        // Grid de pets
        add_shortcode('apwp_animals_grid', array($this, 'render_animals_grid'));
        
        // Formulário de adoção
        add_shortcode('apwp_adoption_form', array($this, 'render_adoption_form'));
        
        // Pets em destaque
        add_shortcode('apwp_featured_pets', array($this, 'render_featured_pets'));
        
        // Contador de pets
        add_shortcode('apwp_pet_counter', array($this, 'render_pet_counter'));
        
        // Busca de pets
        add_shortcode('apwp_pet_search', array($this, 'render_pet_search'));
        
        // Pet individual
        add_shortcode('apwp_single_pet', array($this, 'render_single_pet'));
    }

    /**
     * Renderiza a grade de pets
     */
    public function render_animals_grid($atts) {
        $atts = shortcode_atts(array(
            'species' => '',
            'breed' => '',
            'age' => '',
            'size' => '',
            'gender' => '',
            'limit' => 12,
            'order' => 'DESC'
        ), $atts);

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/animals-grid.php';
        return ob_get_clean();
    }

    /**
     * Renderiza o formulário de adoção
     */
    public function render_adoption_form($atts) {
        $atts = shortcode_atts(array(
            'pet_id' => 0,
            'title' => __('Formulário de Adoção', 'amigopet-wp'),
            'success_message' => __('Solicitação enviada com sucesso!', 'amigopet-wp')
        ), $atts);

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/adoption-form.php';
        return ob_get_clean();
    }

    /**
     * Renderiza o carrossel de pets em destaque
     */
    public function render_featured_pets($atts) {
        $atts = shortcode_atts(array(
            'count' => 4,
            'autoplay' => true,
            'interval' => 5000
        ), $atts);

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/featured-pets.php';
        return ob_get_clean();
    }

    /**
     * Renderiza os contadores de pets
     */
    public function render_pet_counter($atts) {
        $atts = shortcode_atts(array(
            'show' => 'all',
            'layout' => 'inline'
        ), $atts);

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/pet-counter.php';
        return ob_get_clean();
    }

    /**
     * Renderiza o formulário de busca
     */
    public function render_pet_search($atts) {
        $atts = shortcode_atts(array(
            'fields' => 'species,breed,age,size,gender',
            'button_text' => __('Buscar Pet', 'amigopet-wp')
        ), $atts);

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/pet-search.php';
        return ob_get_clean();
    }

    /**
     * Renderiza detalhes de um pet específico
     */
    public function render_single_pet($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'show_form' => true
        ), $atts);

        if (!$atts['id']) {
            return __('ID do pet não fornecido', 'amigopet-wp');
        }

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/single-pet.php';
        return ob_get_clean();
    }

    /**
     * Handler para o AJAX de detalhes do pet
     *
     * @since    1.0.0
     */
    public function get_pet_details() {
        check_ajax_referer('apwp-ajax-nonce', 'nonce');
        
        $pet_id = isset($_POST['pet_id']) ? intval($_POST['pet_id']) : 0;
        if (!$pet_id) {
            wp_send_json_error('ID do pet não fornecido');
        }

        $pet = new APWP_Pet();
        $pet_data = $pet->get($pet_id);
        
        if (!$pet_data) {
            wp_send_json_error('Pet não encontrado');
        }

        wp_send_json_success($pet_data);
    }

    /**
     * Handler para o AJAX de solicitação de adoção
     *
     * @since    1.0.0
     */
    public function submit_adoption_request() {
        check_ajax_referer('apwp-ajax-nonce', 'nonce');
        
        $pet_id = isset($_POST['pet_id']) ? intval($_POST['pet_id']) : 0;
        if (!$pet_id) {
            wp_send_json_error('ID do pet não fornecido');
        }

        // Validação dos campos do formulário
        $required_fields = array('adopter_name', 'adopter_email', 'adopter_phone', 'adopter_address', 'adoption_reason');
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                wp_send_json_error(sprintf(__('Campo %s é obrigatório', 'amigopet-wp'), $field));
            }
        }

        // TODO: Implementar lógica de salvamento da solicitação de adoção
        
        wp_send_json_success(__('Solicitação de adoção enviada com sucesso!', 'amigopet-wp'));
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
