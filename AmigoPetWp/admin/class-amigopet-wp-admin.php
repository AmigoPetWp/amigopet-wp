<?php
/**
 * A classe que gerencia a área administrativa do plugin
 *
 * @package AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin
 */
class AmigoPet_Wp_Admin {

    /**
     * O ID do plugin
     *
     * @var string
     */
    private $plugin_name;

    /**
     * A versão do plugin
     *
     * @var string
     */
    private $version;

    /**
     * Inicializa a classe e define suas propriedades
     *
     * @param string $plugin_name O nome do plugin
     * @param string $version A versão do plugin
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Registra os estilos do painel administrativo
     */
    public function enqueue_styles() {
        // Estilos base do admin
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            APWP_PLUGIN_URL . 'admin/css/apwp-admin.css',
            array(),
            $this->version,
            'all'
        );

        // Carrega estilos específicos baseado na página atual
        $current_screen = get_current_screen();
        if ($current_screen) {
            switch ($current_screen->id) {
                case 'toplevel_page_amigopet-wp':
                    wp_enqueue_style(
                        $this->plugin_name . '-dashboard',
                        APWP_PLUGIN_URL . 'admin/css/apwp-admin-dashboard.css',
                        array($this->plugin_name . '-admin'),
                        $this->version,
                        'all'
                    );
                    break;

                case 'amigopet-wp_page_amigopet-wp-pets':
                    wp_enqueue_style(
                        $this->plugin_name . '-pet-list',
                        APWP_PLUGIN_URL . 'admin/css/apwp-admin-pet-list.css',
                        array($this->plugin_name . '-admin'),
                        $this->version,
                        'all'
                    );
                    break;

                case 'amigopet-wp_page_amigopet-wp-adoptions':
                    wp_enqueue_style(
                        $this->plugin_name . '-adoption-list',
                        APWP_PLUGIN_URL . 'admin/css/apwp-admin-adoption-list.css',
                        array($this->plugin_name . '-admin'),
                        $this->version,
                        'all'
                    );
                    break;

                case 'amigopet-wp_page_amigopet-wp-adopters':
                    wp_enqueue_style(
                        $this->plugin_name . '-adopter-list',
                        APWP_PLUGIN_URL . 'admin/css/apwp-admin-adopter-list.css',
                        array($this->plugin_name . '-admin'),
                        $this->version,
                        'all'
                    );
                    break;

                case 'amigopet-wp_page_amigopet-wp-volunteers':
                    wp_enqueue_style(
                        $this->plugin_name . '-volunteer-list',
                        APWP_PLUGIN_URL . 'admin/css/apwp-admin-volunteer-list.css',
                        array($this->plugin_name . '-admin'),
                        $this->version,
                        'all'
                    );
                    break;

                case 'amigopet-wp_page_amigopet-wp-organizations':
                    wp_enqueue_style(
                        $this->plugin_name . '-organization-list',
                        APWP_PLUGIN_URL . 'admin/css/apwp-admin-organization-list.css',
                        array($this->plugin_name . '-admin'),
                        $this->version,
                        'all'
                    );
                    break;

                case 'amigopet-wp_page_amigopet-wp-terms':
                    wp_enqueue_style(
                        $this->plugin_name . '-terms-list',
                        APWP_PLUGIN_URL . 'admin/css/apwp-admin-terms-list.css',
                        array($this->plugin_name . '-admin'),
                        $this->version,
                        'all'
                    );
                    break;

                case 'amigopet-wp_page_amigopet-wp-settings':
                    wp_enqueue_style(
                        $this->plugin_name . '-settings',
                        APWP_PLUGIN_URL . 'admin/css/apwp-admin-settings.css',
                        array($this->plugin_name . '-admin'),
                        $this->version,
                        'all'
                    );
                    break;
            }
        }
        $this->enqueue_adminlte_assets();
    }

    /**
     * Registra os scripts do painel administrativo
     */
    public function enqueue_scripts() {
        // Scripts base do admin
        wp_enqueue_script(
            $this->plugin_name . '-admin',
            APWP_PLUGIN_URL . 'admin/js/apwp-admin.js',
            array('jquery'),
            $this->version,
            true
        );
        
        // Localização para scripts
        wp_localize_script(
            $this->plugin_name . '-admin',
            'apwpAdmin',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('apwp_admin_nonce'),
                'i18n' => array(
                    'confirmDelete' => __('Tem certeza que deseja excluir "%s"?', 'amigopet-wp'),
                    'confirmArchive' => __('Tem certeza que deseja arquivar?', 'amigopet-wp'),
                    'confirmBlock' => __('Tem certeza que deseja bloquear?', 'amigopet-wp'),
                    'error' => __('Ocorreu um erro. Tente novamente.', 'amigopet-wp'),
                    'success' => __('Operação realizada com sucesso!', 'amigopet-wp')
                )
            )
        );

        // Carrega scripts específicos baseado na página atual
        $current_screen = get_current_screen();
        if ($current_screen) {
            switch ($current_screen->id) {
                case 'toplevel_page_amigopet-wp':
                    wp_enqueue_script(
                        $this->plugin_name . '-dashboard',
                        APWP_PLUGIN_URL . 'admin/js/apwp-admin-dashboard.js',
                        array($this->plugin_name . '-admin'),
                        $this->version,
                        true
                    );
                    break;

                case 'amigopet-wp_page_amigopet-wp-pets':
                    wp_enqueue_script(
                        $this->plugin_name . '-pet-list',
                        APWP_PLUGIN_URL . 'admin/js/apwp-admin-pet-list.js',
                        array($this->plugin_name . '-admin'),
                        $this->version,
                        true
                    );
                    break;

                // Adicione outros casos conforme necessário
            }
        }
    }

    /**
     * Registra os menus do painel administrativo
     */
    public function add_admin_menu() {
        // Menu principal
        add_menu_page(
            __('AmigoPet', 'amigopet-wp'),
            __('AmigoPet', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp',
            array($this, 'display_dashboard_page'),
            'dashicons-pets',
            30
        );

        // Submenus
        add_submenu_page(
            'amigopet-wp',
            __('Dashboard', 'amigopet-wp'),
            __('Dashboard', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp',
            array($this, 'display_dashboard_page')
        );

        add_submenu_page(
            'amigopet-wp',
            __('Pets', 'amigopet-wp'),
            __('Pets', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-pets',
            array($this, 'display_pets_page')
        );

        add_submenu_page(
            'amigopet-wp',
            __('Adoções', 'amigopet-wp'),
            __('Adoções', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-adoptions',
            array($this, 'display_adoptions_page')
        );

        add_submenu_page(
            'amigopet-wp',
            __('Adotantes', 'amigopet-wp'),
            __('Adotantes', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-adopters',
            array($this, 'display_adopters_page')
        );

        add_submenu_page(
            'amigopet-wp',
            __('Voluntários', 'amigopet-wp'),
            __('Voluntários', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-volunteers',
            array($this, 'display_volunteers_page')
        );

        add_submenu_page(
            'amigopet-wp',
            __('Organizações', 'amigopet-wp'),
            __('Organizações', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-organizations',
            array($this, 'display_organizations_page')
        );

        add_submenu_page(
            'amigopet-wp',
            __('Termos', 'amigopet-wp'),
            __('Termos', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-terms',
            array($this, 'display_terms_page')
        );

        add_submenu_page(
            'amigopet-wp',
            __('Configurações', 'amigopet-wp'),
            __('Configurações', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-settings',
            array($this, 'display_settings_page')
        );
    }

    /**
     * Registra os hooks do admin.
     *
     * @since    1.0.0
     */
    private function define_admin_hooks() {
        // Adiciona o menu do plugin
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Registra os scripts e estilos do admin
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Registra as ações AJAX
        add_action('wp_ajax_delete_organization', array($this, 'ajax_delete_organization'));
    }

    /**
     * Exibe a página do dashboard
     */
    public function display_dashboard_page() {
        require_once APWP_PLUGIN_DIR . 'admin/partials/dashboard/apwp-admin-dashboard.php';
    }

    /**
     * Exibe a página de pets
     */
    public function display_pets_page() {
        require_once APWP_PLUGIN_DIR . 'admin/partials/pet/apwp-admin-pet-list.php';
    }

    /**
     * Exibe a página de adoções
     */
    public function display_adoptions_page() {
        require_once APWP_PLUGIN_DIR . 'admin/partials/adoption/apwp-admin-adoption-list.php';
    }

    /**
     * Exibe a página de adotantes
     */
    public function display_adopters_page() {
        require_once APWP_PLUGIN_DIR . 'admin/partials/adopter/apwp-admin-adopter-list.php';
    }

    /**
     * Exibe a página de voluntários
     */
    public function display_volunteers_page() {
        require_once APWP_PLUGIN_DIR . 'admin/partials/volunteer/apwp-admin-volunteer-list.php';
    }

    /**
     * Exibe a página de organizações
     */
    public function display_organizations_page() {
        require_once APWP_PLUGIN_DIR . 'admin/partials/organization/apwp-admin-organization-list.php';
    }

    /**
     * Exibe a página de termos
     */
    public function display_terms_page() {
        require_once APWP_PLUGIN_DIR . 'admin/partials/terms/apwp-admin-terms-list.php';
    }

    /**
     * Exibe a página de configurações
     */
    public function display_settings_page() {
        require_once APWP_PLUGIN_DIR . 'admin/partials/settings/apwp-admin-settings.php';
    }

    /**
     * Manipula a requisição AJAX para excluir uma organização
     */
    public function ajax_delete_organization() {
        // Verifica o nonce
        check_ajax_referer('apwp_admin_nonce', '_wpnonce');
        
        // Verifica as permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array(
                'message' => __('Você não tem permissão para executar esta ação.', 'amigopet-wp')
            ));
        }
        
        // Obtém o ID da organização
        $id = intval($_POST['id']);
        if (!$id) {
            wp_send_json_error(array(
                'message' => __('ID da organização inválido.', 'amigopet-wp')
            ));
        }
        
        // Tenta excluir a organização
        $organization = new APWP_Organization();
        if ($organization->delete($id)) {
            wp_send_json_success(array(
                'message' => __('Organização excluída com sucesso.', 'amigopet-wp')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Erro ao excluir a organização.', 'amigopet-wp')
            ));
        }
    }

    /**
     * Register the AdminLTE styles and scripts for the admin area.
     *
     * @since    1.0.0
     */
    private function register_adminlte_assets() {
        // AdminLTE CSS
        wp_register_style(
            'adminlte',
            'https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css',
            array(),
            '3.2.0',
            'all'
        );

        // Font Awesome
        wp_register_style(
            'fontawesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
            array(),
            '5.15.4',
            'all'
        );

        // AdminLTE JS
        wp_register_script(
            'adminlte',
            'https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js',
            array('jquery'),
            '3.2.0',
            true
        );

        // Custom AdminLTE CSS
        wp_register_style(
            'apwp-adminlte',
            plugin_dir_url(__FILE__) . 'css/apwp-admin-lte.css',
            array('adminlte'),
            APWP_VERSION,
            'all'
        );
    }

    /**
     * Enqueue AdminLTE assets for plugin pages
     */
    private function enqueue_adminlte_assets() {
        if ($this->is_plugin_page()) {
            wp_enqueue_style('adminlte');
            wp_enqueue_style('fontawesome');
            wp_enqueue_style('apwp-adminlte');
            wp_enqueue_script('adminlte');
        }
    }

    /**
     * Check if current page is a plugin page
     */
    private function is_plugin_page() {
        if (!function_exists('get_current_screen')) {
            return false;
        }

        $screen = get_current_screen();
        return strpos($screen->id, 'amigopet-wp') !== false;
    }
}
