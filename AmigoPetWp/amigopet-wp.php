<?php
/**
 * Plugin Name:       AmigoPet WP
 * Plugin URI:        https://github.com/wendelmax/amigopet-wp
 * Description:       Sistema completo de gestão de adoção de animais para ONGs e abrigos.
 * Version:           2.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Jackson Sa
 * Author URI:        https://github.com/wendelmax
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       amigopet-wp
 * Domain Path:       /languages
 * Plugin Icon:       assets/images/icon-128x128.png
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define('AMIGOPET_WP_VERSION', '2.0.0');
define('AMIGOPET_WP_PLUGIN_NAME', 'amigopet-wp');
define('AMIGOPET_WP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AMIGOPET_WP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Autoloader para as classes do plugin
 */
spl_autoload_register(function ($class) {
    // Log para debug
    error_log("Tentando carregar classe: " . $class);

    // Prefixo base do namespace do plugin
    $prefix = 'AmigoPetWp\\';
    $base_dir = plugin_dir_path(__FILE__) . 'AmigoPet/';

    // Verifica se a classe usa o prefixo do namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        error_log("Classe não pertence ao nosso namespace: " . $class);
        return;
    }

    // Pega o caminho relativo da classe
    $relative_class = substr($class, $len);

    // Substitui o namespace pelo caminho do diretório e \ por /
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Log do caminho do arquivo
    error_log("Tentando carregar arquivo: " . $file);

    // Se o arquivo existir, carrega-o
    if (file_exists($file)) {
        error_log("Arquivo encontrado: " . $file);
        require $file;
    } else {
        error_log("Arquivo não encontrado: " . $file);
    }
});

/**
 * Classe principal do plugin
 */
class AmigoPetWp {
    private static $instance = null;
    private $database;
    private $admin_controllers = [];
    private $public_controllers = [];

    private function __construct() {
        // Registra os hooks de ativação e desativação
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // Inicializa o banco de dados
        $this->database = \AmigoPetWp\Domain\Database\Database::getInstance();

        // Inicializa os controllers administrativos
        if (is_admin()) {
            $this->initAdminControllers();
        }

        // Inicializa os controllers públicos
        $this->initPublicControllers();

        // Registra os hooks
        add_action('init', [$this, 'init']);
        add_action('plugins_loaded', [$this, 'loadTextdomain']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueuePublicAssets']);
    }

    private function initAdminControllers() {
        // Controllers Admin
        $this->admin_controllers = [
            new \AmigoPetWp\Controllers\Admin\AdminPetSpeciesController(),
            new \AmigoPetWp\Controllers\Admin\AdminPetBreedController(),
            new \AmigoPetWp\Controllers\Admin\AdminTermTypeController(),
            new \AmigoPetWp\Controllers\Admin\AdminSignedTermController(),
            new \AmigoPetWp\Controllers\Admin\AdminAdoptionPaymentController(),
            new \AmigoPetWp\Controllers\Admin\DashboardController()
        ];

        // Registra os hooks de cada controller
        foreach ($this->admin_controllers as $controller) {
            if (method_exists($controller, 'registerHooks')) {
                $controller->registerHooks();
            }
        }
    }

    private function initPublicControllers() {
        // Controllers Public
        $this->public_controllers = [
            // Adicione os controllers públicos aqui
        ];

        // Registra os hooks de cada controller
        foreach ($this->public_controllers as $controller) {
            if (method_exists($controller, 'registerHooks')) {
                $controller->registerHooks();
            }
        }
    }

    public function enqueueAdminAssets() {
        // CSS Admin
        wp_enqueue_style(
            'amigopet-admin',
            AMIGOPET_WP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            AMIGOPET_WP_VERSION
        );

        // JavaScript Admin
        wp_enqueue_script(
            'amigopet-admin',
            AMIGOPET_WP_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'jquery-ui-datepicker'],
            AMIGOPET_WP_VERSION,
            true
        );

        // Localize script
        wp_localize_script('amigopet-admin', 'amigopetAdmin', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amigopet-admin-nonce')
        ]);
    }

    public function enqueuePublicAssets() {
        // CSS Public
        wp_enqueue_style(
            'amigopet-public',
            AMIGOPET_WP_PLUGIN_URL . 'assets/css/public.css',
            [],
            AMIGOPET_WP_VERSION
        );

        // JavaScript Public
        wp_enqueue_script(
            'amigopet-public',
            AMIGOPET_WP_PLUGIN_URL . 'assets/js/public.js',
            ['jquery'],
            AMIGOPET_WP_VERSION,
            true
        );
    }
    }

    /**
     * Singleton
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Ativação do plugin
     */
    public function activate(): void {
        // Cria as tabelas do banco de dados
        $migration = new \AmigoPetWp\Domain\Database\Migrations\CreateTables();
        $migration->up();

        // Registra os papéis e capacidades
        \AmigoPetWp\Domain\Security\RoleManager::activate();

        // Insere dados iniciais nas tabelas
        $seeds = [
            // Dados base
            new \AmigoPetWp\Domain\Database\Migrations\SeedTermTypes(),
            new \AmigoPetWp\Domain\Database\Migrations\SeedSpecies(),
            new \AmigoPetWp\Domain\Database\Migrations\SeedBreeds(),
            new \AmigoPetWp\Domain\Database\Migrations\SeedTerms(),
            // Dados da organização
            new \AmigoPetWp\Domain\Database\Migrations\SeedOrganizations(),
            new \AmigoPetWp\Domain\Database\Migrations\SeedVolunteers()
        ];

        foreach ($seeds as $seed) {
            try {
                $seed->up();
            } catch (\Exception $e) {
                error_log('Erro ao executar seed ' . get_class($seed) . ': ' . $e->getMessage());
            }
        }

        // Adiciona as configurações padrão
        $this->addDefaultSettings();

        // Limpa o cache de rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Adiciona as configurações padrão do plugin
     */
    private function addDefaultSettings(): void {
        // Configurações da organização
        add_option('apwp_organization_name', '');
        add_option('apwp_organization_email', '');
        add_option('apwp_organization_phone', '');

        // Configurações de API
        add_option('apwp_google_maps_key', '');

        // Configurações de workflow
        add_option('apwp_adoption_workflow', [
            'require_home_visit' => true,
            'require_adoption_fee' => true,
            'require_terms_acceptance' => true,
            'require_adopter_documents' => true
        ]);

        add_option('apwp_notification_workflow', [
            'notify_new_adoption' => true,
            'notify_adoption_status' => true,
            'notify_new_donation' => true,
            'notify_new_volunteer' => true
        ]);

        // Configurações de email
        add_option('apwp_email_settings', [
            'from_name' => get_bloginfo('name'),
            'from_email' => get_bloginfo('admin_email'),
            'adoption_approved_template' => 'Parabéns! Sua adoção foi aprovada.',
            'adoption_rejected_template' => 'Infelizmente sua adoção não foi aprovada.',
            'donation_received_template' => 'Obrigado pela sua doação!',
            'volunteer_application_template' => 'Obrigado por se voluntariar!'
        ]);

        // Termos e condições
        add_option('apwp_terms_settings', [
            'adoption_terms' => '',
            'donation_terms' => '',
            'volunteer_terms' => ''
        ]);
    }

    /**
     * Desativação do plugin
     */
    public function deactivate(): void {
        // Remove os papéis e capacidades
        \AmigoPetWp\Domain\Security\RoleManager::deactivate();

        // Limpa o cache de rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Inicialização do plugin
     */
    public function init(): void {
        // Registra os post types
        $this->registerPostTypes();
        error_log('Registrando post types');

        // Registra as taxonomias
        $this->registerTaxonomies();
    }

    /**
     * Carrega o arquivo de tradução
     */
    public function loadTextdomain(): void {
        load_plugin_textdomain(
            'amigopet-wp',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    /**
     * Registra os post types
     */
    private function registerPostTypes(): void {
        // Post type para termos
        register_post_type('apwp_term', [
            'labels' => [
                'name'               => __('Termos', 'amigopet-wp'),
                'singular_name'      => __('Termo', 'amigopet-wp'),
                'add_new'            => __('Adicionar Novo', 'amigopet-wp'),
                'add_new_item'       => __('Adicionar Novo Termo', 'amigopet-wp'),
                'edit_item'          => __('Editar Termo', 'amigopet-wp'),
                'new_item'           => __('Novo Termo', 'amigopet-wp'),
                'view_item'          => __('Ver Termo', 'amigopet-wp'),
                'search_items'       => __('Buscar Termos', 'amigopet-wp'),
                'not_found'          => __('Nenhum termo encontrado', 'amigopet-wp'),
                'not_found_in_trash' => __('Nenhum termo encontrado na lixeira', 'amigopet-wp'),
                'menu_name'          => __('Termos', 'amigopet-wp')
            ],
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'capability_type'     => 'apwp_term',
            'map_meta_cap'        => true,
            'supports'            => ['title', 'editor'],
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-media-document',
            'show_in_rest'        => true,
            'rest_base'           => 'terms',
            'capabilities'        => [
                'edit_post'          => 'edit_apwp_term',
                'read_post'          => 'read_apwp_term',
                'delete_post'        => 'delete_apwp_term',
                'edit_posts'         => 'edit_apwp_terms',
                'edit_others_posts'  => 'edit_others_apwp_terms',
                'publish_posts'      => 'publish_apwp_terms',
                'read_private_posts' => 'read_private_apwp_terms'
            ]
        ]);
        
        // Post type para pets
        register_post_type('apwp_pet', [
            'labels' => [
                'name'               => __('Pets', 'amigopet-wp'),
                'singular_name'      => __('Pet', 'amigopet-wp'),
                'add_new'            => __('Adicionar Novo', 'amigopet-wp'),
                'add_new_item'       => __('Adicionar Novo Pet', 'amigopet-wp'),
                'edit_item'          => __('Editar Pet', 'amigopet-wp'),
                'new_item'           => __('Novo Pet', 'amigopet-wp'),
                'view_item'          => __('Ver Pet', 'amigopet-wp'),
                'search_items'       => __('Buscar Pets', 'amigopet-wp'),
                'not_found'          => __('Nenhum pet encontrado', 'amigopet-wp'),
                'not_found_in_trash' => __('Nenhum pet encontrado na lixeira', 'amigopet-wp'),
                'menu_name'          => __('Pets', 'amigopet-wp')
            ],
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => 'amigopet-wp',
            'capability_type'     => 'apwp_pet',
            'map_meta_cap'        => true,
            'supports'            => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-pets',
            'show_in_rest'        => true,
            'rest_base'           => 'pets',
            'capabilities'        => [
                'edit_post'          => 'edit_apwp_pet',
                'read_post'          => 'read_apwp_pet',
                'delete_post'        => 'delete_apwp_pet',
                'edit_posts'         => 'edit_apwp_pets',
                'edit_others_posts'  => 'edit_others_apwp_pets',
                'publish_posts'      => 'publish_apwp_pets',
                'read_private_posts' => 'read_private_apwp_pets'
            ]
        ]);

        // Post type para eventos
        register_post_type('apwp_event', [
            'labels' => [
                'name' => __('Eventos', 'amigopet-wp'),
                'singular_name' => __('Evento', 'amigopet-wp')
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'menu_icon' => 'dashicons-calendar-alt',
            'show_in_rest' => true
        ]);
    }

    /**
     * Registra as taxonomias
     */
    private function registerTaxonomies(): void {
        // Taxonomia para espécies
        register_taxonomy('apwp_species', ['apwp_pet'], [
            'labels' => [
                'name' => __('Espécies', 'amigopet-wp'),
                'singular_name' => __('Espécie', 'amigopet-wp')
            ],
            'hierarchical' => true,
            'show_in_rest' => true
        ]);

        // Taxonomia para raças
        register_taxonomy('apwp_breed', ['apwp_pet'], [
            'labels' => [
                'name' => __('Raças', 'amigopet-wp'),
                'singular_name' => __('Raça', 'amigopet-wp')
            ],
            'hierarchical' => true,
            'show_in_rest' => true
        ]);

        // Taxonomia para tipos de eventos
        register_taxonomy('apwp_event_type', ['apwp_event'], [
            'labels' => [
                'name' => __('Tipos de Evento', 'amigopet-wp'),
                'singular_name' => __('Tipo de Evento', 'amigopet-wp')
            ],
            'hierarchical' => true,
            'show_in_rest' => true
        ]);
    }
}

// Inicializa o plugin
AmigoPetWp::getInstance();
