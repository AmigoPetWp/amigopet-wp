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

        // Sempre carrega o CSS do ícone do menu
        wp_enqueue_style(
            $this->plugin_name . '-menu',
            plugin_dir_url(__FILE__) . 'css/amigopet-wp-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        // Registra e carrega o CSS admin personalizado
        wp_enqueue_style(
            $this->plugin_name . '-admin-styles', 
            plugin_dir_url(__FILE__) . 'css/amigopet-wp-admin.css', 
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
     * Adiciona o menu do plugin no painel administrativo
     */
    public function add_plugin_admin_menu() {
        global $menu;

        // Menu principal
        add_menu_page(
            __('AmigoPet WP', 'amigopet-wp'),
            __('AmigoPet WP', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp',
            array($this, 'display_plugin_dashboard'),
            'none', // Usando 'none' pois definimos o ícone via CSS
            25
        );

        // Submenu Dashboard
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

        // Submenu Adicionar Pet
        add_submenu_page(
            'amigopet-wp',
            __('Adicionar Pet', 'amigopet-wp'),
            __('Adicionar Pet', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-add-pet',
            array($this, 'display_add_pet_page')
        );

        // Submenu Espécies
        add_submenu_page(
            'amigopet-wp',
            __('Espécies', 'amigopet-wp'),
            __('Espécies', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-species',
            array($this, 'display_species_page')
        );

        // Submenu Raças
        add_submenu_page(
            'amigopet-wp',
            __('Raças', 'amigopet-wp'),
            __('Raças', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-breeds',
            array($this, 'display_breeds_page')
        );

        // Submenu Relatórios de Pets
        add_submenu_page(
            'amigopet-wp',
            __('Relatórios de Pets', 'amigopet-wp'),
            __('Relatórios de Pets', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-pets-reports',
            array($this, 'display_pets_reports_page')
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

        // Submenu Adicionar Adoção
        add_submenu_page(
            'amigopet-wp',
            __('Adicionar Adoção', 'amigopet-wp'),
            __('Adicionar Adoção', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-add-adoption',
            array($this, 'display_add_adoption_page')
        );

        // Submenu Adotantes com suporte a filtros
        $adopters_hook = add_submenu_page(
            'amigopet-wp',
            __('Adotantes', 'amigopet-wp'),
            __('Adotantes', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-adopters',
            array($this, 'display_adopters_page')
        );

        // Adiciona ação para adicionar filtros de status na página de adotantes
        add_action("load-{$adopters_hook}", array($this, 'add_adopters_screen_options'));

        // Submenu Contratos
        add_submenu_page(
            'amigopet-wp',
            __('Contratos', 'amigopet-wp'),
            __('Contratos', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-contracts',
            array($this, 'display_contracts_page')
        );

        // Submenu Organizações
        add_submenu_page(
            'amigopet-wp',
            __('Organizações', 'amigopet-wp'),
            __('Organizações', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-organizations',
            array($this, 'display_organizations_page')
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

        // Submenu Shortcodes
        add_submenu_page(
            'amigopet-wp',
            __('Shortcodes', 'amigopet-wp'),
            __('Shortcodes', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-shortcodes',
            array($this, 'display_shortcodes_page')
        );

        // Submenu Ajuda (último item)
        add_submenu_page(
            'amigopet-wp',
            __('Ajuda', 'amigopet-wp'),
            __('Ajuda', 'amigopet-wp'),
            'manage_options',
            'amigopet-wp-help',
            array($this, 'display_help_page')
        );
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
        include_once 'partials/apwp-admin-dashboard.php';
    }

    /**
     * Renderiza a página de pets
     */
    public function display_pets_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/apwp-admin-pets.php';
    }

    /**
     * Renderiza a página de adicionar pet
     */
    public function display_add_pet_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/apwp-admin-add-pet.php';
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
     * Display the add adoption page
     *
     * @since    1.0.0
     */
    public function display_add_adoption_page() {
        include_once 'partials/apwp-admin-add-adoption.php';
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
     * Display the species page
     *
     * @since    1.0.0
     */
    public function display_species_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/apwp-admin-species.php';
    }

    /**
     * Display the breeds page
     *
     * @since    1.0.0
     */
    public function display_breeds_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/apwp-admin-breeds.php';
    }

    /**
     * Display the pets reports page
     *
     * @since    1.0.0
     */
    public function display_pets_reports_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/apwp-admin-pets-reports.php';
    }

    /**
     * Renderiza a página de shortcodes
     *
     * @since    1.0.0
     */
    public function display_shortcodes_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/apwp-admin-shortcodes.php';
    }

    /**
     * Renderiza a página de ajuda
     *
     * @since    1.0.0
     */
    public function display_help_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/apwp-admin-help.php';
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
}
