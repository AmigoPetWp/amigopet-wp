<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/AmigoPetWp/amigopet-wp
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
 * @author     Jackson Sá <jacksonwendel@gmail.com>
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
        
        // Adiciona o menu do plugin
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        
        // Enfileira os estilos e scripts admin
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Filtro para adicionar ícones aos submenus
        add_filter('parent_file', array($this, 'add_submenu_icons'));

        // Filtro para modificar as classes do menu
        add_filter('admin_body_class', array($this, 'add_admin_body_classes'));

        // Cria a tabela de pets na ativação
        register_activation_hook(__FILE__, array('APWP_Pet', 'create_table'));

        // Registra os endpoints AJAX
        add_action('admin_init', array($this, 'register_ajax_endpoints'));
    }

    /**
     * Enfileira assets apenas nas páginas do plugin
     *
     * @since    1.0.0
     */
    public function enqueue_admin_assets($hook) {
        // Obtém a página atual
        $current_screen = get_current_screen();
        
        // Verifica se estamos em uma página do plugin
        if (strpos($current_screen->id, $this->plugin_name) !== false) {
            $this->enqueue_styles();
            $this->enqueue_scripts();
        }
    }

    /**
     * Registra os estilos do admin
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            plugin_dir_url(__FILE__) . 'css/apwp-admin-bundle.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Registra os scripts do admin
     */
    public function enqueue_scripts() {
        // Scripts principais do WordPress
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');

        // Estilos do admin
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/apwp-admin.css', array(), $this->version, 'all');
        
        // Script principal do admin
        wp_enqueue_script(
            'apwp-admin-bundle',
            plugin_dir_url(__FILE__) . 'js/apwp-admin-bundle.js',
            array('jquery', 'wp-color-picker'),
            $this->version,
            true
        );

        // Localize script para configurações de exibição
        wp_localize_script('apwp-admin-bundle', 'prDisplaySettings', array(
            'previewUrl' => admin_url('admin-ajax.php'),
            'previewNonce' => wp_create_nonce('apwp_preview_grid')
        ));

        // Localize script para mensagens da interface
        wp_localize_script('apwp-admin-bundle', 'apwpI18n', array(
            'chooseLogoTitle' => __('Escolher Logo', 'amigopet-wp'),
            'useThisImage' => __('Usar esta imagem', 'amigopet-wp'),
            'settingsSaved' => __('Configurações salvas com sucesso!', 'amigopet-wp'),
            'settingsError' => __('Erro ao salvar configurações:', 'amigopet-wp'),
            'loading' => __('Carregando...', 'amigopet-wp'),
            'noTasksFound' => __('Nenhuma tarefa pendente.', 'amigopet-wp'),
            'error' => __('Erro:', 'amigopet-wp')
        ));
        
        // Scripts específicos da página de pets
        if (isset($_GET['page']) && $_GET['page'] === $this->plugin_name . '-pets') {
            wp_enqueue_script($this->plugin_name . '-pets', plugin_dir_url(__FILE__) . 'js/pets.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name . '-species', plugin_dir_url(__FILE__) . 'js/species.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name . '-breeds', plugin_dir_url(__FILE__) . 'js/breeds.js', array('jquery'), $this->version, false);
            
            // Localização para a API REST
            wp_localize_script($this->plugin_name . '-pets', 'wpApiSettings', array(
                'root' => esc_url_raw(rest_url()),
                'nonce' => wp_create_nonce('wp_rest')
            ));
        }
    }

    /**
     * Adiciona o menu do plugin no painel administrativo
     */
    public function add_plugin_admin_menu() {
        // Menu Principal - Dashboard
        add_menu_page(
            __('AmigoPet WP', 'amigopet-wp'),
            __('AmigoPet WP', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp',
            array($this, 'display_plugin_dashboard'),
            'none',
            25
        );

        // Submenu Dashboard (renomeia o item principal)
        add_submenu_page(
            'amigopet-wp',
            __('Dashboard', 'amigopet-wp'),
            __('Dashboard', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp',
            array($this, 'display_plugin_dashboard')
        );

        // Submenu Pets
        add_submenu_page(
            'amigopet-wp',
            __('Pets', 'amigopet-wp'),
            __('Pets', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-pets',
            array($this, 'display_pets_page')
        );

        // Submenu Adoções
        add_submenu_page(
            'amigopet-wp',
            __('Adoções', 'amigopet-wp'),
            __('Adoções', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-adoptions',
            array($this, 'display_adoptions_page')
        );

        // Submenu Adoções - Listar
        add_submenu_page(
            'amigopet-wp-adoptions',
            __('Listar Adoções', 'amigopet-wp'),
            __('Listar', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-adoptions',
            array($this, 'display_adoptions_page')
        );

        // Submenu Adoções - Adicionar
        add_submenu_page(
            'amigopet-wp-adoptions',
            __('Adicionar Adoção', 'amigopet-wp'),
            __('Adicionar', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-adoption-add',
            array($this, 'display_adoption_add_page')
        );

        // Submenu Adoções - Relatórios
        add_submenu_page(
            'amigopet-wp-adoptions',
            __('Relatórios de Adoções', 'amigopet-wp'),
            __('Relatórios', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-adoption-reports',
            array($this, 'display_adoption_reports_page')
        );

        // Submenu Adotantes
        add_submenu_page(
            'amigopet-wp',
            __('Adotantes', 'amigopet-wp'),
            __('Adotantes', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-adopters',
            array($this, 'display_adopters_page')
        );

        // Submenu Adotantes - Listar
        add_submenu_page(
            'amigopet-wp-adopters',
            __('Listar Adotantes', 'amigopet-wp'),
            __('Listar', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-adopters',
            array($this, 'display_adopters_page')
        );

        // Submenu Adotantes - Adicionar
        add_submenu_page(
            'amigopet-wp-adopters',
            __('Adicionar Adotante', 'amigopet-wp'),
            __('Adicionar', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-adopter-add',
            array($this, 'display_adopter_add_page')
        );

        // Submenu Adotantes - Relatórios
        add_submenu_page(
            'amigopet-wp-adopters',
            __('Relatórios de Adotantes', 'amigopet-wp'),
            __('Relatórios', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-adopter-reports',
            array($this, 'display_adopter_reports_page')
        );

        // Submenu Termos
        add_submenu_page(
            'amigopet-wp',
            __('Termos', 'amigopet-wp'),
            __('Termos', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-terms',
            array($this, 'display_terms_page')
        );

        // Submenu Configurações
        add_submenu_page(
            'amigopet-wp',
            __('Configurações', 'amigopet-wp'),
            __('Configurações', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-settings',
            array($this, 'display_settings_page')
        );

        // Submenu Ajuda
        add_submenu_page(
            'amigopet-wp',
            __('Ajuda', 'amigopet-wp'),
            __('Ajuda', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-help',
            array($this, 'display_help_page')
        );

        // Remover menus duplicados
        remove_submenu_page('amigopet-wp', 'amigopet-wp-adopter-reports');
        remove_submenu_page('amigopet-wp', 'amigopet-wp-adoption-reports');
    }

    /**
     * Renderiza a página de ajuda com abas
     */
    public function display_help_page() {
        // Verifica a aba atual
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'help';
        
        // Carrega o template da página de ajuda
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/help/apwp-admin-help.php';
    }

    /**
     * Renderiza a página de shortcuts que inclui ajuda e recursos rápidos
     */
    public function display_shortcuts_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/help/apwp-admin-shortcuts.php';
    }

    /**
     * Renderiza a página de termos
     */
    public function display_terms_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/terms/apwp-admin-terms.php';
    }

    /**
     * Adiciona opções de tela para a página de adotantes
     */
    public function add_adopters_screen_options() {
        // Adiciona filtros de status
        add_filter('views_toplevel_page_amigopet-wp-adopters', array($this, 'get_adopters_status_links'));

        // Adiciona barra de busca
        add_action('admin_notices', array($this, 'add_adopters_search_box'));

        // Sanitiza e valida parâmetros de busca
        $this->sanitize_adopters_search_params();
    }

    /**
     * Sanitiza e valida parâmetros de busca de adotantes
     */
    private function sanitize_adopters_search_params() {
        // Valida e sanitiza o parâmetro de status
        if (isset($_GET['status'])) {
            $allowed_statuses = ['all', 'active', 'inactive', 'pending_verification'];
            $_GET['status'] = in_array($_GET['status'], $allowed_statuses) 
                ? sanitize_text_field($_GET['status']) 
                : 'all';
        }

        // Valida e sanitiza o termo de busca
        if (isset($_GET['s'])) {
            $_GET['s'] = sanitize_text_field(trim($_GET['s']));
        }
    }

    /**
     * Gera links de filtro por status para adotantes
     */
    public function get_adopters_status_links() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_adopters';
        $current_status = $_GET['status'] ?? 'all';

        // Contagem de adotantes por status
        $status_counts = $wpdb->get_results(
            "SELECT status, COUNT(*) as count FROM $table_name GROUP BY status",
            ARRAY_A
        );

        $status_labels = [
            'all' => __('Todos', 'amigopet-wp'),
            'active' => __('Ativos', 'amigopet-wp'),
            'inactive' => __('Inativos', 'amigopet-wp'),
            'pending_verification' => __('Pendentes', 'amigopet-wp')
        ];

        $status_links = [];
        $total_count = 0;

        // Prepara links de status
        foreach ($status_labels as $status => $label) {
            $count = 0;
            if ($status === 'all') {
                // Soma total de adotantes
                foreach ($status_counts as $row) {
                    $total_count += $row['count'];
                }
                $count = $total_count;
            } else {
                // Busca contagem para status específico
                foreach ($status_counts as $row) {
                    if ($row['status'] === $status) {
                        $count = $row['count'];
                        break;
                    }
                }
            }

            $class = ($current_status === $status) ? 'current' : '';
            $status_url = add_query_arg('status', $status, admin_url('admin.php?page=amigopet-wp-adopters'));
            
            $status_links[$status] = sprintf(
                '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
                esc_url($status_url),
                $class,
                $label,
                $count
            );
        }

        return $status_links;
    }

    /**
     * Adiciona barra de busca na página de adotantes
     */
    public function add_adopters_search_box() {
        // Verifica se estamos na página de adotantes
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'amigopet-wp_page_amigopet-wp-adopters') {
            return;
        }

        $search_term = $_GET['s'] ?? '';
        ?>
        <div class="search-box">
            <form method="get" action="<?php echo admin_url('admin.php'); ?>" class="search-form">
                <input type="hidden" name="page" value="amigopet-wp-adopters" />
                <input type="search" name="s" value="<?php echo esc_attr($search_term); ?>" placeholder="<?php _e('Buscar adotantes por nome, email ou telefone', 'amigopet-wp'); ?>" />
                <?php submit_button(__('Buscar', 'amigopet-wp'), 'button', false, false, array('id' => 'search-submit')); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Display the dashboard page
     *
     * @since    1.0.0
     */
    public function display_plugin_dashboard() {
        include_once 'partials/dashboard/apwp-admin-dashboard.php';
    }

    /**
     * Renderiza a página de pets
     */
    public function display_pets_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/pet/apwp-admin-pets.php';
    }

    /**
     * Display the adoptions page
     *
     * @since    1.0.0
     */
    public function display_adoptions_page() {
        include_once 'partials/adoption/apwp-admin-adoptions.php';
    }

    /**
     * Display the adopters page
     *
     * @since    1.0.0
     */
    public function display_adopters_page() {
        include_once 'partials/adopter/apwp-admin-adopters.php';
    }

    /**
     * Display the contracts page
     *
     * @since    1.0.0
     */
    public function display_contracts_page() {
        include_once 'partials/term/apwp-admin-contracts.php';
    }

    /**
     * Display the organizations page
     *
     * @since    1.0.0
     */
    public function display_organizations_page() {
        include_once 'partials/organization/apwp-admin-organizations.php';
    }

    /**
     * Display the settings page
     *
     * @since    1.0.0
     */
    public function display_settings_page() {
        include_once 'partials/settings/apwp-admin-settings.php';
    }

    /**
     * Display the species page
     *
     * @since    1.0.0
     */
    public function display_species_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/pet/apwp-admin-species.php';
    }

    /**
     * Display the breeds page
     *
     * @since    1.0.0
     */
    public function display_breeds_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/pet/apwp-admin-breeds.php';
    }

    /**
     * Display the pets reports page
     *
     * @since    1.0.0
     */
    public function display_pets_reports_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/pet/apwp-admin-pets-reports.php';
    }

    /**
     * Renderiza a página de shortcodes
     *
     * @since    1.0.0
     */
    public function display_shortcodes_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/shortcode/apwp-admin-shortcodes.php';
    }

    /**
     * Renderiza a página de relatórios de adotantes
     */
    public function display_adopter_reports_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/adopter/apwp-admin-adopter-reports.php';
    }

    /**
     * Renderiza a página de relatórios de adoções
     */
    public function display_adoption_reports_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/adoption/apwp-admin-adoption-reports.php';
    }

    /**
     * Renderiza a página de tipos de termos
     */
    public function display_term_types_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/term/apwp-admin-term-types.php';
    }

    /**
     * Renderiza a página de templates de termos
     */
    public function display_term_templates_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/term/apwp-admin-term-templates.php';
    }

    /**
     * Renderiza a página de termos assinados
     */
    public function display_signed_terms_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/term/apwp-admin-signed-terms.php';
    }

    /**
     * Renderiza a página de assinatura de termo
     */
    public function display_sign_term_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/term/apwp-admin-sign-term.php';
    }

    /**
     * Renderiza a página de adição de adotante
     */
    public function display_adopter_add_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/adopter/apwp-admin-add-adopter.php';
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

    /**
     * Add icons to submenu items
     *
     * @since    1.0.0
     * @param    string    $parent_file    The parent file
     * @return   string    The parent file
     */
    public function add_submenu_icons($parent_file) {
        global $submenu;
        
        // Adiciona ícones aos submenus do menu principal
        if (isset($submenu[$this->plugin_name])) {
            foreach ($submenu[$this->plugin_name] as $key => $menu_item) {
                if (!is_array($menu_item) || count($menu_item) < 3) {
                    continue;
                }

                $icon = '';
                $menu_url = $menu_item[2];

                switch (true) {
                    case $menu_url === $this->plugin_name:
                        $icon = 'dashicons-dashboard';
                        break;

                    case strpos($menu_url, 'adoptions') !== false && strpos($menu_url, 'add-adoption') === false:
                        $icon = 'dashicons-heart';
                        break;

                    case strpos($menu_url, 'add-adoption') !== false:
                        $icon = 'dashicons-plus-alt';
                        break;

                    case strpos($menu_url, 'adopters') !== false:
                        $icon = 'dashicons-groups';
                        break;

                    case strpos($menu_url, 'contracts') !== false:
                        $icon = 'dashicons-media-document';
                        break;

                    case strpos($menu_url, 'organizations') !== false:
                        $icon = 'dashicons-building';
                        break;

                    case strpos($menu_url, 'settings') !== false:
                        $icon = 'dashicons-admin-settings';
                        break;

                    case strpos($menu_url, 'pets') !== false && strpos($menu_url, 'add-pet') === false && strpos($menu_url, 'reports') === false:
                        $icon = 'dashicons-pets';
                        break;

                    case strpos($menu_url, 'add-pet') !== false:
                        $icon = 'dashicons-plus-alt';
                        break;

                    case strpos($menu_url, 'species') !== false:
                        $icon = 'dashicons-category';
                        break;

                    case strpos($menu_url, 'breeds') !== false:
                        $icon = 'dashicons-tag';
                        break;

                    case strpos($menu_url, 'pets-reports') !== false:
                        $icon = 'dashicons-chart-bar';
                        break;

                    case strpos($menu_url, 'help') !== false:
                        $icon = 'dashicons-sos';
                        break;

                    case strpos($menu_url, 'shortcodes') !== false:
                        $icon = 'dashicons-editor-code';
                        break;

                    case strpos($menu_url, 'adopter-reports') !== false:
                        $icon = 'dashicons-chart-bar';
                        break;

                    case strpos($menu_url, 'adoption-reports') !== false:
                        $icon = 'dashicons-chart-bar';
                        break;

                    case strpos($menu_url, 'term-types') !== false:
                        $icon = 'dashicons-category';
                        break;

                    case strpos($menu_url, 'term-templates') !== false:
                        $icon = 'dashicons-tag';
                        break;

                    case strpos($menu_url, 'signed-terms') !== false:
                        $icon = 'dashicons-media-document';
                        break;

                    default:
                        $icon = 'dashicons-admin-generic';
                }

                if ($icon) {
                    $submenu[$this->plugin_name][$key][0] = sprintf(
                        '<span class="dashicons %s" style="font-size: 17px; line-height: 1.2;"></span> <span style="padding-left: 8px;">%s</span>',
                        esc_attr($icon),
                        $menu_item[0]
                    );
                }
            }
        }

        return $parent_file;
    }

    /**
     * Adiciona classes ao body da página admin
     *
     * @since    1.0.0
     */
    public function add_admin_body_classes($classes) {
        global $pagenow;
        
        // Adiciona a classe apenas se estivermos em uma página admin
        if (is_admin()) {
            $classes .= ' apwp-admin';
            
            // Se estivermos em uma página do plugin, adiciona classe específica
            $current_screen = get_current_screen();
            if (strpos($current_screen->id, $this->plugin_name) !== false) {
                $classes .= ' apwp-plugin-page';
            }
        }
        
        return $classes;
    }

    /**
     * Obtém a lista de adotantes com suporte a filtros, busca e paginação
     * 
     * @param int $per_page Número de itens por página
     * @param int $page_number Número da página atual
     * @return array Lista de adotantes
     */
    public function get_adopters_list($per_page = 20, $page_number = 1) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_adopters';

        // Inicializa parâmetros de consulta
        $where_clauses = [];
        $params = [];

        // Filtro de status
        $status = $_GET['status'] ?? 'all';
        if ($status && $status !== 'all') {
            $where_clauses[] = "status = %s";
            $params[] = $status;
        }

        // Filtro de busca
        $search = $_GET['s'] ?? '';
        if (!empty($search)) {
            $where_clauses[] = "(name LIKE %s OR email LIKE %s OR phone LIKE %s)";
            $search_param = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }

        // Monta cláusula WHERE
        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        // Calcula offset para paginação
        $offset = ($page_number - 1) * $per_page;

        // Prepara query com paginação
        $query = $wpdb->prepare(
            "SELECT * FROM $table_name $where_sql ORDER BY created_at DESC LIMIT %d OFFSET %d",
            array_merge($params, [$per_page, $offset])
        );

        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * Conta o número total de adotantes para paginação
     * 
     * @return int Total de adotantes
     */
    public function count_adopters() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_adopters';

        // Inicializa parâmetros de consulta
        $where_clauses = [];
        $params = [];

        // Filtro de status
        $status = $_GET['status'] ?? 'all';
        if ($status && $status !== 'all') {
            $where_clauses[] = "status = %s";
            $params[] = $status;
        }

        // Filtro de busca
        $search = $_GET['s'] ?? '';
        if (!empty($search)) {
            $where_clauses[] = "(name LIKE %s OR email LIKE %s OR phone LIKE %s)";
            $search_param = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }

        // Monta cláusula WHERE
        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        // Prepara query de contagem
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name $where_sql",
            $params
        );

        return $wpdb->get_var($query);
    }

    /**
     * Renderiza a paginação para a lista de adotantes
     * 
     * @param int $total_items Total de itens
     * @param int $per_page Itens por página
     */
    public function render_adopters_pagination($total_items, $per_page = 20) {
        $page_number = $_GET['paged'] ?? 1;
        $total_pages = ceil($total_items / $per_page);

        // Verifica se a paginação é necessária
        if ($total_pages <= 1) {
            return;
        }

        // Gera URL base para paginação
        $base_url = add_query_arg(
            array(
                'page' => 'amigopet-wp-adopters',
                'status' => $_GET['status'] ?? 'all',
                's' => $_GET['s'] ?? ''
            ), 
            admin_url('admin.php')
        );

        ?>
        <div class="tablenav-pages">
            <span class="displaying-num">
                <?php 
                printf(
                    __('%d itens', 'amigopet-wp'), 
                    number_format_i18n($total_items)
                ); 
                ?>
            </span>

            <?php if ($total_pages > 1): ?>
                <span class="pagination-links">
                    <?php
                    // Botão Primeira Página
                    if ($page_number > 1) {
                        $first_page_url = add_query_arg('paged', 1, $base_url);
                        printf(
                            '<a class="first-page button" href="%s"><span class="screen-reader-text">%s</span><span aria-hidden="true">«</span></a>',
                            esc_url($first_page_url),
                            __('Primeira página', 'amigopet-wp')
                        );
                    } else {
                        echo '<span class="first-page button disabled" aria-hidden="true">«</span>';
                    }

                    // Botão Página Anterior
                    if ($page_number > 1) {
                        $prev_page = max(1, $page_number - 1);
                        $prev_page_url = add_query_arg('paged', $prev_page, $base_url);
                        printf(
                            '<a class="prev-page button" href="%s"><span class="screen-reader-text">%s</span><span aria-hidden="true">‹</span></a>',
                            esc_url($prev_page_url),
                            __('Página anterior', 'amigopet-wp')
                        );
                    } else {
                        echo '<span class="prev-page button disabled" aria-hidden="true">‹</span>';
                    }

                    // Página atual
                    printf(
                        '<span class="paging-input">
                            <span class="screen-reader-text">%s</span>
                            <span class="current-page">%d</span> 
                            %s 
                            <span class="total-pages">%d</span>
                        </span>',
                        __('Página atual', 'amigopet-wp'),
                        $page_number,
                        __('de', 'amigopet-wp'),
                        $total_pages
                    );

                    // Botão Próxima Página
                    if ($page_number < $total_pages) {
                        $next_page = min($total_pages, $page_number + 1);
                        $next_page_url = add_query_arg('paged', $next_page, $base_url);
                        printf(
                            '<a class="next-page button" href="%s"><span class="screen-reader-text">%s</span><span aria-hidden="true">›</span></a>',
                            esc_url($next_page_url),
                            __('Próxima página', 'amigopet-wp')
                        );
                    } else {
                        echo '<span class="next-page button disabled" aria-hidden="true">›</span>';
                    }

                    // Botão Última Página
                    if ($page_number < $total_pages) {
                        $last_page_url = add_query_arg('paged', $total_pages, $base_url);
                        printf(
                            '<a class="last-page button" href="%s"><span class="screen-reader-text">%s</span><span aria-hidden="true">»</span></a>',
                            esc_url($last_page_url),
                            __('Última página', 'amigopet-wp')
                        );
                    } else {
                        echo '<span class="last-page button disabled" aria-hidden="true">»</span>';
                    }
                    ?>
                </span>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Retorna as estatísticas do dashboard via AJAX
     */
    public function get_dashboard_stats() {
        try {
            // Verificação de segurança
            check_ajax_referer('apwp_dashboard_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => __('Permissão negada.', 'amigopet-wp')]);
            }

            global $wpdb;
            
            // Verifica se as tabelas existem
            $tables = [
                $wpdb->prefix . 'apwp_pets',
                $wpdb->prefix . 'apwp_adoptions',
                $wpdb->prefix . 'apwp_adopters',
                $wpdb->prefix . 'apwp_terms'
            ];

            foreach ($tables as $table) {
                if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
                    throw new Exception(sprintf(__('Tabela %s não encontrada.', 'amigopet-wp'), $table));
                }
            }

            // Estatísticas de pets
            $pets_table = $wpdb->prefix . 'apwp_pets';
            $pets_stats = [
                'total' => $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i", $pets_table)),
                'available' => $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM %i WHERE status = %s",
                    $pets_table,
                    'available'
                ))
            ];

            if ($wpdb->last_error) {
                throw new Exception($wpdb->last_error);
            }

            // Estatísticas de adoções
            $adoptions_table = $wpdb->prefix . 'apwp_adoptions';
            $adoptions_stats = [
                'total' => $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i", $adoptions_table)),
                'in_progress' => $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM %i WHERE status = %s",
                    $adoptions_table,
                    'in_progress'
                ))
            ];

            if ($wpdb->last_error) {
                throw new Exception($wpdb->last_error);
            }

            // Estatísticas de adotantes
            $adopters_table = $wpdb->prefix . 'apwp_adopters';
            $adopters_stats = [
                'total' => $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i", $adopters_table)),
                'with_adoptions' => $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT adopter_id) FROM %i",
                    $adoptions_table
                ))
            ];

            if ($wpdb->last_error) {
                throw new Exception($wpdb->last_error);
            }

            // Estatísticas de termos
            $terms_table = $wpdb->prefix . 'apwp_terms';
            $terms_stats = [
                'total' => $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i", $terms_table)),
                'signed' => $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM %i WHERE status = %s",
                    $terms_table,
                    'signed'
                ))
            ];

            if ($wpdb->last_error) {
                throw new Exception($wpdb->last_error);
            }

            // Pets recentes
            $recent_pets = $wpdb->get_results("
                SELECT id, name, species, status 
                FROM {$wpdb->prefix}apwp_pets 
                ORDER BY created_at DESC 
                LIMIT 5
            ");
            
            // Adoções recentes
            $recent_adoptions = $wpdb->get_results("
                SELECT a.id, a.pet_id, a.adopter_id, a.status, a.created_at,
                       p.name as pet_name, 
                       ad.name as adopter_name
                FROM {$wpdb->prefix}apwp_adoptions a
                LEFT JOIN {$wpdb->prefix}apwp_pets p ON a.pet_id = p.id
                LEFT JOIN {$wpdb->prefix}apwp_adopters ad ON a.adopter_id = ad.id
                ORDER BY a.created_at DESC
                LIMIT 5
            ");
            
            $response = array(
                'pets' => $pets_stats,
                'adoptions' => $adoptions_stats,
                'adopters' => $adopters_stats,
                'terms' => $terms_stats,
                'recent_pets' => $recent_pets,
                'recent_adoptions' => $recent_adoptions
            );
            
            wp_send_json_success($response);
        } catch (Exception $e) {
            error_log('[AmigoPetWp] Error retrieving dashboard stats: ' . $e->getMessage());
            wp_send_json_error([
                'message' => __('Erro ao buscar estatísticas:', 'amigopet-wp') . ' ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Retorna as atividades pendentes para o dashboard
     * 
     * @since 1.0.0
     * @return void
     */
    public function get_pending_activities() {
        try {
            check_ajax_referer('apwp_dashboard_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => __('Permissão negada.', 'amigopet-wp')]);
            }

            global $wpdb;
            
            // Verifica se as tabelas existem
            $tables = [
                $wpdb->prefix . 'apwp_pets',
                $wpdb->prefix . 'apwp_adoptions',
                $wpdb->prefix . 'apwp_terms'
            ];

            foreach ($tables as $table) {
                if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
                    throw new Exception(sprintf(__('Tabela %s não encontrada.', 'amigopet-wp'), $table));
                }
            }

            // Adoções em andamento
            $adoptions_table = $wpdb->prefix . 'apwp_adoptions';
            $pets_table = $wpdb->prefix . 'apwp_pets';
            $adopters_table = $wpdb->prefix . 'apwp_adopters';
            
            $pending_adoptions = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    a.id,
                    a.status,
                    a.created_at,
                    p.name as pet_name,
                    ad.name as adopter_name
                FROM %i a
                LEFT JOIN %i p ON a.pet_id = p.id
                LEFT JOIN %i ad ON a.adopter_id = ad.id
                WHERE a.status = %s
                ORDER BY a.created_at DESC
                LIMIT %d
            ", $adoptions_table, $pets_table, $adopters_table, 'in_progress', 5));

            if ($wpdb->last_error) {
                throw new Exception($wpdb->last_error);
            }

            // Termos aguardando assinatura
            $terms_table = $wpdb->prefix . 'apwp_terms';
            
            $pending_terms = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    t.id,
                    t.title,
                    t.created_at,
                    ad.name as adopter_name
                FROM %i t
                LEFT JOIN %i ad ON t.adopter_id = ad.id
                WHERE t.status = %s
                ORDER BY t.created_at DESC
                LIMIT %d
            ", $terms_table, $adopters_table, 'pending', 5));

            if ($wpdb->last_error) {
                throw new Exception($wpdb->last_error);
            }

            // Pets aguardando revisão
            $pending_pets = $wpdb->get_results($wpdb->prepare("
                SELECT 
                    id,
                    name,
                    species,
                    created_at
                FROM %i
                WHERE status = %s
                ORDER BY created_at DESC
                LIMIT %d
            ", $pets_table, 'pending_review', 5));

            if ($wpdb->last_error) {
                throw new Exception($wpdb->last_error);
            }

            // Log para debug
            error_log(sprintf(
                '[AmigoPetWp] Pending activities retrieved. Adoptions: %d, Terms: %d, Pets: %d',
                count($pending_adoptions),
                count($pending_terms),
                count($pending_pets)
            ));

            wp_send_json_success([
                'pending_adoptions' => $pending_adoptions,
                'pending_terms' => $pending_terms,
                'pending_pets' => $pending_pets
            ]);

        } catch (Exception $e) {
            error_log('[AmigoPetWp] Error retrieving pending activities: ' . $e->getMessage());
            wp_send_json_error([
                'message' => __('Erro ao buscar atividades pendentes:', 'amigopet-wp') . ' ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Registra os endpoints AJAX
     */
    public function register_ajax_endpoints() {
        add_action('wp_ajax_get_dashboard_stats', array($this, 'get_dashboard_stats'));
        add_action('wp_ajax_save_apwp_settings', array($this, 'ajax_save_settings'));
        add_action('wp_ajax_add_species', array($this, 'ajax_add_species'));
        add_action('wp_ajax_add_breed', array($this, 'ajax_add_breed'));
        add_action('wp_ajax_get_species', array($this, 'ajax_get_species'));
        add_action('wp_ajax_get_breeds', array($this, 'ajax_get_breeds'));
        add_action('wp_ajax_view_term', array($this, 'ajax_view_term'));
        add_action('wp_ajax_download_term', array($this, 'ajax_download_term'));
        add_action('wp_ajax_email_term', array($this, 'ajax_email_term'));
        add_action('wp_ajax_delete_term_type', array($this, 'ajax_delete_term_type'));
        add_action('wp_ajax_delete_term_template', array($this, 'ajax_delete_term_template'));
        add_action('wp_ajax_get_pending_tasks', array($this, 'ajax_get_pending_tasks'));
        add_action('wp_ajax_get_pending_activities', array($this, 'get_pending_activities'));
    }

    /**
     * Endpoint AJAX para buscar estatísticas do dashboard
     */
    public function ajax_get_dashboard_stats() {
        check_ajax_referer('apwp_dashboard_nonce', 'nonce');

        global $wpdb;

        // Busca estatísticas dos pets
        $pets_total = wp_count_posts('animal')->publish;
        $pets_aguardando = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}posts p
                LEFT JOIN {$wpdb->prefix}postmeta m ON p.ID = m.post_id
                WHERE p.post_type = %s 
                AND p.post_status = 'publish'
                AND m.meta_key = 'status'
                AND m.meta_value = 'aguardando'",
                'animal'
            )
        );

        // Busca estatísticas das adoções
        $adocoes_total = wp_count_posts('adocao')->publish;
        $adocoes_em_andamento = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}posts p
                LEFT JOIN {$wpdb->prefix}postmeta m ON p.ID = m.post_id
                WHERE p.post_type = %s 
                AND p.post_status = 'publish'
                AND m.meta_key = 'status'
                AND m.meta_value = 'em_andamento'",
                'adocao'
            )
        );

        // Busca estatísticas dos adotantes
        $adotantes_total = wp_count_posts('adotante')->publish;
        $adotantes_com_adocoes = $wpdb->get_var(
            "SELECT COUNT(DISTINCT pm.meta_value) 
            FROM {$wpdb->prefix}posts p
            JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id
            WHERE p.post_type = 'adocao'
            AND p.post_status = 'publish'
            AND pm.meta_key = 'adotante_id'"
        );

        // Busca estatísticas dos termos
        $termos_total = wp_count_posts('termo')->publish;
        $termos_assinados = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}posts p
                LEFT JOIN {$wpdb->prefix}postmeta m ON p.ID = m.post_id
                WHERE p.post_type = %s 
                AND p.post_status = 'publish'
                AND m.meta_key = 'status'
                AND m.meta_value = 'assinado'",
                'termo'
            )
        );

        // Pets recentes
        $recent_pets = $wpdb->get_results("
            SELECT id, name, species, status 
            FROM {$wpdb->prefix}apwp_pets 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        
        // Adoções recentes
        $recent_adoptions = $wpdb->get_results("
            SELECT a.id, p.name as pet_name, ad.name as adopter_name, a.status
            FROM {$wpdb->prefix}apwp_adoptions a
            JOIN {$wpdb->prefix}apwp_pets p ON a.pet_id = p.id
            JOIN {$wpdb->prefix}apwp_adopters ad ON a.adopter_id = ad.id
            ORDER BY a.created_at DESC
            LIMIT 5
        ");
        
        $response = array(
            'pets' => $pets_total,
            'adoptions' => $adocoes_total,
            'adopters' => $adotantes_total,
            'terms' => $termos_total,
            'recent_pets' => $recent_pets,
            'recent_adoptions' => $recent_adoptions
        );
        
        wp_send_json_success($response);
    }

    /**
     * Adiciona uma nova espécie via AJAX
     */
    public function ajax_add_species() {
        check_ajax_referer('apwp_add_species', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'amigopet-wp')));
        }

        parse_str($_POST['formData'], $form_data);
        
        $name = sanitize_text_field($form_data['species_name']);
        $description = sanitize_textarea_field($form_data['species_description']);

        $species = new APWP_Species();
        $result = $species->add($name, $description);

        if ($result === false) {
            wp_send_json_error(array('message' => __('Erro ao adicionar espécie.', 'amigopet-wp')));
        }

        wp_send_json_success(array('message' => __('Espécie adicionada com sucesso!', 'amigopet-wp')));
    }

    /**
     * Adiciona uma nova raça via AJAX
     */
    public function ajax_add_breed() {
        check_ajax_referer('apwp_add_breed', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'amigopet-wp')));
        }

        parse_str($_POST['formData'], $form_data);
        
        $name = sanitize_text_field($form_data['breed_name']);
        $species_id = intval($form_data['breed_species']);
        $description = sanitize_textarea_field($form_data['breed_description']);

        $breed = new APWP_Breed();
        $result = $breed->add($name, $species_id, $description);

        if ($result === false) {
            wp_send_json_error(array('message' => __('Erro ao adicionar raça.', 'amigopet-wp')));
        }

        wp_send_json_success(array('message' => __('Raça adicionada com sucesso!', 'amigopet-wp')));
    }

    /**
     * Retorna a lista de espécies via AJAX
     */
    public function ajax_get_species() {
        check_ajax_referer('apwp_get_species', 'nonce');

        $species = new APWP_Species();
        $list = $species->list(array(
            'orderby' => 'name',
            'order' => 'ASC'
        ));

        wp_send_json_success($list);
    }

    /**
     * Retorna a lista de raças por espécie via AJAX
     */
    public function ajax_get_breeds() {
        check_ajax_referer('apwp_get_breeds', 'nonce');

        $species_id = intval($_POST['species']);

        $breed = new APWP_Breed();
        $list = $breed->list(array(
            'species_id' => $species_id,
            'orderby' => 'name',
            'order' => 'ASC'
        ));

        wp_send_json_success($list);
    }

    /**
     * Visualiza um termo assinado
     */
    public function ajax_view_term() {
        check_ajax_referer('apwp_view_term', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'amigopet-wp')));
        }

        $term_id = intval($_POST['term_id']);
        $term = new APWP_Term();
        $term_data = $term->get($term_id);

        if (!$term_data) {
            wp_send_json_error(array('message' => __('Termo não encontrado.', 'amigopet-wp')));
        }

        wp_send_json_success(array(
            'content' => $term_data['content'],
            'signature' => $term_data['signature'],
            'signature_date' => $term_data['signature_date']
        ));
    }

    /**
     * Faz o download de um termo em PDF
     */
    public function ajax_download_term() {
        check_ajax_referer('apwp_download_term', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'amigopet-wp')));
        }

        $term_id = intval($_POST['term_id']);
        $term = new APWP_Term();
        
        if (!$term->get($term_id)) {
            wp_send_json_error(array('message' => __('Termo não encontrado.', 'amigopet-wp')));
        }

        $pdf = $term->generate_pdf();
        if (!$pdf) {
            wp_send_json_error(array('message' => __('Erro ao gerar PDF.', 'amigopet-wp')));
        }

        wp_send_json_success(array(
            'pdf' => base64_encode($pdf),
            'filename' => sprintf('termo-adocao-%d.pdf', $term_id)
        ));
    }

    /**
     * Envia um termo por email
     */
    public function ajax_email_term() {
        check_ajax_referer('apwp_email_term', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'amigopet-wp')));
        }

        $term_id = intval($_POST['term_id']);
        $email = sanitize_email($_POST['email']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = wp_kses_post($_POST['message']);

        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Email inválido.', 'amigopet-wp')));
        }

        $term = new APWP_Term();
        if (!$term->get($term_id)) {
            wp_send_json_error(array('message' => __('Termo não encontrado.', 'amigopet-wp')));
        }

        if (!$term->send_email($email, $subject, $message)) {
            wp_send_json_error(array('message' => __('Erro ao enviar email.', 'amigopet-wp')));
        }

        wp_send_json_success(array('message' => __('Email enviado com sucesso!', 'amigopet-wp')));
    }

    /**
     * Exclui um tipo de termo
     */
    public function ajax_delete_term_type() {
        check_ajax_referer('apwp_delete_term_type', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'amigopet-wp')));
        }

        $type_id = intval($_POST['type_id']);
        $type = new APWP_Term_Type();
        
        if (!$type->delete($type_id)) {
            wp_send_json_error(array('message' => __('Erro ao excluir tipo de termo.', 'amigopet-wp')));
        }

        wp_send_json_success(array('message' => __('Tipo de termo excluído com sucesso!', 'amigopet-wp')));
    }

    /**
     * Exclui um template de termo
     */
    public function ajax_delete_term_template() {
        check_ajax_referer('apwp_delete_term_template', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'amigopet-wp')));
        }

        $template_id = intval($_POST['template_id']);
        $template = new APWP_Term_Template();
        
        if (!$template->delete($template_id)) {
            wp_send_json_error(array('message' => __('Erro ao excluir template de termo.', 'amigopet-wp')));
        }

        wp_send_json_success(array('message' => __('Template de termo excluído com sucesso!', 'amigopet-wp')));
    }

    /**
     * Busca tarefas pendentes para o dashboard
     */
    public function get_pending_tasks() {
        check_ajax_referer('apwp_dashboard_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada.', 'amigopet-wp')]);
        }

        $adoption = new APWP_Adoption();
        $adopter = new APWP_Adopter();

        // Busca adoções pendentes
        $pending_adoptions = $adoption->list([
            'status' => 'pending',
            'limit' => 5
        ]);

        // Busca verificações pendentes
        $pending_verifications = [];
        
        // Verifica documentos pendentes
        if (get_option('apwp_require_document_verification', true)) {
            $docs_pending = $adopter->list([
                'status' => 'active',
                'document_verified' => false,
                'limit' => 5
            ]);
            foreach ($docs_pending as $adopter) {
                $pending_verifications[] = [
                    'type' => 'document',
                    'adopter_id' => $adopter->id,
                    'adopter_name' => $adopter->name,
                    'message' => sprintf(
                        __('Verificar documentos de %s', 'amigopet-wp'),
                        $adopter->name
                    )
                ];
            }
        }

        // Verifica endereços pendentes
        if (get_option('apwp_require_address_verification', true)) {
            $address_pending = $adopter->list([
                'status' => 'active',
                'address_verified' => false,
                'limit' => 5
            ]);
            foreach ($address_pending as $adopter) {
                $pending_verifications[] = [
                    'type' => 'address',
                    'adopter_id' => $adopter->id,
                    'adopter_name' => $adopter->name,
                    'message' => sprintf(
                        __('Verificar endereço de %s', 'amigopet-wp'),
                        $adopter->name
                    )
                ];
            }
        }

        // Busca acompanhamentos pendentes
        $follow_up_days = explode(',', get_option('apwp_adoption_follow_up_days', '30,60,90'));
        $pending_followups = [];
        
        foreach ($follow_up_days as $days) {
            $followups = $adoption->get_pending_followups(intval($days));
            foreach ($followups as $followup) {
                $pending_followups[] = [
                    'adoption_id' => $followup->id,
                    'pet_name' => $followup->pet_name,
                    'adopter_name' => $followup->adopter_name,
                    'days' => $days,
                    'message' => sprintf(
                        __('Acompanhamento de %d dias - %s (adotado por %s)', 'amigopet-wp'),
                        $days,
                        $followup->pet_name,
                        $followup->adopter_name
                    )
                ];
            }
        }

        wp_send_json_success([
            'adoptions' => $pending_adoptions,
            'verifications' => $pending_verifications,
            'followups' => $pending_followups
        ]);
    }
}
